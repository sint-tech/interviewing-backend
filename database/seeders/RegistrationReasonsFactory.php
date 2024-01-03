<?php

namespace Database\Seeders;

use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Domain\Candidate\Models\RegistrationReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistrationReason>
 */
class RegistrationReasonsFactory extends Factory
{
    protected $model = RegistrationReason::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->jobTitle,
            'availability_status' => $this->faker->randomElement([
                RegistrationReasonsAvailabilityStatusEnum::Active->value,
                RegistrationReasonsAvailabilityStatusEnum::Inactive->value,
            ]),
        ];
    }

    public function availabilityIsActive()
    {
        return $this->state(function (array $attributes) {
            return [
                'availability_status' => RegistrationReasonsAvailabilityStatusEnum::Active->value,
            ];
        });
    }
}
