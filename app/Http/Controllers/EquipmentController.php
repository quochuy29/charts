<?php

namespace App\Http\Controllers;

use App\Services\EquipmentService;
use Illuminate\Http\JsonResponse;

class EquipmentController extends Controller
{
    protected $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    public function getTree(): JsonResponse
    {
        $tree = $this->equipmentService->getEquipmentTree();
        return response()->json($tree);
    }
}