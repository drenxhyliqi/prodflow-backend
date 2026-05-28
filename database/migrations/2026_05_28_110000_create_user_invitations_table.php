<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_invitations')) {
            Schema::create('user_invitations', function (Blueprint $table) {
                $table->increments('iid');
                $table->string('user', 255);
                $table->string('username', 255);
                $table->unsignedInteger('company_id');
                $table->string('role', 50)->default('staff');
                $table->string('token', 128)->unique();
                $table->string('status', 20)->default('pending');
                $table->timestamp('expires_at');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['username', 'status']);
                $table->index('company_id');
                $table->foreign('company_id')->references('cid')->on('companies')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_invitations')) {
            Schema::dropIfExists('user_invitations');
        }
    }
};
