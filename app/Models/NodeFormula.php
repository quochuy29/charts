<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeFormula extends Model
{
    protected $fillable = [
        'node_id',
        'formula_expression', // VD: "TAG_01 + TAG_02"
        'variables_map',      // JSON mapping biến
    ];

    protected $casts = [
        'variables_map' => 'array', // Tự động convert JSON <-> Array
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}