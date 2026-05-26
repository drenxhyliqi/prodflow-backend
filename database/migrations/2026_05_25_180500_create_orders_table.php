<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('oid');
            $table->string('order_number', 20);
            $table->string('client', 255);
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->string('sale_number', 10)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index(['order_number', 'company_id']);
            $table->index('product_id');
            $table->index('company_id');
            $table->foreign('product_id')->references('pid')->on('products')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
