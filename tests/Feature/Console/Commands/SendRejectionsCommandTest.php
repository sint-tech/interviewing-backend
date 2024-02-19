<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\SendRejectionsCommand;
use App\Mail\Candidate\CandidateRejectedMail;
use Database\Seeders\SintAdminsSeeder;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendRejectionsCommandTest extends TestCase
{
    use DatabaseMigrations,WithFaker;

    protected Vacancy $vacancy;

    protected InterviewTemplate $interviewTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $creator = User::query()->first();

        QuestionVariant::factory()->hasAttached(
            $this->interviewTemplate = InterviewTemplate::factory()->for($creator, 'creator')->createOne()
        )->count(10)->for($creator, 'creator')->create();

        $this->vacancy = Vacancy::factory()->for($creator, 'creator')->createOne([
            'interview_template_id' => $this->interviewTemplate->id,
            'open_positions' => 5,
            'ended_at' => now(),
        ]);
    }

    /** @test  */
    public function itShouldOnlySendMailsForRejectedCandidates()
    {
        //top interviews
        Interview::factory()->count(5)
            ->create([
                'status' => InterviewStatusEnum::Passed,
                'vacancy_id' => $this->vacancy->id,
                'interview_template_id' => $this->interviewTemplate->id,
                'ended_at' => now()->subDay(),
            ])->each(function (Interview $interview) {
                Answer::query()->create([
                    'question_occurrence_reason' => QuestionOccurrenceReasonEnum::TemplateQuestion,
                    'answer_text' => $this->faker->text(300),
                    'score' => $this->faker->numberBetween(8, 10),
                    'min_score' => 10,
                    'max_score' => 5,
                    'interview_id' => $interview->id,
                    'question_variant_id' => $interview->questionVariants->first()->id,
                    'question_cluster_id' => null,
                    'ml_video_semantics' => null,
                    'ml_audio_semantics' => null,
                    'ml_text_semantics' => null,
                ]);
            });

        //passed interviews
        Interview::factory()->count(5)
            ->create([
                'status' => InterviewStatusEnum::Passed,
                'vacancy_id' => $this->vacancy->id,
                'interview_template_id' => $this->interviewTemplate->id,
                'ended_at' => now()->subDay(),
            ])->each(function (Interview $interview) {
                Answer::query()->create([
                    'question_occurrence_reason' => QuestionOccurrenceReasonEnum::TemplateQuestion,
                    'answer_text' => $this->faker->text(300),
                    'score' => $this->faker->numberBetween(5, 7),
                    'min_score' => 10,
                    'max_score' => 5,
                    'interview_id' => $interview->id,
                    'question_variant_id' => $interview->questionVariants->first()->id,
                    'question_cluster_id' => null,
                    'ml_video_semantics' => null,
                    'ml_audio_semantics' => null,
                    'ml_text_semantics' => null,
                ]);
            });

        //failed interviews
        Interview::factory()->count(5)
            ->create([
                'status' => InterviewStatusEnum::Rejected,
                'vacancy_id' => $this->vacancy->id,
                'interview_template_id' => $this->interviewTemplate->id,
                'ended_at' => now()->subDay(),
            ])->each(function (Interview $interview) {
                Answer::query()->create([
                    'question_occurrence_reason' => QuestionOccurrenceReasonEnum::TemplateQuestion,
                    'answer_text' => $this->faker->text(300),
                    'score' => $this->faker->numberBetween(1, 3),
                    'min_score' => 10,
                    'max_score' => 5,
                    'interview_id' => $interview->id,
                    'question_variant_id' => $interview->questionVariants->first()->id,
                    'question_cluster_id' => null,
                    'ml_video_semantics' => null,
                    'ml_audio_semantics' => null,
                    'ml_text_semantics' => null,
                ]);
            });

        $this->artisan(SendRejectionsCommand::class)
            ->assertSuccessful();

        Mail::assertSent(CandidateRejectedMail::class, function (CandidateRejectedMail $mail) {
            $mail->assertSeeInHtml($this->vacancy->organization->name);

            return true;
        });

        Mail::assertSentCount(5);

        $this->artisan(SendRejectionsCommand::class)
            ->assertSuccessful();

        Mail::assertSentCount(5);
    }
}
