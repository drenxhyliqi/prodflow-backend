<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production', function (Blueprint $table) {
            $table->increments('pid');
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('machine_id')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('product_id');
            $table->index('machine_id');
            $table->index('company_id');
            $table->foreign('machine_id')->references('mid')->on('machines')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production');
    }
};
