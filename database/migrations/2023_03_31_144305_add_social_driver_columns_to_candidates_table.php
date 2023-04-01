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
        Schema::table('candidates', function (Blueprint $table) {
            $table->enum("social_driver_name",["google","linkedin"])->nullable()->after("password");
            $table->string("social_driver_id")->nullable()->after("social_driver_name");

            $table->unique([
                "social_driver_name",
                "social_driver_id"
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropUnique([
                "social_driver_name",
                "social_driver_id"
            ]);

            $table->dropColumn([
                "social_driver_name",
                "social_driver_id"
            ]);
        });
    }
};
