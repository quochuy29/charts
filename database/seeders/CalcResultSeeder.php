<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalcResultSeeder extends Seeder
{
    public function run()
    {
        // 1. Cấu hình
        $equipmentIds = range(1, 20); // Giả lập 20 thiết bị
        $startDate = Carbon::create(2025, 12, 20, 0, 0, 0); // Ngày bắt đầu
        $endDate   = Carbon::create(2025, 12, 25, 23, 0, 0); // Ngày kết thúc (5 ngày)
        $batchSize = 500;
        $dataBuffer = [];

        // 2. Vòng lặp từng ngày (Day Loop)
        $currentDate = $startDate->copy();
        
        // Loop cho đến khi vượt quá ngày kết thúc (so sánh theo ngày)
        while ($currentDate->lte($endDate)) {
            
            // --- BƯỚC QUAN TRỌNG: TẠO PARTITION CHO NGÀY HIỆN TẠI ---
            $this->createPartitionForDate($currentDate);

            // 3. Vòng lặp từng giờ trong ngày (00:00 -> 23:00)
            for ($hour = 0; $hour < 24; $hour++) {
                
                // Tạo thời gian chuẩn: YYYY-MM-DD HH:00:00
                $timeBucket = $currentDate->copy()->setTime($hour, 0, 0);
                
                // Nếu thời gian vượt quá thời điểm kết thúc thì dừng
                if ($timeBucket->gt($endDate)) break 2;

                $timeBucketStr = $timeBucket->format('Y-m-d H:i:00');

                // 4. Tạo data cho từng thiết bị
                foreach ($equipmentIds as $equipId) {
                    $dataBuffer[] = [
                        'time_bucket'   => $timeBucketStr,
                        'equipment_id'  => $equipId,
                        'value'         => mt_rand(1000, 50000) / 100, // Random float 10.00 - 500.00
                        'type'          => 1, // Loại điện
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    // Batch Insert
                    if (count($dataBuffer) >= $batchSize) {
                        DB::table('t_calc_result')->insert($dataBuffer);
                        $dataBuffer = [];
                    }
                }
            }

            // Sang ngày tiếp theo
            $currentDate->addDay();
        }

        // Insert nốt data còn dư
        if (!empty($dataBuffer)) {
            DB::table('t_calc_result')->insert($dataBuffer);
        }
    }

    /**
     * Hàm tạo partition PostgreSQL theo ngày
     */
    private function createPartitionForDate(Carbon $date)
    {
        $partitionName = 't_calc_result_' . $date->format('Y_m_d');
        $fromDate = $date->format('Y-m-d 00:00:00');
        
        // Lưu ý: Range Partition trong Postgres là [start, end), tức là lấy start nhưng KHÔNG lấy end.
        // Nên 'TO' phải là 00:00:00 của ngày hôm sau.
        $toDate = $date->copy()->addDay()->format('Y-m-d 00:00:00');

        $sql = "
            CREATE TABLE IF NOT EXISTS {$partitionName} 
            PARTITION OF t_calc_result 
            FOR VALUES FROM ('{$fromDate}') TO ('{$toDate}')
        ";

        DB::statement($sql);
    }
}
