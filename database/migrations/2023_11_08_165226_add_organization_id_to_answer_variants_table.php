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
        Schema::table('answer_variants', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('owner_type')
                ->constrained('organizations', 'id')
                ->nullOnDelete();

            $table->dropMorphs('owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answer_variants', function (Blueprint $table) {
            $table->after('organization_id', function (Blueprint $table) {
                $table->nullableMorphs('owner');
            });
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
