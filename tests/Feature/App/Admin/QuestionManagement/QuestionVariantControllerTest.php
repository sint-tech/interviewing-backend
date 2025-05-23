<?php

namespace Tests\Feature\App\Admin\QuestionManagement;

use Tests\TestCase;
use Domain\Users\Models\User;
use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Domain\QuestionManagement\Models\Question;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;

class QuestionVariantControllerTest extends TestCase
{
    use DatabaseMigrations,WithFaker;

    protected User $sintUser;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test */
    public function itShouldSeeAllQuestionVariants(): void
    {
        $this->get(route('admin.question-variants.index'))
            ->assertSuccessful();
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
            'question_id' => Question::factory()->for($this->sintUser, 'creator')->createOne()->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
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
