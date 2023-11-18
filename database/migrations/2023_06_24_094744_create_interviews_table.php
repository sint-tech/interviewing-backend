<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('interview_template_id')
                ->nullable()
                ->constrained('interview_templates', 'id')
                ->nullOnDelete();

            $table->foreignId('candidate_id')
                ->nullable()
                ->constrained('candidates', 'id')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['started', 'withdrew', 'canceled', 'finished', 'passed', 'rejected'])->default('started');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interviews');
    }
};
