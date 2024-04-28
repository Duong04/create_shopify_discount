<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GraphqlShopifyService;
use App\Models\GraphqlDiscount;
use App\Models\GraphqlRule;

class ApplyGraphqlDiscountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $variant;
    protected $id;
    public function __construct($variant, $id)
    {
        $this->variant = $variant;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(GraphqlShopifyService $client): void
    {
        $client = $client->getClient();

        $query = <<<QUERY
                query {
                    productVariant(id: "$this->variant") {
                        id
                        price
                        compareAtPrice
                    }
                }
            QUERY;
            $response = $client->query(['query' => $query]);
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true)['data'];
            $dataVariants = $responseData['productVariant'];

            $price = $dataVariants['price'];
            $compareAtPrice = isset($dataVariants['compareAtPrice']) ? $dataVariants['compareAtPrice'] : null;
            $requestData = []; 
            $existingDiscount = GraphqlDiscount::join('graphql_rules', 'graphql_discounts.rule_id', '=', 'graphql_rules.id')
                                              ->where('graphql_discounts.variant_id', $this->variant)
                                              ->first();
            if (!$existingDiscount || $existingDiscount->discount_status == 'off') {
                $discount = GraphqlRule::where('id', $this->id)->first();
                if ($discount['discount_status'] == 'on') {
                    GraphqlDiscount::updateOrCreate(
                        ['variant_id' => $this->variant],
                        [
                            'price' => $price,
                            'compare_at_price' => $compareAtPrice,
                            'variant_id' => $this->variant,
                            'rule_id' => $this->id
                        ]
                    );
        
                    if ($discount['discount_type'] === 'percentage') {
                        $discountedPrice = round($price - ($price * intval($discount['discount_value']) / 100));
                    } elseif ($discount['discount_type'] === 'fixed_amount') {
                        $discountedPrice = round($price - intval($discount['discount_value']));
                    }
        
                    $requestData = [
                        'id' => $this->variant,
                        'price' => $discountedPrice,
                        'compareAtPrice' => $price, 
                    ];
                }
            }else {
                $requestData = [
                    'id' => $this->variant,
                    'price' => intval($dataVariants['price']),
                    'compareAtPrice' => $dataVariants['compareAtPrice']
                ];
            }
            $query = <<<QUERY
                mutation productVariantUpdate(\$input: ProductVariantInput!) {
                    productVariantUpdate(input: \$input) {
                        productVariant {
                            id
                            title
                            inventoryPolicy
                            inventoryQuantity
                            price
                            compareAtPrice
                        }
                        userErrors {
                            field
                            message
                        }
                    }
                }
            QUERY;
            $variables = [
                "input" => $requestData,
            ];
              
            $response = $client->query(["query" => $query, "variables" => $variables]);
    }
}
