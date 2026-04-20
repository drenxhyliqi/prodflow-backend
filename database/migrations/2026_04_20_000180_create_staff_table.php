<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('sid');
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('position')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedInteger('company_id')->nullable();

            $table->index('company_id');
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
