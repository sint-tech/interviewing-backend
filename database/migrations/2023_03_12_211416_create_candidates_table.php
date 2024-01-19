<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->unique();

            $table->enum('mobile_dial_code', ['+20', '+966'])->nullable();
            $table->integer('mobile_number')->nullable();
            $table->unique(['mobile_dial_code', 'mobile_number']);

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->foreignId('current_job_title_id')
                ->nullable()
                ->constrained('job_titles')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
