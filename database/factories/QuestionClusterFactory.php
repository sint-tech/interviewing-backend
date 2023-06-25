<?php

namespace Database\Factories;

use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionClusterFactory extends Factory
{
    protected $model = QuestionCluster::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(1000),
        ];
    }
}
