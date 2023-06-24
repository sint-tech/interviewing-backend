<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_variants', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->text('description')->nullable();
            $table->integer('reading_time_in_seconds');
            $table->integer('answering_time_in_seconds');

            $table->foreignId('question_id')->nullable()->constrained('questions','id')->nullOnDelete();

            $table->morphs('creator');
            $table->morphs('owner');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_variants');
    }
};
