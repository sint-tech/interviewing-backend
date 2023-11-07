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
        Schema::table('interview_templates', function (Blueprint $table) {
            $table->dropMorphs('owner');

            $table->foreignId('organization_id')
                ->nullable()
                ->after('owner_id')
                ->constrained('organizations', 'id')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_templates', function (Blueprint $table) {
            $table->nullableMorphs('owner');

            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
