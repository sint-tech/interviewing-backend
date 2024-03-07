<?php

namespace Database\Factories;

use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class QuestionVariantFactory extends Factory
{
    protected $model = QuestionVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => $this->faker->text(1000),
            'description' => $this->faker->text(1000),
            'status' => $this->faker->randomElement(QuestionVariantStatusEnum::toArray()),
            'reading_time_in_seconds' => $this->faker->numberBetween(12, 360),
            'answering_time_in_seconds' => $this->faker->numberBetween(12, 360),
        ];
    }
}
