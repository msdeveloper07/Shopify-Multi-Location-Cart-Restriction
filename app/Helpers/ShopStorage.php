<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShopStorage
{
    private static $dataFile = 'private/shops.json';
    
    /**
     * Get access token for a specific shop
     */
    public static function get($shopDomain)
    {
        try {
            $data = self::readData();
            $shop = $data[$shopDomain] ?? null;
            
            if (!$shop) {
                return null;
            }
            
            // Decrypt the access token
            return decrypt($shop['access_token']);
        } catch (\Exception $e) {
            Log::error('ShopStorage::get failed', [
                'shop' => $shopDomain,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Store or update shop access token
     */
    public static function set($shopDomain, $accessToken, $settings = null)
    {
        try {
            $data = self::readData();
            
            $data[$shopDomain] = [
                'access_token' => encrypt($accessToken),
                'settings' => $settings,
                'installed_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];
            
            return self::writeData($data);
        } catch (\Exception $e) {
            Log::error('ShopStorage::set failed', [
                'shop' => $shopDomain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Delete shop from storage
     */
    public static function delete($shopDomain)
    {
        try {
            $data = self::readData();
            
            if (isset($data[$shopDomain])) {
                unset($data[$shopDomain]);
                return self::writeData($data);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('ShopStorage::delete failed', [
                'shop' => $shopDomain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Check if shop exists
     */
    public static function exists($shopDomain)
    {
        try {
            $data = self::readData();
            return isset($data[$shopDomain]);
        } catch (\Exception $e) {
            Log::error('ShopStorage::exists failed', [
                'shop' => $shopDomain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get all shops
     */
    public static function getAllShops()
    {
        try {
            $data = self::readData();
            $shops = [];
            
            foreach ($data as $domain => $shopData) {
                $shops[$domain] = decrypt($shopData['access_token']);
            }
            
            return $shops;
        } catch (\Exception $e) {
            Log::error('ShopStorage::getAllShops failed', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Get shop data (for dashboard/admin purposes)
     */
    public static function getShop($shopDomain)
    {
        try {
            $data = self::readData();
            $shop = $data[$shopDomain] ?? null;
            
            if (!$shop) {
                return null;
            }
            
            // Return as object for compatibility with existing code
            return (object) [
                'shop_domain' => $shopDomain,
                'access_token' => decrypt($shop['access_token']),
                'settings' => $shop['settings'],
                'installed_at' => $shop['installed_at'],
                'updated_at' => $shop['updated_at'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('ShopStorage::getShop failed', [
                'shop' => $shopDomain,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get shop settings
     */
    public static function getSettings($shopDomain, $key = null, $default = null)
    {
        try {
            $data = self::readData();
            $shop = $data[$shopDomain] ?? null;
            
            if (!$shop) {
                return $default;
            }
            
            $settings = $shop['settings'] ?? [];
            
            if ($key === null) {
                return $settings;
            }
            
            return data_get($settings, $key, $default);
        } catch (\Exception $e) {
            Log::error('ShopStorage::getSettings failed', [
                'shop' => $shopDomain,
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }
    
    /**
     * Update shop settings
     */
    public static function updateSettings($shopDomain, $key, $value = null)
    {
        try {
            $data = self::readData();
            
            if (!isset($data[$shopDomain])) {
                return false;
            }
            
            $settings = $data[$shopDomain]['settings'] ?? [];
            
            if (is_array($key)) {
                // Update multiple settings
                $settings = array_merge($settings, $key);
            } else {
                // Update single setting
                data_set($settings, $key, $value);
            }
            
            $data[$shopDomain]['settings'] = $settings;
            $data[$shopDomain]['updated_at'] = now()->toISOString();
            
            return self::writeData($data);
        } catch (\Exception $e) {
            Log::error('ShopStorage::updateSettings failed', [
                'shop' => $shopDomain,
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Read data from JSON file
     */
    private static function readData()
    {
        try {
            if (!Storage::exists(self::$dataFile)) {
                return [];
            }
            
            $content = Storage::get(self::$dataFile);
            
            if ($content === null) {
                return [];
            }
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error in ShopStorage::readData', [
                    'error' => json_last_error_msg()
                ]);
                return [];
            }
            
            return $data ?? [];
        } catch (\Exception $e) {
            Log::error('ShopStorage::readData failed', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Write data to JSON file
     */
    private static function writeData($data)
    {
        try {
            $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            
            if ($jsonContent === false) {
                Log::error('JSON encode error in ShopStorage::writeData', [
                    'error' => json_last_error_msg()
                ]);
                return false;
            }
            
            // Use Laravel's Storage facade for atomic writes
            return Storage::put(self::$dataFile, $jsonContent);
        } catch (\Exception $e) {
            Log::error('ShopStorage::writeData failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get file info for debugging
     */
    public static function getFileInfo()
    {
        $fullPath = storage_path('app/' . self::$dataFile);
        
        $info = [
            'file' => self::$dataFile,
            'full_path' => $fullPath,
            'exists' => Storage::exists(self::$dataFile),
            'readable' => is_readable($fullPath),
            'writable' => is_writable(dirname($fullPath)),
            'size' => Storage::exists(self::$dataFile) ? Storage::size(self::$dataFile) : 0,
            'modified' => Storage::exists(self::$dataFile) ? 
                date('Y-m-d H:i:s', Storage::lastModified(self::$dataFile)) : null,
        ];
        
        if ($info['exists']) {
            $data = self::readData();
            $info['shop_count'] = count($data);
            $info['shops'] = array_keys($data);
        }
        
        return $info;
    }
    
    /**
     * Get the full file path (for debugging)
     */
    public static function getFilePath()
    {
        return storage_path('app/' . self::$dataFile);
    }
}