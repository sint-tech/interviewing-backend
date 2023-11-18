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
            $table->foreignId('question_cluster_id')->nullable()->after('question_variant_id')->constrained('question_clusters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_answers', function (Blueprint $table) {
            if (Schema::getConnection()->getName() == 'sqlite') {
                return;
            }
            $table->dropConstrainedForeignId('question_cluster_id');
        });
    }
};
