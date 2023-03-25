<?php

namespace Database\Factories;

use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Domain\Candidate\Models\RegistrationReasons;

/**
 * @extends Factory<RegistrationReasons>
 */
class RegistrationReasonsFactory extends Factory
{

    protected $model = RegistrationReasons::class;

    public function definition()
    {
        return [
            "title"         => $this->faker->unique()->jobTitle,
            "availability_status"   => $this->faker->randomElement([
                RegistrationReasonsAvailabilityStatusEnum::Active->value,
                RegistrationReasonsAvailabilityStatusEnum::Inactive->value
            ])
        ];
    }

    public function availabilityIsActive()
    {
        return $this->state(function (array $attributes) {
            return [
                "availability_status"    => RegistrationReasonsAvailabilityStatusEnum::Active->value
            ];
        });
    }
}
