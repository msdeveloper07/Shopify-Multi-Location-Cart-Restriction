<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\ProxyController;
use App\Helpers\ShopStorage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Artisan;

Route::delete('/shop/{shop}', function ($shopDomain, Request $request) {
    $deleted = ShopStorage::delete($shopDomain);

    if ($deleted) {
        return redirect('/')->with('status', 'Shop data deleted successfully!');
    }

    return redirect('/')->with('error', 'Shop not found.');
})->name('shop.delete');

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');

    Artisan::call('route:clear');
    Artisan::call('route:cache');

    Artisan::call('view:clear');
    Artisan::call('view:cache');

    return 'Config, cache, route, and view caches cleared!!!';
});

Route::get('/cart-check.js', function () {
    return response()->view('js.cart-check')->header('Content-Type', 'application/javascript');
});

// OAuth start
Route::get('/shopify/install', [ShopifyController::class, 'install'])->name('shopify.install');
Route::get('/shopify/callback', [ShopifyController::class, 'callback'])->name('shopify.callback');

// Uninstall webhook (optional)
Route::post('/shopify/uninstall', [ShopifyController::class, 'uninstall'])->name('shopify.uninstall');

// App proxy handler
Route::get('/browns-proxy-handler', [ProxyController::class, 'handle'])->name('shopify.proxy');

Route::get('/', function (Request $request) {
    $shop = request()->get('shop'); // Get ?shop= param if passed

    if (!$shop) {
        return view('welcome');
    }

    try {
        $shopModel = ShopStorage::getShop($shop);
        if ($shopModel && $shopModel->access_token) {
            return view('shopify.dashboard', [
                'shop' => $shop,
                'installed_at' => $shopModel->installed_at
            ]);
        } else {
            return view('shopify.not_installed', ['shop' => $shop]);
        }
        
    } catch (\Exception $e) {
        
        return view('shopify.not_installed', ['shop' => $shop]);
    }
})->name('shopify.home');