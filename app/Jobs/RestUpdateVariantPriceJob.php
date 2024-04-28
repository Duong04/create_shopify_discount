<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RestShopifyService;

class RestUpdateVariantPriceJob implements ShouldQueue
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
    public function handle(RestShopifyService $client): void
    {
        $client = $client->getClient();
        $requestData = [];
        if ($this->status == 'off') {
            $requestData = [
                'variant' => [
                    'price' => $this->rule['price'],
                    'compare_at_price' => $this->rule['compare_at_price']
                ]
            ];
        }else {
            if ($this->rule['discount_type'] === 'percentage') {
                $discountedPrice = round($this->rule['price'] - ($this->rule['price'] * intval($this->rule['discount_value']) / 100));
            } elseif ($this->rule['discount_type'] === 'fixed_amount') {
                $discountedPrice = round($this->rule['price'] - intval($this->rule['discount_value']));
            }
    
            $requestData = [
                'variant' => [
                    'price' => $discountedPrice,
                    'compare_at_price' => $this->rule['price']
                ]
            ];
        }
        
        $response = $client->put("variants/{$this->rule['variant_id']}.json", [
            'json' => $requestData,
        ]);
    }
}
