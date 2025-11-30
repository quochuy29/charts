<?php

namespace App\Http\Controllers;

use App\Repositories\NodeRepository;
use App\Repositories\EnergyDataRepository;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    protected $nodeRepo;
    protected $energyRepo;

    public function __construct(NodeRepository $nodeRepo, EnergyDataRepository $energyRepo) {
        $this->nodeRepo = $nodeRepo;
        $this->energyRepo = $energyRepo;
    }

    public function getSidebarTree() {
        return response()->json($this->nodeRepo->getTreeStructure());
    }

    public function getChartData(Request $request) {
        $nodeId = (int) $request->input('node_id');
        $viewMode = $request->input('view_mode', 'daily');
        $date = $request->input('date');
        $unitType = $request->input('unit_type', 'energy');

        // Gọi Repo
        $data = $this->energyRepo->getDataByNode($nodeId, $viewMode, $date);

        // Nếu không có labels -> Trả về rỗng (Vue sẽ hiện "No data")
        if (empty($data['labels'])) {
            return response()->json([]);
        }

        $response = [];

        // Hệ số chuyển đổi đơn vị
        $factor = 1;
        if ($unitType === 'cost') $factor = 0.15;
        if ($unitType === 'co2')  $factor = 0.4;

        // =========================================================
        // 1. COMPARISON MODE RESPONSE
        // =========================================================
        if ($viewMode === 'comparison') {
            foreach ($data['labels'] as $i => $label) {
                $response[] = [
                    'label' => $label,
                    'period1Data' => ($data['period1'][$i] ?? 0) * $factor, // Năm nay
                    'period2Data' => ($data['period2'][$i] ?? 0) * $factor, // Năm ngoái
                ];
            }
            return response()->json($response);
        }

        // =========================================================
        // 2. DAILY / PERIOD / SHOP-COMPARISON RESPONSE
        // =========================================================
        foreach ($data['labels'] as $i => $label) {
            $response[] = [
                'label' => $label,
                'value' => ($data['values'][$i] ?? 0) * $factor,
                'target' => ($data['targets'][$i] ?? 0) * $factor,
            ];
        }

        return response()->json($response);
    }
}