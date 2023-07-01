<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_prompt_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('ai_model', ['gpt-3.5']);
            $table->longText('prompt_text');

            $table->foreignId('question_variant_id')
                ->nullable()
                ->constrained('question_variants')
                ->nullOnDelete();

            $table->boolean('is_default')
                ->default(true)
                ->comment('the default prompt message template for the question variant');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_prompt_messages');
    }
};
