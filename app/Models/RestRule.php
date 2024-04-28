<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestRule extends Model
{
    use HasFactory;

    protected $table = 'rest_rules';
    protected $fillable = [
        'id',
        'name',
        'discount_type',
        'discount_value',
        'discount_status',
        'variant_id'
    ];
}
