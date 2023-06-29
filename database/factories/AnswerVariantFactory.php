<?php

namespace Database\Factories;

use Domain\AnswerManagement\Models\Answer;
use Domain\AnswerManagement\Models\AnswerVariant;
use Illuminate\Database\Eloquent\Factories\Factory;


class AnswerVariantFactory extends Factory
{
    protected $model = AnswerVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text'  => $this->faker->text(1000_00),
            'description' => $this->faker->text(1000),
            'score'     => $this->faker->numberBetween(1,10),
            'answer_id' => Answer::query()->whereHas('questionVariant')->inRandomOrder()->first()->getKey()
        ];
    }
}
