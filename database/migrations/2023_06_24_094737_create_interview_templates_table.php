<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interview_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('availability_status', ['pending', 'available', 'unavailable', 'paused']);

            $table->morphs('creator');
            $table->foreignId('organization_id')
                ->nullable()
                ->constrained('organizations', 'id')
                ->nullOnDelete();

            $table->boolean('reusable')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interview_templates');
    }
};
