<?php
use Shopify\Context;
use Shopify\Auth\FileSessionStorage;
return [

    'credentials' => [

        /*
         * The API access token from the private app.
         */
        'access_token' => env('SHOPIFY_ACCESS_TOKEN', '7977add4c9de3cd8f6a07740b9b294c5'),

        /*
         * The shopify domain for your shop.
         */
        'domain' => env('SHOPIFY_DOMAIN', ''),

        /*
         * The shopify api version.
         */
        'api_version' => env('SHOPIFY_API_VERSION', '2021-01'),

    ],

    'webhooks' => [

        /*
         * The webhook secret provider to use.
         */
        'secret_provider' => \Signifly\Shopify\Webhooks\ConfigSecretProvider::class,

        /*
         * The shopify webhook secret.
         */
        'secret' => env('SHOPIFY_WEBHOOK_SECRET'),

    ],

    'exceptions' => [

        /*
         * Whether to include the validation errors in the exception message.
         */
        'include_validation_errors' => false,

    ],

    Context::initialize(
        apiKey: $_ENV['SHOPIFY_API_KEY'],
        apiSecretKey: $_ENV['SHOPIFY_API_SECRET'],
        scopes: ['read_products', 'write_products', 'read_orders'],
        hostName: $_ENV['SHOPIFY_DOMAIN'],
        sessionStorage: new FileSessionStorage('/tmp/php_sessions'),
        apiVersion: '2024-04',
        isEmbeddedApp: true,
        isPrivateApp: false,
    )
];
