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
            if (! Schema::hasColumn('questions','default_ai_model_id')) {
                $table->foreignId('default_ai_model_id')
                    ->nullable()
                    ->default(DB::table('ai_models')->first()->id)
                    ->constrained('ai_models', 'id')
                    ->nullOnDelete();
            }

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
            $table->dropConstrainedForeignId('default_ai_model_id');
        });
    }
};
