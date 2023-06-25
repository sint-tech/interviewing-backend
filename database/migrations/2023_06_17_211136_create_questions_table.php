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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->morphs('creator');
            $table->foreignId('question_cluster_id')->nullable()->constrained('question_clusters')->nullOnDelete();

            $table->text('title');
            $table->text('description')->nullable();

            $table->enum('question_type', [
                'written',
                'boolean',
                'mcq',
            ])->default('written');

            $table->tinyInteger('difficult_level');

            $table->integer('min_reading_duration_in_seconds')->default(10);
            $table->integer('max_reading_duration_in_seconds')->default(120);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
