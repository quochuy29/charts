<?php

namespace App\Services;

use App\Models\Equipment;

class EquipmentService extends BaseService
{
    public function getEquipmentTree()
    {
        // Lấy tất cả các Node gốc (Level 1 - Line) và load đệ quy con của nó
        // Giả sử item_type = 1 là Root (Line)
        return Equipment::where('item_type', 1)
            ->where('is_visibility', true)
            ->orderBy('display_order')
            ->with(['children' => function ($query) {
                $query->where('is_visibility', true)
                      ->orderBy('display_order')
                      ->with(['children' => function ($q) {
                          $q->where('is_visibility', true)
                            ->orderBy('display_order')
                            ->with(['children' => function ($k) {
                                // Level 4 (Equipment)
                                $k->where('is_visibility', true)
                                  ->orderBy('display_order');
                            }]);
                      }]);
            }])
            ->get();
    }
}