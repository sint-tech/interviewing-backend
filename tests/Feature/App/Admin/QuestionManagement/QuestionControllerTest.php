<?php

namespace Tests\Feature\App\Admin\QuestionManagement;

use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    protected User $sintUser;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'api');
    }

    /** @test  */
    public function itShouldStoreQuestion(): void
    {
        $this->post(
            route('admin.questions.store'),
            $this->requestData()
        )->assertSuccessful();
    }

    /** @test */
    public function itShouldUpdateQuestion()
    {
        $question = Question::factory()
            ->for($this->sintUser, 'creator')
            ->for(QuestionCluster::factory()->for($this->sintUser, 'creator')->createOne())
            ->createOne();

        $question->defaultAIPrompt()->create([
            'content' => $this->faker->text(244),
            'system' => $this->faker->text(244),
            'weight' => 100,
            'model' => AiModelEnum::Gpt_3_5,
            'status' => PromptMessageStatus::Enabled,
        ]);

        $this->put(route('admin.questions.update', $question))
            ->assertSuccessful();
    }

    protected function requestData(array $merged_data = []): array
    {
        $data = [
            'title' => $this->faker->text(64),
            'description' => $this->faker->realText(100),
            'creator_id' => $this->sintUser->getKey(),
            'creator_type' => $this->sintUser::class,
            'question_cluster_id' => QuestionCluster::factory()->for($this->sintUser, 'creator')->createOne()->getKey(),
            'question_type' => 'written',
            'difficult_level' => '10',
            'min_reading_duration_in_seconds' => 120,
            'max_reading_duration_in_seconds' => 360,
            'ai_prompt' => [
                'model' => AiModelEnum::Gpt_3_5->value,
                'content' => 'question is: _QUESTION_TEXT_ , and the interviewee answer is: _INTERVIEWEE_ANSWER_',
                'system' => 'Im interviewer and asking the next content question, please provide answer as: _RESPONSE_JSON_STRUCTURE_',
            ],
        ];

        return array_merge($data, $merged_data);
    }
}
