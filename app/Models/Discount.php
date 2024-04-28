<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discount_rules';
    protected $fillable = [
        'id',
        'name_rule',
        'compare_at_price',
        'price',
        'rule_status',
        'variant_id'
    ];
}
