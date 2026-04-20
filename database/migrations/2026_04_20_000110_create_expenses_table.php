<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('eid');
            $table->string('comment')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('company_id');
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
