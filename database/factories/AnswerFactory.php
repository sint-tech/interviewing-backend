<?php

namespace Database\Factories;

use Domain\AnswerManagement\Models\Answer;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\Factory;


class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text'  => $this->faker->text(1000_00),
            'min_score' => 1,
            'max_score' => 10,
            'question_variant_id'   => QuestionVariant::query()->whereHas('interviewTemplates')->inRandomOrder()->first()?->getKey()
        ];
    }
}
