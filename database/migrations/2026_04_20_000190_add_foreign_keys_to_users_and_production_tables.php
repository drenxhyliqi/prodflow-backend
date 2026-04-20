<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_id')->references('cid')->on('companies')->onDeleteCascade();
        });

        Schema::table('production', function (Blueprint $table) {
            $table->foreign('product_id')->references('pid')->on('products')->onDeleteCascade();
        });
    }

    public function down(): void
    {
        Schema::table('production', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
    }
};
