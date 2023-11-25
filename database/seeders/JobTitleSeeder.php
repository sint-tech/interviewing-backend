<?php

namespace Database\Seeders;

use Domain\JobTitle\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['software engineer','ml ops','devops','hr','marketing manager','product owner'];

        foreach ($data as $datum) {
            JobTitle::query()->firstOrCreate(['title' => $datum]);
        }
    }
}
