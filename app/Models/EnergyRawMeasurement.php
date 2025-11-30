<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyRawMeasurement extends Model
{
    use HasFactory;

    protected $table = 'energy_raw_measurements';

    protected $fillable = [
        'node_id',
        'consumption_kwh',
        'measured_at',
    ];

    protected $casts = [
        'consumption_kwh' => 'float',
        'measured_at' => 'datetime',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}