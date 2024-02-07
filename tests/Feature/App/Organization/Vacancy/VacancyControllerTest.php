<?php

namespace Tests\Feature\App\Organization\Vacancy;

use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VacancyControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function itShouldShowIndexVacancies(): void
    {
        $employee = Employee::factory()->createOne();

        Vacancy::factory(15)->for($employee->organization, 'organization')->for($employee, 'creator')->create();

        $response = $this->actingAs($employee, 'organization')->get(route('organization.vacancies.index'));

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

        $this->actingAs($authEmployee, 'organization')
            ->get(route('organization.vacancies.index'))
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

        $response = $this->actingAs($employee, 'organization')->get(route('organization.vacancies.show', $jobOpportunity));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) use (&$jobOpportunity) {
            return $json->where('data.id', $jobOpportunity->getKey());
        });
    }

    /** @test  */
    public function itShouldStoreNewJobOpportunity(): void
    {
        $this->assertEquals(0, Vacancy::query()->count());

        $response = $this->actingAs($employee = Employee::factory()->create(), 'organization')
            ->post(
                route('organization.vacancies.store'),
                $this->getRequestData($employee)
            );

        $response->assertCreated();

        $this->assertEquals(1, Vacancy::query()->count());
    }

    /** @test */
    public function createdJobOpportunityShouldBelongsToTheAuthEmployeeOrganization()
    {
        $this->actingAs($employee = Employee::factory()->create(), 'organization')
            ->post(
                route('organization.vacancies.store'),
                $this->getRequestData($employee)
            );

        $this->assertEquals($employee->organization->getKey(), Vacancy::query()->latest()->first()->organization->getKey());
    }

    /** @test  */
    public function itShouldUpdateVacancy()
    {
        $employee = Employee::factory()->create();

        $updatableVacancy = Vacancy::factory()->for($employee->organization, 'organization')->create([
            'started_at' => now()->subYear()->format('Y-m-d H:i'),
        ]);

        $url = route('organization.vacancies.update', $updatableVacancy);

        $this->actingAs($employee, 'organization');

        $this->put($url)->assertSuccessful();

        $response = $this->put($url, [
            'started_at' => $startedAt = now()->addDay()->format('Y-m-d H:i'),
        ]);

        $response->assertSuccessful();

        $response->assertJsonFragment(['started_at' => $startedAt]);
    }

    /** @test  */
    public function itShouldNotUpdateVacancyAfterThisVacancyHasAnyInterview()
    {
        $employee = Employee::factory()->create();

        $updatableVacancy = Vacancy::factory()->for($employee->organization, 'organization')->create([
            'started_at' => now()->subYear()->format('Y-m-d H:i'),
        ]);

        Interview::factory(3)->create([
            'vacancy_id' => $updatableVacancy->getKey(),
            'interview_template_id' => $updatableVacancy->interviewTemplate()->value('id'),
        ]);

        $url = route('organization.vacancies.update', $updatableVacancy);

        $this->actingAs($employee, 'organization');

        $res = $this->put($url);
        $res->assertUnprocessable();
        $res->assertJsonValidationErrorFor('vacancy');
    }

    /** @test  */
    public function itShouldNotUpdateVacancyOnceItsEnded()
    {
        $employee = Employee::factory()->create();

        $updatableVacancy = Vacancy::factory()->for($employee->organization, 'organization')->create([
            'started_at' => now()->subYear()->format('Y-m-d H:i'),
            'ended_at' => now()->addDay(),
        ]);

        $this->actingAs($employee, 'organization');

        $url = route('organization.vacancies.update', $updatableVacancy);

        $this->put($url)->assertSuccessful();

        $updatableVacancy->update(['ended_at' => now()->subDay()]);
        $this->put($url)->assertUnprocessable()->assertJsonValidationErrorFor('vacancy');
    }

    /** @test  */
    public function itShouldDeleteVacancy(): void
    {
        $employee = Employee::factory()->create();

        Vacancy::factory(2)->for($employee, 'creator')->for($employee->organization, 'organization')->create();

        $this->freezeTime(function (Carbon $carbon) use (&$employee) {
            $this->actingAs($employee, 'organization')
                ->delete(route('organization.vacancies.destroy', Vacancy::query()->first()))
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

        $this->actingAs($employee, 'organization')
            ->delete(route('organization.vacancies.destroy', $outOfScopeVacancy))
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
                'organization_id' => $employee->organization_id,
            ])->getKey(),
            'open_positions' => 5,
            'max_reconnection_tries' => 1,
        ];
    }
}
