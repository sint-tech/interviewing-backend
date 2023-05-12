<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create('registration_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->enum('availability_status', ['active', 'inactive']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registration_reasons');
    }
};
