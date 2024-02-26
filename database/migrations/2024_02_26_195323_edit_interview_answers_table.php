<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('interview_answers', function (Blueprint $table) {
            $table->integer('english_score')->after('score')->comment('english avg score from 0 to 100')->nullable();
            $table->after('ml_text_semantics', function (Blueprint $table) {
                $table->longText('raw_response')->nullable();
                $table->longText('raw_prompt_request')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('interview_answers', function (Blueprint $table) {
            $table->dropColumn([
                'english_score',
                'raw_response',
                'raw_prompt_request',
            ]);
        });

    }
};
