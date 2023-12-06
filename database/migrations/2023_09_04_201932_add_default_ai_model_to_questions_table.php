<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('default_ai_model',['gpt-3.5-turbo']);

            $table->longText('system_prompt');
            $table->longText('content_prompt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::getConnection()->getName() == 'sqlite') {
                return;
            }
            $table->dropColumn(['default_ai_model','system_prompt','content_prompt']);
        });
    }
};
