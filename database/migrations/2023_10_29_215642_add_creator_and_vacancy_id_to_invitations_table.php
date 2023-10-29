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
        Schema::table('invitations', function (Blueprint $table) {
            if (! app()->isProduction()) {
                DB::table('invitations')->delete();
            }

            $table->foreignId('vacancy_id')->nullable()->after('batch')->constrained('vacancies', 'id')->nullOnDelete();

            $table->after('interview_template_id', fn (Blueprint $table) => $table->nullableMorphs('creator'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vacancy_id');

            $table->dropMorphs('creator');
        });
    }
};
