<?php

namespace Database\Factories;

use Domain\Invitation\Models\Invitation;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'mobile_country_code' => '+20',
            'mobile_number' => $this->faker->numerify('11########'),
            'should_be_invited_at' => now()->addDays(1),
            'expired_at' => now()->addDays(5),
            'vacancy_id' => Vacancy::factory(),
        ];
    }
}
