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
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId("current_job_title_id")
                ->nullable()
                ->after("password")
                ->constrained("candidates")
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (DB::getDriverName() == "sqlite") {
                $table->dropColumn(["current_job_title_id"]);
            }
            else {
                $table->dropConstrainedForeignId("current_job_title_id");
            }
        });
    }
};
