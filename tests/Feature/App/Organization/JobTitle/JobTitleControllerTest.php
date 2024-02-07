<?php

namespace Tests\Feature\App\Organization\JobTitle;

use Database\Seeders\SintAdminsSeeder;
use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Domain\JobTitle\Models\JobTitle;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class JobTitleControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->seed(SintAdminsSeeder::class);

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test */
    public function itShouldShowAllJobTitles()
    {
        JobTitle::factory(50)->create(['availability_status' => AvailabilityStatusEnum::Active->value]);

        $response = $this->get(route('organization.job-titles.index'));
        $response->assertSuccessful();

        $response->assertJsonCount(25, 'data');

        JobTitle::factory(50)->create(['availability_status' => AvailabilityStatusEnum::Inactive->value]);

        $response = $this->get(route('organization.job-titles.index', ['per_page' => 100]));

        $response->assertJsonCount(50, 'data');
    }

    /** @test  */
    public function itShouldShowSingleJobTitle()
    {
        $jobTitle = JobTitle::factory(1)->createOne(['availability_status' => AvailabilityStatusEnum::Active->value]);
        $inactiveJobTitle = JobTitle::factory(1)->createOne(['availability_status' => AvailabilityStatusEnum::Inactive->value]);

        $response = $this->get(route('organization.job-titles.show', $jobTitle));
        $response->assertSuccessful();
        $response->assertJsonFragment(['title' => $jobTitle->title, 'description' => $jobTitle->description]);

        $response = $this->get(route('organization.job-titles.show', $inactiveJobTitle));
        $response->assertNotFound();

        $response = $this->get(route('organization.job-titles.show', 10012));
        $response->assertNotFound();
    }
}
