<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SintAdminsSeeder::class);

        $this->call(SintOrganizationSeeder::class);

        $this->call(RegistrationReasonSeeder::class);

        $this->call(JobTitleSeeder::class);
    }
}
