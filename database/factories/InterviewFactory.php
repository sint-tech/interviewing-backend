<?php

namespace Database\Factories;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Interview>
 */
class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vacancy_id' => Vacancy::factory()->for(User::factory(), 'creator'),
            'candidate_id' => Candidate::factory(),
            'interview_template_id' => InterviewTemplate::factory()->for(User::factory(), 'creator'),
            'started_at' => $started_at = $this->faker->dateTime('yesterday'),
            'ended_at' => $this->faker->dateTime('-1 day'),
            'status' => $this->faker->randomElement(InterviewStatusEnum::endedStatuses()),
        ];
    }
}
