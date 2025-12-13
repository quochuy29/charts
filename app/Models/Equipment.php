<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'mst_equipment';

    protected $fillable = [
        'code', 'name', 'level', 'parent_id', 
        'item_type', 'graph_type', 'calculation_formula', 
        'display_order', 'is_visibility'
    ];

    // Quan hệ đệ quy để lấy con
    public function children()
    {
        return $this->hasMany(Equipment::class, 'parent_id')
                    ->orderBy('display_order', 'asc');
    }
}