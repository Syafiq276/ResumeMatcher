<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Nullable for guest uploads if needed
            $table->foreignId('job_ad_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('match_percentage', 5, 2);
            $table->text('llm_feedback')->nullable();
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->json('detailed_scores')->nullable(); // Store breakdown like { 'lexicon': 80, 'vector': 90 }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
