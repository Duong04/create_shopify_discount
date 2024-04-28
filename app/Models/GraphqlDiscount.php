<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraphqlDiscount extends Model
{
    use HasFactory;

    protected $table = 'graphql_discounts';
    protected $fillable = [
        'id',
        'price',
        'compare_at_price',
        'variant_id',
        'rule_id',
    ];
}
