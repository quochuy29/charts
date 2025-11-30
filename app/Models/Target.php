<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    protected $fillable = [
        'node_id',
        'target_value',
        'target_type', // daily, period
        'effective_start_date',
        'effective_end_date',
    ];

    protected $casts = [
        'target_value' => 'float',
        'effective_start_date' => 'date',
        'effective_end_date' => 'date',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}