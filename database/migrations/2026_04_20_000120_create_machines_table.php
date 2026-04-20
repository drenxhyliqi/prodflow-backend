<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->increments('mid');
            $table->string('machine')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('company_id');
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
