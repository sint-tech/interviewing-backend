<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('answer_variants', function (Blueprint $table) {
            $table->id();
            $table->longText('text');
            $table->text('description')->nullable();
            $table->float('score');

            $table->foreignId('answer_id')->nullable()->constrained('answers')->nullOnDelete();

            $table->morphs('creator');
            $table->morphs('owner');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answer_variants');
    }
};
