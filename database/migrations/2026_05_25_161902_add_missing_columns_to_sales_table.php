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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('sale_number', 10)->nullable()->after('sid');
            $table->string('client', 255)->nullable()->after('sale_number');
            $table->decimal('total', 10, 2)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sale_number', 'client', 'total']);
        });
    }
};
