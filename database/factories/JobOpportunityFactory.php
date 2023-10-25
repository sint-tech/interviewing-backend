<?php

namespace Database\Factories;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Domain\Vacancy\Models\JobOpportunity;

/**
 * @extends Factory<JobOpportunity>
 */
class JobOpportunityFactory extends Factory
{

    protected $model = JobOpportunity::class;

    public function definition()
    {
        return [
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(1000),
            'interview_template_id' => InterviewTemplate::factory()->for(Employee::factory()->createOne(),'creator')
                ->for(Employee::factory()->createOne(),'owner'),
            'organization_id' => Organization::factory(),
            'open_positions' => $this->faker->numberBetween(1,15)
        ];
    }
}
