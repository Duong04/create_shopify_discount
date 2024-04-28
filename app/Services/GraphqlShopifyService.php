<?php

namespace App\Services;

use Shopify\Clients\Graphql;
use App\Models\User;

class GraphqlShopifyService
{
    protected $client;

    public function __construct()
    {
        $user = User::where('status', 1)->get();
        $this->client = new Graphql(
            $user[0]['name'],
            $user[0]['password'],
        );
    }

    public function getClient()
    {
        return $this->client;
    }
}