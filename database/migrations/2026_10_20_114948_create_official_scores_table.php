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
       Schema::create('official_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('debater_id')->constrained()->onDelete('cascade');
        $table->unsignedInteger('etiquette'); // آداب النقاش
        $table->unsignedInteger('knowledge'); // المعرفة
        $table->unsignedInteger('logic');     // المنطق
        $table->unsignedInteger('victory');    // الفوز
        $table->unsignedInteger('total_score'); // المجموع
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_scores');
    }
};
