<?php

namespace Database\Seeders;

use Domain\Organization\Models\Organization;
use Illuminate\Database\Seeder;

class SintOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::query()->firstOrCreate([
            'name' => 'sint',
        ]);
    }
}
