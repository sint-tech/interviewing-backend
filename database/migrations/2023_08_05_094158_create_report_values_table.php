<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();

            $table->string('type')->default(null);

            $table->string('key')->index();
            $table->text('value')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_values');
    }
};
