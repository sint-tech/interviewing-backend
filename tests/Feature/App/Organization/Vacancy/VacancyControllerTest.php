<?php

namespace Tests\Feature\App\Organization\Vacancy;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class VacancyControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();
    }

    /** @test */
    public function itShouldShowIndexVacancies(): void
    {
        $employee = Employee::factory()->createOne();

        Vacancy::factory(15)->for($employee->organization, 'organization')->for($employee, 'creator')->create();

        $response = $this->actingAs($employee, 'api-employee')->get(route('organization-api.vacancies.index'));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            return $json->count('data', 15)
                ->has('meta')->etc();
        });
    }

    /** @test  */
    public function itShouldShowOnlyTheVacanciesBelongsToAuthOrganization(): void
    {
        $authEmployee = Employee::factory()->createOne();

        $employee = Employee::factory()->createOne();

        Vacancy::factory(5)->for($authEmployee, 'creator')->for($authEmployee->organization, 'organization')->create();

        Vacancy::factory(10)->for($employee, 'creator')->for($employee->organization, 'organization')->create();

        $this->actingAs($authEmployee, 'api-employee')
            ->get(route('organization-api.vacancies.index'))
            ->assertSuccessful()
            ->assertJsonCount(5, 'data');
    }

    /** @test  */
    public function itShouldShowJobOpportunity(): void
    {
        $employee = Employee::factory()->createOne();

        $jobOpportunity = Vacancy::factory()
            ->for($employee->organization, 'organization')
            ->for($employee, 'creator')
            ->createOne();

        $response = $this->actingAs($employee, 'api-employee')->get(route('organization-api.vacancies.show', $jobOpportunity));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) use (&$jobOpportunity) {
            return $json->where('data.id', $jobOpportunity->getKey());
        });
    }

    /** @test  */
    public function itShouldStoreNewJobOpportunity(): void
    {
        $this->assertEquals(0, Vacancy::query()->count());

        $response = $this->actingAs($employee = Employee::factory()->create(), 'api-employee')
            ->post(
                route('organization-api.vacancies.store'),
                $this->getRequestData($employee)
            );

        $response->assertCreated();

        $this->assertEquals(1, Vacancy::query()->count());
    }

    /** @test */
    public function createdJobOpportunityShouldBelongsToTheAuthEmployeeOrganization()
    {
        $this->actingAs($employee = Employee::factory()->create(), 'api-employee')
            ->post(
                route('organization-api.vacancies.store'),
                $this->getRequestData($employee)
            );

        $this->assertEquals($employee->organization->getKey(), Vacancy::query()->latest()->first()->organization->getKey());
    }

    /** @test  */
    public function itShouldDeleteVacancy(): void
    {

        $employee = Employee::factory()->create();

        Vacancy::factory(2)->for($employee, 'creator')->for($employee->organization, 'organization')->create();

        $this->freezeTime(function (Carbon $carbon) use (&$employee) {
            $this->actingAs($employee, 'api-employee')
                ->delete(route('organization-api.vacancies.destroy', Vacancy::query()->first()))
                ->assertSuccessful()
                ->assertJson(fn (AssertableJson $json) => $json->has('data.deleted_at')
                    ->where('data.deleted_at', $carbon->format('Y-m-d H:m'))
                    ->etc()
                );
        });

        $this->assertCount(1, Vacancy::query()->forUser($employee)->get());
    }

    /** @test  */
    public function itShouldNotDeleteAnyVacancyOutsideTheEmployeeOrganization(): void
    {
        $employee = Employee::factory()->create();

        Vacancy::factory()->for($employee, 'creator')->for($employee->organization)->createOne();

        $outOfScopeVacancy = Vacancy::factory()->for($otherEmployee = Employee::factory()->create(), 'creator')->for($otherEmployee->organization)->create();

        $this->actingAs($employee, 'api-employee')
            ->delete(route('organization-api.vacancies.destroy', $outOfScopeVacancy))
            ->assertNotFound();
    }

    private function getRequestData(Employee $employee): array
    {
        return [
            'title' => 'title',
            'description' => null,
            'interview_template_id' => InterviewTemplate::factory()->createOne([
                'creator_type' => $employee::class,
                'creator_id' => $employee->getKey(),
                'owner_type' => $employee::class,
                'owner_id' => $employee->getKey(),
            ])->getKey(),
            'open_positions' => 5,
            'max_reconnection_tries' => 1,
        ];
    }
}
