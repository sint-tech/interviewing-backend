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
        Schema::table('interviews', function (Blueprint $table) {
            DB::statement("ALTER TABLE interviews MODIFY status ENUM('not_started','started','withdrew','canceled','accepted','finished')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            DB::statement("ALTER TABLE interviews MODIFY status ENUM('not_started','started','withdrew','canceled','accepted')");
        });
    }
};
