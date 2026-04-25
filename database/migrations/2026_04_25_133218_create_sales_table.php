<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('sid');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
