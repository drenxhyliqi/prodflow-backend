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
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->increments('sid');
                $table->string('supplier', 255)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('location', 255)->nullable();
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
