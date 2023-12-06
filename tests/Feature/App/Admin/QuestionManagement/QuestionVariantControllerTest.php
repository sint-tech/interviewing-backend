<?php

namespace Tests\Feature\App\Admin\QuestionManagement;

use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionVariantControllerTest extends TestCase
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

    /** @test */
    public function itShouldCreateQuestionVariant(): void
    {
        $this->assertDatabaseEmpty(QuestionVariant::class);

        $this->assertDatabaseEmpty(AIPrompt::class);

        $data = $this->validData();

        $this->post(route('admin.question-variants.store'), $data)
            ->assertSuccessful();

        $this->assertCount(1, QuestionVariant::query()->get());

        $this->assertCount(1, AIPrompt::query()->get());
    }

    protected function validData(): array
    {
        return [
            'text' => $this->faker->text,
            'description' => $this->faker->text,
            'question_id' => Question::factory()->for($this->sintUser, 'creator')->createOne(['system_prompt' => $this->faker->text, 'content_prompt' => $this->faker->text])->getKey(),
            'reading_time_in_seconds' => 120,
            'answering_time_in_seconds' => 340,
            'organization_id' => Organization::factory()->createOne()->getKey(),
            'ai_prompts' => [
                0 => [
                    'model' => AiModelEnum::Gpt_3_5->value,
                    'weight' => 100,
                    'status' => PromptMessageStatus::Enabled->value,
                    'content' => $this->faker->text.'_QUESTION_TEXT_ and _INTERVIEWEE_ANSWER_',
                    'system' => $this->faker->text.'_RESPONSE_JSON_STRUCTURE_',
                ],
            ],
        ];
    }
}
