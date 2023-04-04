<?php

namespace Database\Factories;

use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobTitle>
 */
class JobTitleFactory extends Factory
{
    protected $model = JobTitle::class;

    public function definition()
    {
        return [
            'title' => $this->faker->unique()->jobTitle,
            'description' => $this->faker->text(100),
            'availability_status' => $this->faker->randomElement(
                [
                    AvailabilityStatusEnum::Active->value,
                    AvailabilityStatusEnum::Inactive->value,
                ]),
        ];
    }

    public function availabilityIsActive()
    {
        return $this->state(function (array $attributes) {
            return [
                'availability_status' => AvailabilityStatusEnum::Active->value,
            ];
        });
    }
}
