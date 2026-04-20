<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials_stock', function (Blueprint $table) {
            $table->increments('msid');
            $table->unsignedInteger('material_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('material_id');
            $table->index('company_id');
            $table->foreign('material_id')->references('mid')->on('materials')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials_stock');
    }
};
