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
       Schema::create('audience_reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('debater_id')->constrained()->onDelete('cascade');
        $table->string('reviewer_name')->default('زائر');
        $table->unsignedInteger('etiquette')->default(0); // 1-25
        $table->unsignedInteger('knowledge')->default(0); // 1-25
        $table->unsignedInteger('logic')->default(0);     // 1-25
        $table->unsignedInteger('victory')->default(0);    // 1-25
        $table->unsignedInteger('total_score')->default(0); // Out of 100
        $table->text('comment')->nullable();
        $table->boolean('approved')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audience_reviews');
    }
};
