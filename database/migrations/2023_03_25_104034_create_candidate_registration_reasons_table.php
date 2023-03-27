<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidate_registration_reasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId("candidate_id")->nullable()->constrained("candidates")->nullOnDelete();
            $table->foreignId("registration_reason_id")->nullable()->constrained("registration_reasons")->nullOnDelete();

            $table->unique([
                "candidate_id",
                "registration_reason_id"
            ],"candidate_id_registration_reason_id_unique");

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidate_registration_reasons');
    }
};
