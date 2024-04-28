<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraphqlRule extends Model
{
    use HasFactory;

    protected $table = 'graphql_rules';
    protected $fillable = [
        'id',
        'name',
        'discount_type',
        'discount_value',
        'discount_status',
        'variant_id'
    ];
}
