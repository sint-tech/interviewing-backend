<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interview_template_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('interview_template_id')
                ->constrained('interview_templates', 'id')
                ->cascadeOnDelete();

            $table->foreignId('question_variant_id')
                ->nullable()
                ->constrained('question_variants', 'id')
                ->nullOnDelete();

            $table->foreignId('question_cluster_id')
                ->nullable()
                ->constrained('question_clusters', 'id')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interview_template_questions');
    }
};
