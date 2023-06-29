<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->longText('text');
            $table->float('min_score')->default(1);
            $table->float('max_score')->default(10);
            $table->foreignId('question_variant_id')->nullable()->constrained('question_variants')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
};
