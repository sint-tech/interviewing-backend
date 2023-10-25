<?php

namespace Tests\Feature\Organization\JobOpportunity;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\JobOpportunity;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class JobOpportunityControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();
    }

    /** @test  */
    public function itShouldShowJobOpportunity(): void
    {
        $employee = Employee::factory()->createOne();

        $jobOpportunity = JobOpportunity::factory()
            ->for($employee->organization,'organization')
            ->for($employee,'creator')
            ->createOne();

        $response = $this->actingAs($employee,'api-employee')->get(route('organization-api.job-opportunity.show',$jobOpportunity));

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) use(&$jobOpportunity){
            return $json->where('data.id',$jobOpportunity->getKey());
        });
    }

    /** @test  */
    public function itShouldStoreNewJobOpportunity(): void
    {
        $this->assertEquals(0,JobOpportunity::query()->count());

        $response = $this->actingAs($employee = Employee::factory()->create(),'api-employee')
            ->post(
                route('organization-api.job-opportunity.store'),
                $this->getRequestData($employee)
            );

        $response->assertCreated();

        $this->assertEquals(1,JobOpportunity::query()->count());
    }

    /** @test */
    public function createdJobOpportunityShouldBelongsToTheAuthEmployeeOrganization()
    {
        $this->actingAs($employee = Employee::factory()->create(),'api-employee')
            ->post(
                route('organization-api.job-opportunity.store'),
                $this->getRequestData($employee)
            );

        $this->assertEquals($employee->organization->getKey(),JobOpportunity::query()->latest()->first()->organization->getKey());
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
                'owner_id' => $employee->getKey()
            ])->getKey(),
            'open_positions' => 5,
            'max_reconnection_tries' => 1,
        ];
    }
}
