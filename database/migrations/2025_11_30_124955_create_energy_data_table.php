<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng Dữ liệu thô (Raw Data)
        Schema::create('energy_raw_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('nodes')->onDelete('cascade');
            $table->decimal('consumption_kwh', 18, 4);
            $table->timestamp('measured_at');
            $table->timestamps();

            // Index kép cực quan trọng để View tính toán nhanh
            $table->index(['node_id', 'measured_at']);
        });

        // 2. Tạo MATERIALIZED VIEW
        // Postgres sẽ tính toán sẵn và lưu kết quả vào ổ cứng
        $viewQuery = "
            CREATE MATERIALIZED VIEW energy_daily_snapshots AS
            SELECT
                m.node_id,
                DATE(m.measured_at) as date,
                
                -- Tổng kWh
                SUM(m.consumption_kwh) as total_kwh,
                
                -- Tổng Cost (nhân với giá tại thời điểm đo)
                SUM(m.consumption_kwh * COALESCE(
                    (SELECT value FROM conversion_factors f
                     WHERE f.factor_type = 'unit_price' 
                       AND f.effective_date <= DATE(m.measured_at)
                     ORDER BY f.effective_date DESC LIMIT 1), 0)
                ) as total_cost,

                -- Tổng CO2
                SUM(m.consumption_kwh * COALESCE(
                    (SELECT value FROM conversion_factors f
                     WHERE f.factor_type = 'co2_factor' 
                       AND f.effective_date <= DATE(m.measured_at)
                     ORDER BY f.effective_date DESC LIMIT 1), 0)
                ) as total_co2,
                
                NOW() as last_calculated_at
            FROM energy_raw_measurements m
            GROUP BY m.node_id, DATE(m.measured_at)
        ";

        DB::statement($viewQuery);

        // 3. Tạo Unique Index cho View
        // Bắt buộc để dùng lệnh REFRESH CONCURRENTLY (Update không bị lock bảng)
        DB::statement("CREATE UNIQUE INDEX idx_energy_daily_snapshots_unique ON energy_daily_snapshots (node_id, date)");
    }

    public function down(): void
    {
        // Xóa View trước bảng Raw
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS energy_daily_snapshots");
        Schema::dropIfExists('energy_raw_measurements');
    }
};