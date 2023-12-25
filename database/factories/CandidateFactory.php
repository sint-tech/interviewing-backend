<?php

namespace Database\Factories;

use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Domain\Candidate\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Candidate
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->email,
            'mobile_dial_code' => '+20',
            'mobile_number' => $this->faker->unique()->numerify('11########'),
            'password' => Hash::make('password'),
            'email_verified_at' => $this->faker->dateTimeBetween('-30 days'),
            'social_driver_name' => null,
            'social_driver_id' => null,
        ];
    }

    public function registeredNow()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function registeredWithSocialApp(): CandidateFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'social_driver_name' => $this->faker->randomElement([
                    CandidateSocialAppEnum::Linkedin->value,
                    CandidateSocialAppEnum::Google->value,
                ]),
                'social_driver_id' => $this->faker->unique()->uuid(),
                'email_verified_at' => null,
                'password' => null,
            ];
        });
    }
}
