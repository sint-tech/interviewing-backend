<?php
namespace Tests\Feature\App\Organization\Vacancy;

use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TotalVacanciesControllerTest extends TestCase
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
    public function itShouldShowTotalVacancies(): void
    {
        Vacancy::factory(15)->for($this->employeeAuth->organization, 'organization')->for($this->employeeAuth, 'creator')->create();

        $response = $this->get(route('organization.vacancies.count'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->has('count')
                ->has('last_updated_at');
        });

        $response->assertJsonFragment([
            'count' => 15,
        ]);
    }
}
