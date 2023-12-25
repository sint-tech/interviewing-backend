<?php

namespace Tests\Feature\App\Organization\QuestionManagement;

use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
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

        $this->seed(SintAdminsSeeder::class);

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'api-employee');
    }

    /** @test  */
    public function itShouldShowTheOrganizationQuestionVariants()
    {
        QuestionVariant::factory(30)->for($this->employeeAuth, 'creator')->create(['organization_id' => $this->employeeAuth->organization_id]);

        QuestionVariant::factory(30)->for(User::first(), 'creator')->create(['organization_id' => Organization::factory()->createOne()->getKey()]);

        $response = $this->get(route('organization.question-variants.index', ['per_page' => 1000]));
        $response->assertSuccessful();
        $response->assertJsonCount(30, 'data');
    }

    /** @test  */
    public function itShouldShowSingleQuestionVariant()
    {
        $questionVariant = QuestionVariant::factory(1)->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id]);

        $invalidQuestionVariant = QuestionVariant::factory(1)->for(User::first(), 'creator')->createOne(['organization_id' => Organization::factory()->createOne()->getKey()]);

        $this->get(route('organization.question-variants.show', $questionVariant))
            ->assertSuccessful();

        $this->get(route('organization.question-variants.show', $invalidQuestionVariant))
            ->assertNotFound();
    }

    /** @test  */
    public function itShouldStoreQuestionVariant(): void
    {
        $this->post(route('organization.question-variants.store'), [
            'text' => 'this is text',
            'description' => 'this is description',
            'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne()->getKey(),
            'reading_time_in_seconds' => 60 * 3, // 3 minutes
            'answering_time_in_seconds' => 60 * 10, // 10 minutes
        ])->assertSuccessful();
    }
}
