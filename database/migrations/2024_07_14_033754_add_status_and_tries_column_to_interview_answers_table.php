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
            $table->json('status')->nullable()->before('created_at');
            $table->json('tries')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_answers', function (Blueprint $table) {
            $table->dropColumn(['status', 'tries']);
        });
    }
};
