<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('report_batches')) {
            Schema::create('report_batches', function (Blueprint $table) {
                $table->increments('bid');
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('user_id')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 50)->default('queued');
                $table->unsignedSmallInteger('total_count')->default(0);
                $table->unsignedSmallInteger('completed_count')->default(0);
                $table->unsignedSmallInteger('failed_count')->default(0);
                $table->timestamps();

                $table->index(['company_id', 'status']);
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('report_runs')) {
            Schema::create('report_runs', function (Blueprint $table) {
                $table->increments('rid');
                $table->unsignedInteger('batch_id');
                $table->string('report_type', 100);
                $table->string('status', 50)->default('queued');
                $table->longText('result')->nullable();
                $table->text('error_message')->nullable();
                $table->string('download_url')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['batch_id', 'status']);
                $table->index('report_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('report_runs');
        Schema::dropIfExists('report_batches');
    }
};
