<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->increments('tid');
            $table->string('truck')->nullable();
            $table->string('license_plate')->nullable();
            $table->decimal('capacity', 10, 2)->nullable();
            $table->string('status', 50)->default('Free');
            $table->unsignedInteger('company_id')->nullable();

            $table->index('company_id');
            $table->foreign('company_id')->references('cid')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
