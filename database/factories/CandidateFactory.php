<?php

namespace Database\Factories;

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
            "first_name"    => $first_name = $this->faker->firstName,
            "last_name"     => $last_name = $this->faker->lastName,
            "full_name"     => $first_name . $last_name,
            "email"         => $this->faker->unique()->email,
            "password"      => Hash::make("password"),
            "email_verified_at" => $this->faker->dateTimeBetween('-30 days')
        ];
    }

    public function registeredNow()
    {
        return $this->state(function (array $attributes) {
            return [
                "email_verified_at" => null
            ];
        });
    }
}
