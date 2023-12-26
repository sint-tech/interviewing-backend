<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('invitations');

        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');

            $table->enum('mobile_country_code', supported_countries_codes());
            $table->integer('mobile_number');

            $table->integer('batch');
            $table->foreignId('vacancy_id')->nullable()->constrained('vacancies', 'id')->nullOnDelete();
            $table->foreignId('interview_template_id')->nullable()->constrained('interview_templates', 'id')->nullOnDelete();

            $table->timestamp('should_be_invited_at');

            $table->morphs('creator');

            $table->timestamp('last_invited_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invitations');
    }
};
