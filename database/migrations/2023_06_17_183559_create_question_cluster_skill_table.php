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
        Schema::create('question_cluster_skill', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_cluster_id')->constrained('question_clusters')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();

            $table->unique(['question_cluster_id','skill_id']);

            $table->nullableMorphs('assigner');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_cluster_skill');
    }
};
