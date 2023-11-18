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
        Schema::table('ai_prompt_messages', function (Blueprint $table) {
            $table->foreignId('ai_model_id')
                ->after('ai_model')
                ->nullable()
                ->default(DB::table('ai_models')->first()->id)
                ->constrained('ai_models', 'id')
                ->nullOnDelete();

            $table->dropColumn('ai_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompt_messages', function (Blueprint $table) {
            if (Schema::getConnection()->getName() == 'sqlite') {
                return;
            }

            $table->dropConstrainedForeignId('ai_model_id');
        });
    }
};
