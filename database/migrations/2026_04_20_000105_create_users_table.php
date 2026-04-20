<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('user')->nullable();
            $table->string('username')->nullable();
            $table->string('password');
            $table->unsignedInteger('company_id')->nullable();
            $table->string('role', 50)->default('staff');

            $table->index('username');
            $table->index('role');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
