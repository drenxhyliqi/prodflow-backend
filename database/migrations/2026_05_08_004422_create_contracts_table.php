<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('cid');

            // Lidhja me tabelen staff (employee_id)
            $table->unsignedInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('sid')->on('staff')->onDelete('cascade');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('Active');

            $table->unsignedInteger('company_id')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
