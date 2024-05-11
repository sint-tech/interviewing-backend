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
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedInteger('interview_consumption')->after('number_of_employees')->default(0)->comment('this column is deprecated, will be removed or reimplemented once the subscription module is finished');
            $table->unsignedInteger('limit')->after('number_of_employees')->default(config('app.organization_default_limit', 5));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['interview_consumption', 'limit']);
        });
    }
};
