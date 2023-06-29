<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interview_answers', function (Blueprint $table) {
            $table->id();
            $table->enum('question_occurrence_reason',
                [
                    'template_question',
                    'additional',
                    'recommended'
                ]
            )
                ->default('template_question');
            $table->longText('answer_text')->nullable();

            $table->float('score');
            $table->integer('min_score')->default(0);
            $table->integer('max_score')->default(10);

            $table->foreignId('interview_id')->constrained('interviews')->cascadeOnDelete();
            $table->foreignId('answer_variant_id')->nullable()->constrained('answer_variants')->nullOnDelete();
            $table->foreignId('question_variant_id')->nullable()->constrained('question_variants')->nullOnDelete();

            $table->json('ml_video_semantics')->nullable();
            $table->json('ml_audio_semantics')->nullable();
            $table->json('ml_text_semantics')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interview_answers');
    }
};
