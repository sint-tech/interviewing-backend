<?php

namespace Tests\Feature\App\Organization\QuestionManagement;

use Domain\Organization\Models\Employee;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionVariantControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'api-employee');
    }

    /** @test  */
    public function itShouldStoreQuestionVariant(): void
    {
        $this->post(route('organization.question-variants.store'), [
            'text' => 'this is text',
            'description' => 'this is description',
            'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->createOne()->getKey(),
            'reading_time_in_seconds' => 60 * 3, // 3 minutes
            'answering_time_in_seconds' => 60 * 10, // 10 minutes
        ])->assertSuccessful();
    }
}
