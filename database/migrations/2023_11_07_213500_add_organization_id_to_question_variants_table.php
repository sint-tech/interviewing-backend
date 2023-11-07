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
        Schema::table('question_variants', function (Blueprint $table) {
            $table->dropMorphs('owner');

            $table->foreignId('organization_id')
                ->nullable()
                ->after('question_id')
                ->constrained('organizations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->nullableMorphs('owner');
        });
    }
};
