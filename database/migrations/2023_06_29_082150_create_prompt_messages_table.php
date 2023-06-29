<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prompt_messages', function (Blueprint $table) {
            $table->id();
            $table->longText('prompt_text');
            $table->foreignId('question_variant_id')->nullable()->constrained('question_Variants')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prompt_messages');
    }
};
