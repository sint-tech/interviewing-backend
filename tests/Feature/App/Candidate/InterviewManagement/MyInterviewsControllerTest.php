<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Illuminate\Testing\Fluent\AssertableJson;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\InterviewManagement\Models\InterviewTemplate;

class MyInterviewsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Candidate $authCandidate;

    protected Vacancy $vacancy;

    protected InterviewTemplate $interviewTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->authCandidate = Candidate::factory()->createOne();

        $this->vacancy = Vacancy::factory()
            ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne();

        $this->actingAs($this->authCandidate, 'candidate');
    }

    /** @test  */
    public function itShouldShowEmptyDataWhenHaveNoInterviews(): void
    {
        $response = $this->get(route('candidate.interviews.my-interviews'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->count('data', 0)->etc();
        });
    }

    /** @test  */
    public function itShouldOnlyShowMyInterviews()
    {
        Interview::factory(25)->create(['candidate_id' => $this->authCandidate->getKey()]);

        Interview::factory(50)->create(['candidate_id' => Candidate::factory()->createOne()->getKey()]);

        $response = $this->get(route('candidate.interviews.my-interviews'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->count('data', 25)->etc();
        });
    }
}
