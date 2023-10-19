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
        Schema::table('organization_employees', function (Blueprint $table) {
            $table->dropIndex('employees__lft__rgt_parent_id_index');

            $table->dropColumn([
                '_lft',
                '_rgt',
                'parent_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_employees', function (Blueprint $table) {
            $table->after('organization_id', function (Blueprint $table) {
                $table->unsignedInteger('_lft');
                $table->unsignedInteger('_rgt');
                $table->unsignedInteger('parent_id');

                $table->index(['_lft', '_rgt', 'parent_id'], 'employees__lft__rgt_parent_id_index');
            });
        });
    }
};
