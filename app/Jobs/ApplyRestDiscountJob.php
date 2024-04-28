<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RestShopifyService;
use App\Models\RestRule;
use App\Models\RestDiscount;

class ApplyRestDiscountJob implements ShouldQueue
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
    public function handle(RestShopifyService $client): void
    {
        $client = $client->getClient();
        $getVariants = $client->get("variants/$this->variant.json");
        $dataVariants = json_decode($getVariants->getBody()->getContents(), true)['variant'];
        $price = $dataVariants['price'];
        $compareAtPrice = isset($dataVariants['compare_at_price']) ? $dataVariants['compare_at_price'] : null;
        $requestData = [];
        $existingDiscount = RestDiscount::join('rest_rules', 'rest_discounts.rule_id', '=', 'rest_rules.id')
                                          ->where('rest_discounts.variant_id', $this->variant)
                                          ->first();
        if (!$existingDiscount || $existingDiscount->discount_status == 'off') {
            $discount = RestRule::where('id', $this->id)->first();
            if ($discount['discount_status'] == 'on') {
                RestDiscount::updateOrCreate(
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
                    'variant' => [
                        'price' => $discountedPrice,
                        'compare_at_price' => $price, 
                    ]
                ];
            }
        }else {
            $requestData = [
                'variant' => [
                    'price' => $dataVariants['price'],
                    'compare_at_price' => $dataVariants['compare_at_price'], 
                ]
            ];
        }

        $response = $client->put("variants/{$this->variant}.json", [
            'json' => $requestData,
        ]);
    }
}
