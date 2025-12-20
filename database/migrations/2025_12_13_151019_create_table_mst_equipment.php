<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mst_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('level');
            $table->bigInteger('parent_id')->nullable();
            $table->integer('item_type')->comment('1:Line 2:Facility 3:Utility 4:Equipment');
            $table->integer('display_order');
            $table->boolean('is_visibility');
            $table->timestamps();

            // 1. Index tối ưu cho việc đệ quy lấy con (Quan trọng nhất)
            // Query: WHERE parent_id = X AND is_visibility = 1 ORDER BY display_order
            $table->index(['parent_id', 'is_visibility', 'display_order'], 'idx_recursive_children');

            // 2. Index tối ưu cho việc lấy Node gốc (Root)
            // Query: WHERE item_type = 1 AND is_visibility = 1 ORDER BY display_order
            $table->index(['item_type', 'is_visibility', 'display_order'], 'idx_tree_root');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_mst_equipment');
    }
};
