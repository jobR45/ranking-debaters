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
      Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user'); // 'admin' or 'user'
        $table->string('google_id')->nullable();
        $table->string('discord_id')->nullable();
        $table->string('tiktok_id')->nullable();
        $table->string('avatar')->nullable();
        $table->string('password')->nullable()->change(); // Nullable for social users
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
