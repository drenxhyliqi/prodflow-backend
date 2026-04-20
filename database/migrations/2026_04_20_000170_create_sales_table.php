<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('sid');
            $table->integer('client_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('client_id');
            $table->index('product_id');
            $table->index('company_id');
            $table->foreign('product_id')->references('pid')->on('products')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
