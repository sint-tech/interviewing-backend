<?php

namespace Database\Factories;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancy>
 */
class JobOpportunityFactory extends Factory
{
    protected $model = Vacancy::class;

    public function definition()
    {
        return [
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(1000),
            'interview_template_id' => InterviewTemplate::factory()->for(Employee::factory()->createOne(), 'creator')
                ->for(Employee::factory()->createOne(), 'owner'),
            'organization_id' => Organization::factory(),
            'open_positions' => $this->faker->numberBetween(1, 15),
        ];
    }
}
