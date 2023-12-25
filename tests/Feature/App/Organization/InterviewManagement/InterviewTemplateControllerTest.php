<?php

namespace Tests\Feature\App\Organization\InterviewManagement;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\JobTitle\Models\JobTitle;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class InterviewTemplateControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public Employee $employeeAuth;

    public Collection $questionVariants;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->questionVariants = QuestionVariant::factory(5)
            ->for($this->employeeAuth, 'creator')
            ->for($this->employeeAuth->organization, 'organization')
            ->create();

        $this->actingAs($this->employeeAuth, 'api-employee');
    }

    /** @test  */
    public function itShouldShowOnlyInterviewTemplateBelongsToTheAuthOrganization()
    {
        InterviewTemplate::factory(25)->create([
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        InterviewTemplate::factory(30)->create([
            'organization_id' => Organization::factory()->createOne()->getKey(),
        ]);

        $response = $this->get(route('organization.interview-templates.index'), ['per_page' => 1000]);
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');
    }

    /** @test  */
    public function itShouldShowSingleInterviewTemplateBelongsToAuthOrganization()
    {
        $organizationInterviewTemplate = InterviewTemplate::factory()->createOne(['organization_id' => $this->employeeAuth->organization_id]);
        $otherInterviewTemplate = InterviewTemplate::factory()->createOne(['organization_id' => Organization::factory()->createOne()->getKey()]);

        $response = $this->get(route('organization.interview-templates.show', $organizationInterviewTemplate));
        $response->assertSuccessful();

        $this->get(route('organization.interview-templates.show', $otherInterviewTemplate))->assertNotFound();
    }

    /** @test  */
    public function itShouldStoreInterviewTemplate(): void
    {
        $this->assertCount(0, InterviewTemplate::query()->get());

        $this->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
            'job_profile_id' => JobTitle::factory()->createOne()->getKey(),
            'question_variants' => [
                $this->questionVariants->first()->getKey(),
                $this->questionVariants->last()->getKey(),
            ],
        ])->assertSuccessful();

        $this->assertCount(1, InterviewTemplate::query()->get());
    }

    /** @test  */
    public function itShouldCreateInterviewTemplateForParentInterviewTemplate(): void
    {
        $this->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
            'job_profile_id' => JobTitle::factory()->createOne()->getKey(),
            'question_variants' => [
                $this->questionVariants->first()->getKey(),
                $this->questionVariants->last()->getKey(),
            ],
            'parent_id' => InterviewTemplate::factory()->create([
                'organization_id' => $this->employeeAuth->organization_id,
            ])->getKey(),
        ])->assertSuccessful();

        $this->assertNotNull(InterviewTemplate::query()->latest('id')->first()->parent_id);
    }
}
