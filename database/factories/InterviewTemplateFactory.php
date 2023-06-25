<?php

namespace Database\Factories;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewTemplateFactory extends Factory
{
    protected $model = InterviewTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(20),
            'description' => $this->faker->text(1000),
            'availability_status' => $this->faker->randomElement(['available', 'unavailable', 'pending', 'paused']),
            'reusable' => $this->faker->boolean(90),
        ];
    }
}
