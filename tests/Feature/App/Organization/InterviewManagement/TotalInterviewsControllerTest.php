<?php
namespace Tests\Feature\App\Organization\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Illuminate\Testing\Fluent\AssertableJson;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;

class TotalInterviewsControllerTest extends TestCase
{
    use DatabaseMigrations;
    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test */
    public function itShouldShowTotalInterviews(): void
    {
        Interview::create([
            'template_id' => InterviewTemplate::factory()->createOne()->id,
            'candidate_id' => Candidate::factory()->createOne()->id,
            'employee_id' => $this->employeeAuth->id,
            'vacancy_id' => Vacancy::factory()->for($this->employeeAuth->organization, 'organization')->createOne()->id,
            'status' => InterviewStatusEnum::Passed->value,
        ]);

        $response = $this->get(route('organization.interviews.count'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->has('count')
                ->has('last_updated_at');
        });

        $response->assertJsonFragment([
            'count' => 1,
        ]);
    }
}
