<?php

namespace Database\Seeders;

use Domain\Candidate\Models\RegistrationReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'enhance my sof skills',
            'attend interview',
            'enhance my technical skills',
            'join new company'
        ];

        foreach ($reasons as $reason) {
            RegistrationReason::query()->firstOrCreate(['name' => $reason]);
        }
    }
}
