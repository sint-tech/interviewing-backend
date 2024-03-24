<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('status', ['started', 'withdrew', 'canceled', 'finished', 'passed', 'rejected', 'selected'])->default('started');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('status', ['started', 'withdrew', 'canceled', 'finished', 'passed', 'rejected'])->default('started');
        });
    }
};
