<?php
namespace Tests\Feature\App\Organization\CandidateManagement;

use Domain\Organization\Models\Employee;
use Domain\Candidate\Models\Candidate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TotalCandidatesControllerTest extends TestCase
{
    use DatabaseMigrations;
    public Employee $employeeAuth;

    public Vacancy $vacancy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();
        $this->vacancy = Vacancy::factory()->for($this->employeeAuth->organization, 'organization')->createOne();

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test */
    public function itShouldShowTotalCandidates(): void
    {
        Candidate::factory(15)->hasInterviews(1,[
            'vacancy_id' => $this->vacancy->id,
        ])->create();

        $response = $this->get(route('organization.candidates.count'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->has('total')
                ->has('last_updated_at');
        });

        $response->assertJsonFragment([
            'total' => 15,
        ]);
    }
}
