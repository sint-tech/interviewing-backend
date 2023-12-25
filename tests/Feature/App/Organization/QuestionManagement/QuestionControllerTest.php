<?php

namespace Tests\Feature\App\Organization\QuestionManagement;

use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Domain\QuestionManagement\Models\Question;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->seed(SintAdminsSeeder::class);

        $this->actingAs($this->employeeAuth, 'api-employee');
    }

    /** @test  */
    public function itShouldOnlyShowTheOrganizationQuestions()
    {
        Question::factory(100)->for(User::query()->first(), 'creator')->configure()->create();

        $response = $this->get(route('organization.questions.index'));
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('organization.questions.index', ['per_page' => 1000]));
        $response->assertSuccessful();
        $response->assertJsonCount(100, 'data');
    }

    /** @test  */
    public function itShouldShowSingleQuestionForThisOrganization()
    {
        Question::factory(1)->for(User::query()->first(), 'creator')->create();

        $this->get(route('organization.questions.show', Question::query()->first()))->assertSuccessful();

        $this->get(route('organization.questions.show', 2))->assertNotFound();
    }
}
