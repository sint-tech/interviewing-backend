<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mapping = [
            'gpt-3.5-turbo' => AiModelEnum::Gpt_3_5->value,
            'gpt-4o' => AiModelEnum::Gpt_4o->value,
        ];

        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->renameColumn('model', 'model_old');
        });

        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->tinyInteger('model')->after('model_old');
        });

        $aiPrompts = DB::table('ai_prompts')->get();
        foreach ($aiPrompts as $aiPrompt) {
            $newModelValue = $mapping[$aiPrompt->model_old];
            DB::table('ai_prompts')
                ->where('id', $aiPrompt->id)
                ->update(['model' => $newModelValue]);
        }

        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->dropColumn('model_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            //
        });
    }
};
