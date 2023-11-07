<?php

namespace Tests\Feature\App\Organization\InterviewManagement;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
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
            ->for($this->employeeAuth->organization, 'owner')
            ->create();
    }

    /** @test  */
    public function itShouldStoreInterviewTemplate(): void
    {
        $this->assertCount(0, InterviewTemplate::query()->get());

        $this->actingAs($this->employeeAuth, 'api-employee')->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
            'question_variants' => [
                $this->questionVariants->first()->getKey(),
                $this->questionVariants->last()->getKey(),
            ],
        ])->assertSuccessful();

        $this->assertCount(1, InterviewTemplate::query()->get());
    }

    /** @test  */
    public function itShouldCreateInterviewTemplateForParent(): void
    {
        $this->actingAs($this->employeeAuth, 'api-employee')->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
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
