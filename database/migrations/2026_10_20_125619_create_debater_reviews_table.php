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
        Schema::create('debater_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debater_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('written_review'); // Full written debate review
            $table->string('video_url')->nullable(); // Social media link (TikTok, YouTube, X)
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debater_reviews');
    }
};
