<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\User;

class RestShopifyService
{
    protected $client;

    public function __construct()
    {
        $user = User::where('status', 1)->get();
        $this->client = new Client([
            'base_uri' => "https://".$user[0]['name']."/admin/api/".ENV('SHOPIFY_API_VERSION')."/",
            'headers' => [
                'X-Shopify-Access-Token' => $user[0]['password'],
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }
}