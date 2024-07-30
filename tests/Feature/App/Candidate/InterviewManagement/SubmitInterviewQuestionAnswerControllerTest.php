<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use OpenAI\Laravel\Facades\OpenAI;
use Database\Seeders\SintAdminsSeeder;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use OpenAI\Responses\Chat\CreateResponse;
use Domain\QuestionManagement\Models\Question;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;

class SubmitInterviewQuestionAnswerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Candidate $candidate;
    protected Vacancy $vacancy;
    protected InterviewTemplate $interview_template;
    protected Interview $interview;

    protected function setUp(): void
    {
        parent::setUp();

        $this->candidate = Candidate::factory()->createOne();

        $this->seed(SintAdminsSeeder::class);

        $question_cluster = QuestionCluster::factory()->create([
            'creator_type' => 'organization',
            'creator_id' => $employee = Employee::factory()->createOne(),
        ]);

        $question = Question::factory()->create([
            'question_cluster_id' => $question_cluster->id,
            'creator_type' => 'organization',
            'creator_id' => $employee->organization_id,
        ]);

        $question_variant = QuestionVariant::factory()->create([
            'question_id' => $question->id,
            'creator_type' => 'organization',
            'creator_id' => $employee->id,
            'organization_id' => $employee->organization_id,
        ]);

        $question_variant->aiPrompts()->create([
            'system' => '_RESPONSE_JSON_STRUCTURE_ _JOB_TITLE_ JSON',
            'content' => 'interviewer: _QUESTION_TEXT_.
                        interviewee: _INTERVIEWEE_ANSWER_ JSON',
            'weight' => 100,
            'model' => AiModelEnum::Gpt_3_5->value,
            'status' => 'enabled',
        ]);

        $this->interview_template = InterviewTemplate::factory()
            ->createOne();

        $this->interview_template->questionVariants()->attach($question_variant, [
            'question_cluster_id' => $question_variant->question->questionCluster->id,
        ]);

        $this->vacancy = Vacancy::factory()
            ->for($this->interview_template, 'defaultInterviewTemplate')
            ->for($employee, 'creator')
            ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0, 'started_at' => now()->subDay(), 'ended_at' => now()->addDay()]);

        $this->interview = Interview::factory([
            'status' => InterviewStatusEnum::Started->value,
        ])
            ->for($this->interview_template)
            ->for($this->vacancy)
            ->for($this->candidate)
            ->create();

        $this->actingAs($this->candidate, 'candidate');
    }

    /** @test */
    public function itShouldSubmitInterviewQuestionAnswer()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "is_logical" => "true",
                                "correctness_rate" => "3",
                                "is_correct" => "false",
                                "answer_analysis" => "The interviewee's response partially addresses the question, but there are several inaccuracies and misunderstandings in the explanation provided. It shows some awareness of the topic but lacks precise information.",
                                "english_score" => "4",
                                "english_score_analysis" => "The English language used is fluent and the structure of the sentences is clear. Despite the inaccuracies, the response is well-articulated and coherent."
                            ])
                        ],
                    ]
                ]
            ]),
        ]);

        $this->post(route('candidate.interviews.submit-answer', $this->interview->id), [
            'question_occurrence_reason' => QuestionOccurrenceReasonEnum::TemplateQuestion->value,
            'question_variant_id' => $this->interview_template->questionVariants()->first()->id,
            'answer_text' => 'answer test',
        ])->assertSuccessful();

        $this->assertDatabaseCount('interview_answers', 1);

        $answer = $this->interview->answers()->first();
        $raw_prompt_response = collect(json_decode($answer->pluck('raw_prompt_response')->first(), true))->map(fn ($value) => json_decode($value, true));

        $this->assertEquals($raw_prompt_response->toArray(), [[
            "is_logical" => "true",
            "correctness_rate" => "3",
            "is_correct" => "false",
            "answer_analysis" => "The interviewee's response partially addresses the question, but there are several inaccuracies and misunderstandings in the explanation provided. It shows some awareness of the topic but lacks precise information.",
            "english_score" => "4",
            "english_score_analysis" => "The English language used is fluent and the structure of the sentences is clear. Despite the inaccuracies, the response is well-articulated and coherent."
        ]]);
        $this->assertEquals($answer->english_score, 4);
        $this->assertEquals($answer->score, 3);
    }

    /** @test */
    public function itShouldTryThreeTimesIfResponsIsNotValid()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not expected response",
                            ])
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not expected response",
                            ])
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not expected response",
                            ])
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not expected response",
                            ])
                        ],
                    ]
                ]
            ]),
        ]);

        $this->post(route('candidate.interviews.submit-answer', $this->interview->id), [
            'question_occurrence_reason' => QuestionOccurrenceReasonEnum::TemplateQuestion->value,
            'question_variant_id' => $this->interview_template->questionVariants()->first()->id,
            'answer_text' => 'answer test',
        ])->assertSuccessful();

        $this->assertDatabaseCount('interview_answers', 1);

        $answer = $this->interview->answers()->first();
        $raw_prompt_response = collect(json_decode($answer->pluck('raw_prompt_response')->first(), true))->map(fn ($value) => json_decode($value, true));
    }
}
