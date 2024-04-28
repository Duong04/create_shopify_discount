<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestDiscount extends Model
{
    use HasFactory;

    protected $table = 'rest_discounts';
    protected $fillable = [
        'id',
        'price',
        'compare_at_price',
        'variant_id',
        'rule_id',
    ];
}
