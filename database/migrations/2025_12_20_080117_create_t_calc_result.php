<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('t_calc_result', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamp('time_bucket');
        //     $table->bigInteger('equipment_id');
        //     $table->float('value');
        //     $table->tinyInteger('type')->comment('1: electricity 2:gas 3:high pressure 4:low pressure 5: co2 6: cost');
        //     $table->timestamps();

        //     $table->index(['time_bucket', 'equipment_id']);
        //     $table->index(['time_bucket', 'equipment_id', 'value']);
        //     $table->index(['time_bucket', 'equipment_id', 'type']);
        //     $table->index(['time_bucket', 'equipment_id', 'value', 'type']);
        // });

        // Tạo bảng cha
        DB::statement("
            CREATE TABLE t_calc_result (
                id BIGSERIAL,
                time_bucket TIMESTAMP WITHOUT TIME ZONE NOT NULL,
                equipment_id BIGINT NOT NULL,
                value DOUBLE PRECISION,
                type INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id, time_bucket)
            ) PARTITION BY RANGE (time_bucket);
        ");

        // Tạo Partition Default để tránh lỗi nếu insert ngày lạ
        DB::statement("CREATE TABLE t_calc_result_default PARTITION OF t_calc_result DEFAULT");
        
        // Tạo Partition mẫu cho ngày hiện tại (Thực tế nên dùng Cronjob để tạo tự động hàng ngày)
        $today = now()->format('Y_m_d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $todayDb = now()->format('Y-m-d');
        
        // Ví dụ tạo partition cho ngày hôm nay
        // Lưu ý: Cần check exists trước khi tạo trong môi trường thực tế
        DB::statement("CREATE INDEX idx_main_cover 
        ON t_calc_result (time_bucket, equipment_id, value, type);");

        DB::statement("CREATE INDEX idx_skip_value 
        ON t_calc_result (time_bucket, equipment_id, type);");

        DB::statement("
            CREATE TABLE IF NOT EXISTS t_calc_result_{$today} PARTITION OF t_calc_result
            FOR VALUES FROM ('{$todayDb} 00:00:00') TO ('{$todayDb} 23:59:59');
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_calc_result');
    }
};
