<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\ShopStorage;

class InjectScriptTagToShop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shopDomain;
    protected $accessToken;

    /**
     * Create a new job instance.
     */
    public function __construct($shopDomain, $accessToken = null)
    {
        $this->shopDomain = $shopDomain;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $accessToken = ShopStorage::get($this->shopDomain);
        
        if (!$accessToken) {

            throw new \Exception("Access token not found for shop: {$this->shopDomain}");
        }

        $scripts = ['/cart-check.js'];

        foreach ($scripts as $scriptPath) {
            $scriptUrl = rtrim(env('APP_URL'), '/') . $scriptPath;
            
            try {
                // Remove existing ScriptTag with same src (optional cleanup)
                $this->removeExistingScriptTag($accessToken, $scriptUrl);

                // Inject new ScriptTag
                $this->injectNewScriptTag($accessToken, $scriptUrl);

            } catch (\Exception $e) {

                throw $e; // Will be retried by queue system
            }
        }

    }

    /**
     * Remove existing script tag with same URL
     */
    private function removeExistingScriptTag($accessToken, $scriptUrl)
    {
        try {
            $existing = Http::withHeaders([
                'X-Shopify-Access-Token' => $accessToken
            ])->get("https://{$this->shopDomain}/admin/api/2024-04/script_tags.json");

            if ($existing->failed()) {

                return;
            }

            $scriptTags = $existing->json('script_tags', []);
            $removedCount = 0;

            foreach ($scriptTags as $tag) {
                if (isset($tag['src']) && $tag['src'] === $scriptUrl) {
                    $deleteResponse = Http::withHeaders([
                        'X-Shopify-Access-Token' => $accessToken
                    ])->delete("https://{$this->shopDomain}/admin/api/2024-04/script_tags/{$tag['id']}.json");

                    if ($deleteResponse->successful()) {
                        $removedCount++;
                    }
                }
            }


        } catch (\Exception $e) {
            // Don't throw here - continue with injection even if cleanup fails
        }
    }

    /**
     * Inject new script tag
     */
    private function injectNewScriptTag($accessToken, $scriptUrl)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json'
        ])->post("https://{$this->shopDomain}/admin/api/2024-04/script_tags.json", [
            'script_tag' => [
                'event' => 'onload',
                'src' => $scriptUrl,
            ]
        ]);

        if ($response->successful()) {
            $scriptTag = $response->json('script_tag', []);
            
        } else {
            $errorMessage = "Failed to inject ScriptTag";
            $responseBody = $response->json();
            

            // Provide more specific error message based on response
            if ($response->status() === 401) {
                throw new \Exception("Authentication failed - invalid access token for shop: {$this->shopDomain}");
            } elseif ($response->status() === 403) {
                throw new \Exception("Insufficient permissions to create script tags for shop: {$this->shopDomain}");
            } elseif ($response->status() === 422) {
                $errors = $responseBody['errors'] ?? 'Validation failed';
                throw new \Exception("Validation error creating script tag: " . json_encode($errors));
            } else {
                throw new \Exception("{$errorMessage} (HTTP {$response->status()})");
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        //
    }
}