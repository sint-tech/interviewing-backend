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
        Schema::table('interviews', function (Blueprint $table) {
            $table->foreignId('vacancy_id')
                ->nullable()
                ->after('interview_template_id')
                ->constrained('vacancies', 'id')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            if (Schema::getConnection()->getName() == 'sqlite') {
                return;
            }
            $table->dropConstrainedForeignId('vacancy_id');
        });
    }
};
