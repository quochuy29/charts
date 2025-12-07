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
        Schema::create('mst_production_plan', function (Blueprint $table) {
            $table->id();
            $table->bigInt('factory_id');
            $table->integer('fiscal_year');
            $table->tinyInteger('month')->unsigned();
            $table->integer('quantity')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['factory_id', 'fiscal_year', 'month'], 'prod_plans_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_production_plan');
    }
};
