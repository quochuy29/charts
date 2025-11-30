<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionFactor extends Model
{
    protected $fillable = [
        'factor_type', // unit_price, co2_factor
        'value',
        'effective_date',
    ];

    protected $casts = [
        'value' => 'float',
        'effective_date' => 'date',
    ];
}