<?php

namespace App\Repositories;

use App\Models\EnergyDailySnapshot;
use App\Models\EnergyRawMeasurement;
use App\Models\Target;
use App\Models\Node;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EnergyDataRepository extends BaseRepository
{
    protected $rawModel;

    public function __construct(EnergyDailySnapshot $snapshotModel, EnergyRawMeasurement $rawModel)
    {
        parent::__construct($snapshotModel);
        $this->rawModel = $rawModel;
    }

    public function getDataByNode(int $nodeId, string $viewMode, ?string $dateStr)
    {
        $date = $dateStr ? Carbon::parse($dateStr) : Carbon::create(2025, 11, 29);

        // Lấy Target chung (để dùng cho Daily/Period)
        $targetType = ($viewMode === 'period') ? 'period' : 'daily';
        $targetRecord = Target::where('node_id', $nodeId)
            ->where('target_type', $targetType)
            ->where('effective_start_date', '<=', $date->toDateString())
            ->orderBy('effective_start_date', 'desc')
            ->first();
        
        $targetValue = $targetRecord ? $targetRecord->target_value : 0;

        // =========================================================
        // CASE 1: COMPARISON VIEW (Sửa lỗi không hiển thị)
        // =========================================================
        if ($viewMode === 'comparison') {
            // Period 1: Năm nay (Ngày được chọn)
            $data1 = $this->rawModel->query()
                ->where('node_id', $nodeId)
                ->whereDate('measured_at', $date->toDateString())
                ->orderBy('measured_at')
                ->get();

            // Period 2: Năm ngoái (Cùng ngày)
            $lastYearDate = $date->copy()->subYear();
            $data2 = $this->rawModel->query()
                ->where('node_id', $nodeId)
                ->whereDate('measured_at', $lastYearDate->toDateString())
                ->orderBy('measured_at')
                ->get();

            // Fill đủ 24 giờ (để đảm bảo biểu đồ luôn vẽ được trục X)
            $f1 = $this->fill24HoursData($data1);
            $f2 = $this->fill24HoursData($data2);

            return [
                'labels' => $f1['labels'],
                'period1' => $f1['values'], // Dữ liệu năm nay
                'period2' => $f2['values'], // Dữ liệu năm ngoái
            ];
        }

        // =========================================================
        // CASE 2: DAILY VIEW
        // =========================================================
        if ($viewMode === 'daily') {
            $data = $this->rawModel->query()
                ->where('node_id', $nodeId)
                ->whereDate('measured_at', $date->toDateString())
                ->orderBy('measured_at')
                ->get();

            if ($data->isEmpty()) return ['labels' => [], 'values' => []];

            $formatted = $this->fill24HoursData($data);
            return [
                'labels' => $formatted['labels'],
                'values' => $formatted['values'],
                'targets' => array_fill(0, 24, $targetValue)
            ];
        }

        // =========================================================
        // CASE 3: PERIOD VIEW (7 Days)
        // =========================================================
        if ($viewMode === 'period') {
            $start = $date->copy()->subDays(6);
            $end = $date->copy();

            // Query từ Raw Data và Group theo ngày để đảm bảo chính xác
            $data = $this->rawModel->query()
                ->select(DB::raw('DATE(measured_at) as date'), DB::raw('SUM(consumption_kwh) as total_kwh'))
                ->where('node_id', $nodeId)
                ->whereBetween('measured_at', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy(DB::raw('DATE(measured_at)'))
                ->get();

            $labels = []; $values = []; $targets = [];
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateString = $d->toDateString();
                $rec = $data->firstWhere('date', $dateString);
                
                $labels[] = $d->format('m-d');
                $values[] = $rec ? $rec->total_kwh : 0;
                $targets[] = $targetValue;
            }

            return ['labels' => $labels, 'values' => $values, 'targets' => $targets];
        }

        // =========================================================
        // CASE 4: SHOP COMPARISON
        // =========================================================
        if ($viewMode === 'shop-comparison') {
            $children = Node::where('parent_id', $nodeId)->where('is_active', true)->orderBy('display_order')->get();
            $labels = []; $values = []; $targets = [];

            foreach ($children as $child) {
                $labels[] = $child->name;
                $val = $this->rawModel->query()
                    ->where('node_id', $child->id)
                    ->whereDate('measured_at', $date->toDateString())
                    ->sum('consumption_kwh');
                $values[] = $val;

                $tObj = Target::where('node_id', $child->id)->where('target_type', 'period')
                    ->where('effective_start_date', '<=', $date->toDateString())
                    ->orderBy('effective_start_date', 'desc')->first();
                $targets[] = $tObj ? $tObj->target_value : 0;
            }
            return ['labels' => $labels, 'values' => $values, 'targets' => $targets];
        }

        return ['labels' => [], 'values' => []];
    }

    // Helper: Fill đủ 24 giờ
    private function fill24HoursData($collection) {
        $map = $collection->mapWithKeys(fn($i) => [Carbon::parse($i->measured_at)->hour => $i->consumption_kwh]);
        $l = []; $v = [];
        for ($i=0; $i<24; $i++) {
            $l[] = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
            $v[] = $map->get($i, 0);
        }
        return ['labels' => $l, 'values' => $v];
    }
}