<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'sale_number')) {
                $table->string('sale_number')->nullable()->after('sid');
            }
            if (!Schema::hasColumn('sales', 'client')) {
                $table->string('client')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('sales', 'total')) {
                $table->decimal('total', 10, 2)->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'sale_number')) {
                $table->dropColumn('sale_number');
            }
            if (Schema::hasColumn('sales', 'client')) {
                $table->dropColumn('client');
            }
            if (Schema::hasColumn('sales', 'total')) {
                $table->dropColumn('total');
            }
        });
    }
};
