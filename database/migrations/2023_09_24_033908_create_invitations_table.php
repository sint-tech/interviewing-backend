<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Support\Services\MobileStrategy\MobileCountryEnum;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            $table->string('email');

            $table->enum('mobile_country_code',supported_countries_codes());
            $table->integer('mobile_number');

            $table->integer('batch');

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
