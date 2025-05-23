<?php

namespace Database\Seeders;

use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Domain\Candidate\Models\RegistrationReason;
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
            'join new company',
        ];

        foreach ($reasons as $reason) {
            RegistrationReason::query()->updateOrCreate(
                ['name' => $reason],
                ['availability_status' => RegistrationReasonsAvailabilityStatusEnum::Active]
            );
        }
    }
}
