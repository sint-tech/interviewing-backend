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
        Schema::create('candidate_desire_hiring_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("candidate_id")->nullable()->constrained("candidates")->nullOnDelete();
            $table->foreignId("job_title_id")->nullable()->constrained("job_titles")->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_desire_hiring_positions');
    }
};
