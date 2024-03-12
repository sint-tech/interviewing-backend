<?php

namespace Database\Factories;

use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InterviewReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => InterviewReport::DEFAULT_REPORT_NAME,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (InterviewReport $interviewReport) {
            $values = [
                'avg_score' => fake()->randomFloat(2, 0, 100),
                'candidate_advices' => [],
                'impacts' => [],
                'question_clusters_stats' => [],
                'language_fluency_score' => fake()->randomFloat(2, 0, 100),
                'recruiter_advices' => [],
            ];

            $interviewReport->setMeta($values);
        });
    }
}
