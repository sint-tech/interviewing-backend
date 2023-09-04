<?php

namespace Feature;

use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionVariantTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        $this->seedSuperAdminAccounts();

        $this->superAdmin = User::query()->first();
    }

    public function testItShouldCreateQuestionVariant(): void
    {
        $question = Question::factory()->for($this->superAdmin,'creator')->create();

        $response = $this->actingAs($this->superAdmin,'api')
            ->post('admin-api/question-variants',[
               'text'  => 'question variant title',
               'description'    => 'question variant description',
               'question_id'  => $question->getKey(),
               'reading_time_in_seconds'    => 3,
               'answering_time_in_seconds'    => 30,
               'owner'  => [
                   'model_id'   => 1,
                   'model_type' => 'admin'
               ],
            ]);

        $response->assertCreated();
    }

    public function testItShouldUpdateQuestionVariant(): void
    {
        $question = Question::factory()->for($this->superAdmin, 'creator')->create();

        $questionVariant = QuestionVariant::factory()
            ->for($this->superAdmin, 'creator')
            ->for($this->superAdmin, 'owner')
            ->for($question, 'question')
            ->create();

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post("admin-api/question-variants/{$questionVariant->getKey()}?_method=PUT", [
                'text' => 'new question variant',
            ]);

        $this->assertSame('new question variant', $questionVariant->refresh()->text);

        $response->assertOk();
    }
}
