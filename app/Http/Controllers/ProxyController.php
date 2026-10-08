<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\ShopStorage;

class ProxyController extends Controller
{
    public function handle(Request $request)
    {
        if (!$this->validateSignature($request->all(), $request->get('signature'))) {
            return response()->json(['error' => 'Invalid app proxy signature'], 403);
        }

        $shop = $request->get('shop');
        $variantIds = explode(',', $request->get('variant_ids', ''));
        
        if (!$shop || empty($variantIds)) {
            return response()->json(['error' => 'Missing shop or variant_ids'], 400);
        }

        $accessToken = $this->getShopToken($shop);
        if (!$accessToken) {
            return response()->json(['error' => 'Access token not found'], 403);
        }

        try {
            $locationsResp = Http::withHeaders([
                'X-Shopify-Access-Token' => $accessToken
            ])->get("https://{$shop}/admin/api/2024-04/locations.json");

            if (!$locationsResp->successful()) {
                return response()->json(['error' => 'Failed to fetch locations'], 500);
            }

            // Location name to country mapping
            $countryMapping = [
                'NL' => 'Netherlands',
                'BROOK STREET' => 'UK',
                'UK' => 'UK',
                'US' => 'USA',
                'NY' => 'USA',
                'DE' => 'Germany',
            ];

            // Clean and map location name + country
            $locationMap = collect($locationsResp['locations'] ?? [])
                ->mapWithKeys(function ($loc) use ($countryMapping) {
                    $cleanName = preg_replace('/^\d+\s*\|\s*/', '', $loc['name']);
                    $country = 'Unknown';

                    foreach ($countryMapping as $keyword => $mappedCountry) {
                        if (stripos($cleanName, $keyword) !== false) {
                            $country = $mappedCountry;
                            break;
                        }
                    }

                    return [$loc['id'] => [
                        'name' => $cleanName,
                        'country' => $country
                    ]];
                })
                ->toArray();

            // Track available locations for each item
            $itemAvailability = [];
            $cartItems = [];

            foreach ($variantIds as $variantId) {
                if (empty(trim($variantId))) {
                    continue;
                }

                $variantResp = Http::withHeaders([
                    'X-Shopify-Access-Token' => $accessToken
                ])->get("https://{$shop}/admin/api/2024-04/variants/{$variantId}.json");

                if (!$variantResp->successful()) {
                    continue;
                }

                $variant = $variantResp['variant'];

                // Fetch product
                $productResp = Http::withHeaders([
                    'X-Shopify-Access-Token' => $accessToken
                ])->get("https://{$shop}/admin/api/2024-04/products/{$variant['product_id']}.json");

                if (!$productResp->successful()) {
                    continue;
                }

                $productTitle = $productResp['product']['title'] ?? 'Product';
                $rawTitle = $variant['title'];
                $exploded = explode(' / ', $rawTitle);
                $size = trim($exploded[0]);
                $sku = $variant['sku'];

                // Fetch inventory levels for ALL locations
                $inventoryResp = Http::withHeaders([
                    'X-Shopify-Access-Token' => $accessToken
                ])->get("https://{$shop}/admin/api/2024-04/inventory_levels.json", [
                    'inventory_item_ids' => $variant['inventory_item_id']
                ]);

                if (!$inventoryResp->successful()) {
                    continue;
                }

                $levels = $inventoryResp['inventory_levels'];
                $availableLocations = [];

                // Find ALL locations where this item has stock > 0
                foreach ($levels as $level) {
                    if ($level['available'] > 0) {
                        $availableLocations[] = $level['location_id'];
                    }
                }

                // Store item information
                $cartItems[] = [
                    'variant_id' => $variantId,
                    'name' => "{$productTitle} - {$variant['title']} / {$variant['sku']}",
                    'sku' => $variant['sku'] ?? '',
                    'size' => $size,
                    'available_locations' => $availableLocations
                ];

                // Track which locations can fulfill this item
                $itemAvailability[$variantId] = $availableLocations;
            }

            // Find locations that can fulfill ALL items
            $fulfillableLocations = $this->findFulfillableLocations($itemAvailability);
            
            // Generate conflicts data for frontend
            $conflicts = [];
            if (empty($fulfillableLocations)) {
                // No single location can fulfill all items - generate conflict data
                foreach ($cartItems as $item) {
                    // For conflict resolution, show primary location for each item
                    $primaryLocationId = !empty($item['available_locations']) ? $item['available_locations'][0] : null;
                    
                    if ($primaryLocationId && isset($locationMap[$primaryLocationId])) {
                        $locationInfo = $locationMap[$primaryLocationId];
                        $conflicts[] = [
                            'name' => $item['name'],
                            'location' => $locationInfo['name'],
                            'shipping_country' => $locationInfo['country'],
                            'sku' => $item['sku'],
                            'size' => $item['size']
                        ];
                    }
                }
            }

            return response()->json([
                'allow_checkout' => !empty($fulfillableLocations),
                'fulfillable_locations' => $fulfillableLocations,
                'conflicts' => $conflicts
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Find locations that can fulfill ALL items in the cart
     */
    private function findFulfillableLocations($itemAvailability)
    {
        if (empty($itemAvailability)) {
            return [];
        }

        // Start with locations from the first item
        $firstItem = array_values($itemAvailability)[0];
        $fulfillableLocations = $firstItem;

        // Find intersection of all available locations
        foreach ($itemAvailability as $variantId => $locations) {
            $fulfillableLocations = array_intersect($fulfillableLocations, $locations);
        }

        return array_values($fulfillableLocations);
    }

    /**
     * Validate signatures from Shopify
     */
    private function validateSignature($params, $signature)
    {
        if (!$signature) {
            return false;
        }

        $shared_secret = config('shopify.api_secret');
        $params = request()->all();
        
        if (!isset($params['logged_in_customer_id'])) {
            $params['logged_in_customer_id'] = "";
        }

        $params = array_diff_key($params, array('signature' => ''));
        ksort($params);
        $params = str_replace("%2F", "/", http_build_query($params));
        $params = str_replace("&", "", $params);
        $params = str_replace("%2C", ",", $params);
        $computed_hmac = hash_hmac('sha256', $params, $shared_secret);
        
        return hash_equals($signature, $computed_hmac);
    }

    /**
     * Get access token for shop from JSON file
     */
    private function getShopToken($shop)
    {
        try {
            $accessToken = ShopStorage::get($shop);
            if (!$accessToken) {
                return null;
            }

            return $accessToken;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}