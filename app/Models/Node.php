<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'type', // factory, line, shop, equipment
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- RELATIONSHIPS ---

    // Node cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }

    // Node con trực tiếp (có sắp xếp)
    public function children(): HasMany
    {
        return $this->hasMany(Node::class, 'parent_id')->orderBy('display_order');
    }

    // Đệ quy lấy toàn bộ cây con (Dùng cho API Sidebar)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Công thức tính toán riêng của Node
    public function formula(): HasOne
    {
        return $this->hasOne(NodeFormula::class);
    }

    // Các mục tiêu (Targets) đã thiết lập
    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    // Lấy target hiện tại đang hiệu lực
    public function currentTarget()
    {
        return $this->hasOne(Target::class)
            ->whereDate('effective_start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_end_date')
                      ->orWhereDate('effective_end_date', '>=', now());
            })
            ->latest('effective_start_date');
    }

    // Dữ liệu thô (Raw Data)
    public function rawMeasurements(): HasMany
    {
        return $this->hasMany(EnergyRawMeasurement::class);
    }

    // Dữ liệu tổng hợp (Materialized View) - Dùng để vẽ biểu đồ
    public function dailySnapshots(): HasMany
    {
        return $this->hasMany(EnergyDailySnapshot::class);
    }
}