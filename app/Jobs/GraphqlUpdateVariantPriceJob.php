<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GraphqlShopifyService;

class GraphqlUpdateVariantPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $status;
    protected $rule;
    public function __construct($status, $rule)
    {
        $this->status = $status;
        $this->rule = $rule->toArray();
    }

    /**
     * Execute the job.
     */
    public function handle(GraphqlShopifyService $client): void
    {
        $client = $client->getClient();
        $requestData = [];
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
            if ($this->status == 'off') {
                $requestData = [
                    'id' => $this->rule['variant_id'],
                    'price' => $this->rule['price'],
                    'compareAtPrice' => $this->rule['compare_at_price']
                ];
            }else {
                if ($this->rule['discount_type'] === 'percentage') {
                    $discountedPrice = round($this->rule['price'] - ($this->rule['price'] * intval($this->rule['discount_value']) / 100));
                } elseif ($this->rule['discount_type'] === 'fixed_amount') {
                    $discountedPrice = round($this->rule['price'] - intval($this->rule['discount_value']));
                }

                $requestData = [
                    'id' => $this->rule['variant_id'],
                    'price' => $discountedPrice,
                    'compareAtPrice' => $this->rule['price']
                ];
            }

            $variables = [
                "input" => $requestData,
            ];
            $response = $client->query(["query" => $query, "variables" => $variables]);
    }
}
