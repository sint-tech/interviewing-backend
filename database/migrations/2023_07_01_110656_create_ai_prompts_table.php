<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();

            $table->enum('model',['gpt-3.5-turbo']);

            $table->foreignId('question_variant_id')
                ->nullable()
                ->constrained('question_variants')
                ->nullOnDelete();

            $table->enum('status', [
                'enabled',
                'disabled',
                'enabled_not_used',
            ]);

            $table->longText('system');
            $table->longText('content');

            $table->integer('weight')->comment('total weight group by question variant should equal 100');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_prompts');
    }
};
