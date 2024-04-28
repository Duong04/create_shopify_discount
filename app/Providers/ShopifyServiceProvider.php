<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Signifly\Shopify\Shopify;

class ShopifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton('Shopify', function ($app) {
            return new Shopify(
                env('SHOPIFY_ACCESS_TOKEN'),
                env('SHOPIFY_DOMAIN'),
                env('SHOPIFY_API_VERSION')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
