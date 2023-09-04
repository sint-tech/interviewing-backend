<?php

namespace Database\Factories;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $min_reading_duration_in_seconds = $this->faker->numberBetween(15, 360);

        return [
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(1000),
            'question_type' => $this->faker->randomElement([
                QuestionTypeEnum::Written->value,
                QuestionTypeEnum::Mcq->value,
                QuestionTypeEnum::Boolean->value,
            ]),
            'difficult_level' => $this->faker->numberBetween(1, 10),
            'min_reading_duration_in_seconds' => $min_reading_duration_in_seconds,
            'max_reading_duration_in_seconds' => $min_reading_duration_in_seconds + $this->faker->numberBetween(10, 60),
            'default_ai_model_id' => AIModel::query()->firstOrCreate(),
        ];
    }
}
