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
        Schema::create('salaries', function (Blueprint $table) {
            $table->increments('sid'); 
            $table->unsignedInteger('employee_id')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('comment')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('employee_id');
            $table->index('company_id');

            $table->foreign('employee_id')->references('sid')->on('staff')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};