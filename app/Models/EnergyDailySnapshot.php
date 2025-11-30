<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyDailySnapshot extends Model
{
    // Trỏ vào tên Materialized View
    protected $table = 'energy_daily_snapshots';

    // View không có khóa chính tự tăng
    public $incrementing = false;
    
    // View không hỗ trợ tự động update timestamps
    public $timestamps = false;

    // Các thuộc tính có thể convert type
    protected $casts = [
        'date' => 'date',
        'total_kwh' => 'float',
        'total_cost' => 'float',
        'total_co2' => 'float',
        'last_calculated_at' => 'datetime',
    ];

    // Quan hệ ngược về Node
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}