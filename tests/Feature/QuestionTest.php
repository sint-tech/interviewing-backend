<?php

namespace Tests\Feature;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        $this->seedSuperAdminAccounts();

        $this->superAdmin = User::query()->first();
    }

    public function testItShouldCreateQuestion()
    {
        $question_cluster = QuestionCluster::factory()->for($this->superAdmin, 'creator')->create();

        $default_ai_model = AIModel::query()->create();

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post('admin-api/questions', [
                'title' => 'first and last question',
                'description' => null,
                'question_cluster_id' => $question_cluster->getKey(),
                'difficult_level' => '3',
                'question_type' => 'written',
                'min_reading_duration_in_seconds' => '60',
                'max_reading_duration_in_seconds' => '120',
                'default_ai_model_id' => $default_ai_model->getKey(),
            ]);

        $response->assertCreated();
    }

    public function testItShouldUpdateQuestion(): void
    {
        $question = Question::factory()->for($this->superAdmin, 'creator')->create();

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post("admin-api/questions/{$question->getKey()}?_method=PUT", [
                'title' => 'update question',
            ]);

        $question = $question->refresh();

        $this->assertEquals($this->superAdmin, $question->creator);

        $this->assertEquals('update question', $question->title);

        $response->assertOk();
    }
}
