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
        Schema::table('debater_reviews', function (Blueprint $table) {
            $table->unique('debater_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debater_reviews', function (Blueprint $table) {
            $table->dropUnique(['debater_id']);
        });
    }
};
