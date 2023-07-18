<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_cluster_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_cluster_id')->constrained('question_clusters','id')->cascadeOnDelete();

            $table->enum('type',[
                'advice',
                'impact'
            ]);
            $table->longText('statement');

            $table->tinyInteger('min_score')->default(1);
            $table->tinyInteger('max_score')->default(10);

            $table->unique(['question_cluster_id','type','min_score','max_score'],'unique_recommendation_state_range');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_cluster_recommendations');
    }
};
