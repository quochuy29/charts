<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnergySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hệ số quy đổi
        DB::table('conversion_factors')->insertOrIgnore([
            ['factor_type' => 'unit_price', 'value' => 0.15, 'effective_date' => '2023-01-01', 'created_at' => now(), 'updated_at' => now()],
            ['factor_type' => 'co2_factor', 'value' => 0.40, 'effective_date' => '2023-01-01', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ==========================================
        // 2. Tạo Cây cấu trúc (Nodes)
        // ==========================================
        $factories = [];

        // --- FACTORY 1 ---
        $f1 = $this->createNode(null, 'Factory 1 - Main Production', 'factory');
        $l1 = $this->createNode($f1, 'Line 1 - Assembly', 'line');
        $sA = $this->createNode($l1, 'Shop A - Welding', 'shop');
        $sB = $this->createNode($l1, 'Shop B - Painting', 'shop');
        
        $factories[] = [
            'id' => $f1,
            'lines' => [
                [
                    'id' => $l1,
                    'shops' => [
                        ['id' => $sA, 'equipments' => [$this->createNode($sA, 'Welder 01', 'equipment'), $this->createNode($sA, 'Welder 02', 'equipment')]],
                        ['id' => $sB, 'equipments' => [$this->createNode($sB, 'Paint Booth 01', 'equipment')]],
                    ]
                ]
            ]
        ];

        // --- FACTORY 2 ---
        $f2 = $this->createNode(null, 'Factory 2 - Processing', 'factory');
        $l21 = $this->createNode($f2, 'Line 2.1 - Casting', 'line');
        $sC = $this->createNode($l21, 'Shop C - Molding', 'shop');
        $l22 = $this->createNode($f2, 'Line 2.2 - Finishing', 'line');
        $sD = $this->createNode($l22, 'Shop D - Polishing', 'shop');

        $factories[] = [
            'id' => $f2,
            'lines' => [
                [
                    'id' => $l21,
                    'shops' => [['id' => $sC, 'equipments' => [$this->createNode($sC, 'Molding Machine 01', 'equipment')]]]
                ],
                [
                    'id' => $l22,
                    'shops' => [['id' => $sD, 'equipments' => [$this->createNode($sD, 'Polisher 01', 'equipment'), $this->createNode($sD, 'Polisher 02', 'equipment')]]]
                ]
            ]
        ];

        // ==========================================
        // 3. Sinh Dữ liệu (2024 & 2025) - Dựa trên Input Date
        // ==========================================
        
        // Mốc thời gian test: 29/11/2025
        $baseDate = Carbon::create(2025, 11, 29);

        // Tạo dữ liệu cho 2 khoảng thời gian:
        // 1. Tháng 11 năm 2025 (Năm hiện tại - Period 1)
        // 2. Tháng 11 năm 2024 (Năm ngoái - Period 2)
        $periods = [
            '2025_current' => [
                'start' => $baseDate->copy()->startOfMonth(), // 2025-11-01
                'end'   => $baseDate->copy()->endOfMonth(),   // 2025-11-30
                'modifier' => 1.0 // Dữ liệu gốc
            ],
            '2024_last_year' => [
                'start' => $baseDate->copy()->subYear()->startOfMonth(), // 2024-11-01
                'end'   => $baseDate->copy()->subYear()->endOfMonth(),   // 2024-11-30
                'modifier' => 0.8 // Giả lập năm ngoái thấp hơn 20%
            ]
        ];

        $this->command->info('Generating raw data for Nov 2025 and Nov 2024...');

        foreach ($periods as $key => $period) {
            $startDate = $period['start'];
            $endDate = $period['end'];
            $mod = $period['modifier'];
            $rawInsertData = [];

            $this->command->info("Processing: {$key} ({$startDate->toDateString()} -> {$endDate->toDateString()})");

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                for ($hour = 0; $hour < 24; $hour++) {
                    $measuredAt = $date->copy()->setHour($hour)->toDateTimeString();
                    
                    // Logic giả lập: 8h-17h cao điểm
                    $isPeakHour = ($hour >= 8 && $hour <= 17);

                    foreach ($factories as $factory) {
                        $factoryTotal = 0;

                        foreach ($factory['lines'] as $line) {
                            $lineTotal = 0;

                            foreach ($line['shops'] as $shop) {
                                $shopTotal = 0;

                                foreach ($shop['equipments'] as $eqId) {
                                    // Sinh data Equipment (ngẫu nhiên)
                                    $base = $isPeakHour ? rand(10, 20) : rand(1, 5);
                                    
                                    // Thêm nhiễu ngẫu nhiên để 2 năm trông khác nhau
                                    // Nếu không có rand() ở đây thì biểu đồ 2 năm sẽ giống hệt nhau về hình dáng
                                    $noise = rand(-10, 10) / 10; 
                                    
                                    $val = ($base + $noise) * $mod;
                                    $val = max(0, $val);

                                    $shopTotal += $val;
                                    $rawInsertData[] = $this->makeRow($eqId, $val, $measuredAt);
                                }
                                $rawInsertData[] = $this->makeRow($shop['id'], $shopTotal, $measuredAt);
                                $lineTotal += $shopTotal;
                            }
                            $rawInsertData[] = $this->makeRow($line['id'], $lineTotal, $measuredAt);
                            $factoryTotal += $lineTotal;
                        }
                        $rawInsertData[] = $this->makeRow($factory['id'], $factoryTotal, $measuredAt);
                    }
                }

                // Insert chunk theo ngày
                DB::table('energy_raw_measurements')->insert($rawInsertData);
                $rawInsertData = [];
            }
        }

        // ==========================================
        // 4. Tạo Target (Mục tiêu)
        // ==========================================
        // (Giữ nguyên logic tạo target tích lũy như file cũ)
        $this->command->info('Generating Targets...');
        
        foreach ($factories as $factory) {
            $factoryTarget = 0;
            foreach ($factory['lines'] as $line) {
                $lineTarget = 0;
                foreach ($line['shops'] as $shop) {
                    $shopTarget = 0;
                    foreach ($shop['equipments'] as $eqId) {
                        $eqTargetVal = rand(500, 800); 
                        $this->createTarget($eqId, $eqTargetVal);
                        $shopTarget += $eqTargetVal;
                    }
                    $this->createTarget($shop['id'], $shopTarget);
                    $lineTarget += $shopTarget;
                }
                $this->createTarget($line['id'], $lineTarget);
                $factoryTarget += $lineTarget;
            }
            $this->createTarget($factory['id'], $factoryTarget);
        }
        
        // ==========================================
        // 5. Refresh View
        // ==========================================
        $this->command->info('Refreshing Materialized View...');
        DB::statement("REFRESH MATERIALIZED VIEW CONCURRENTLY energy_daily_snapshots");
        $this->command->info('Seeding completed successfully.');
    }

    // --- HELPERS ---
    private function createNode($parentId, $name, $type) {
        return DB::table('nodes')->insertGetId([
            'parent_id' => $parentId, 'name' => $name, 'type' => $type,
            'created_at' => now(), 'updated_at' => now()
        ]);
    }

    private function createTarget($nodeId, $value) {
        // Target Daily
        DB::table('targets')->insert([
            'node_id' => $nodeId, 'target_value' => $value / 24, 'target_type' => 'daily',
            'effective_start_date' => '2020-01-01', 'created_at' => now(), 'updated_at' => now()
        ]);
        // Target Period
        DB::table('targets')->insert([
            'node_id' => $nodeId, 'target_value' => $value, 'target_type' => 'period',
            'effective_start_date' => '2020-01-01', 'created_at' => now(), 'updated_at' => now()
        ]);
    }

    private function makeRow($nodeId, $val, $time) {
        return [
            'node_id' => $nodeId, 'consumption_kwh' => $val, 'measured_at' => $time,
            'created_at' => now(), 'updated_at' => now(),
        ];
    }
}