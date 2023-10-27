<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();

            $table->foreignId('interview_template_id')->nullable()->constrained('interview_templates', 'id')->nullOnDelete();
            $table->morphs('creator');
            $table->foreignId('organization_id')->nullable()->constrained('organizations', 'id')->nullOnDelete();

            $table->integer('max_reconnection_tries')->default(0);
            $table->integer('open_positions');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vacancies');
    }
};
