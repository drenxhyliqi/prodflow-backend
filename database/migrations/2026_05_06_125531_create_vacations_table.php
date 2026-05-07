<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vacations', function (Blueprint $table) {
            $table->increments('vid');
            $table->unsignedInteger('staff_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->longText('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('company_id')->nullable();

            $table->foreign('staff_id')->references('sid')->on('staff')->nullOnDelete();
            $table->foreign('company_id')->references('cid')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
