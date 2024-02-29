<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class PromptTemplateFactory extends Factory
{
    protected $model = PromptTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'text' => $this->faker->text,
            'stats_text' => $this->faker->text,
            'conclusion_text' => $this->faker->text,
            'version' => $this->faker->numberBetween(1, 100),
            'is_selected' => $this->faker->boolean,
        ];
    }
}
