<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->enum('name',['gpt-3.5-turbo']);
            $table->enum('status',['active','inactive','pause'])->default('active');
            $table->timestamps();
        });

        DB::table('ai_models')->insert([
            'name'  => 'gpt-3.5-turbo',
            'status'    => 'active',
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ai_models');
    }
};
