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
        Schema::create('maintenances', function (Blueprint $table) {
            
            $table->increments('mid'); 
            $table->unsignedInteger('machine_id')->nullable();
            $table->date('date')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('machine_id');
            $table->index('company_id');

            $table->foreign('machine_id')->references('mid')->on('machines')->nullOnDelete(); 
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};