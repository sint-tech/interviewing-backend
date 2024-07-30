<?php

namespace Tests\Unit\Domains\Domain\InterviewManagement\Actions;

use Mockery;
use Tests\TestCase;
use ReflectionClass;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Config;
use OpenAI\Responses\Chat\CreateResponse;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;

class SubmitInterviewQuestionAnswerActionTest extends TestCase
{
    protected int $question_variant_id;
    protected string $answer;
    protected string $vacancy_name;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->question_variant_id = 1;
        $this->answer = 'test answer';
        $this->vacancy_name = 'vacancy 1';

        Config::shouldReceive('get')
            ->with('aimodel.models.gpt-3-5-turbo.system_prompt')
            ->andReturn(json_encode([
                'is_logical' => '<true|false>',
                'correctness_rate' => '<you score for correctness rate evaluation for interviewee answer from 1 to 10>',
                'is_correct' => '<true|false>',
                'answer_analysis' => "<your analysis and justification about the interviewee's answer>",
                'english_score' => '<your score for English language evaluation for interviewee answer from 1 to 10>',
                'english_score_analysis' => "<your analysis and justification about why interviewee's answer got that english_score>",
            ]));

        Config::shouldReceive('get')
            ->with('aimodel.tries', 3)
            ->andReturn(3);
    }

    public function testCalculateAverageScore()
    {
        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $reflection = new ReflectionClass($action);
        $property = $reflection->getProperty('promptResponses');
        $property->setAccessible(true);
        $property->setValue($action, $this->promptResponseFake());

        $this->assertEquals(2, $action->calculateAverageScore());
    }

    public function testCalculateAverageEnglishScore()
    {
        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $reflection = new ReflectionClass($action);
        $property = $reflection->getProperty('promptResponses');
        $property->setAccessible(true);
        $property->setValue($action, $this->promptResponseFake());

        $this->assertEquals(4, $action->calculateAverageEnglishScore());
    }

    public function testPromptResponse()
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

        $aiPromptMock = Mockery::mock(AIPrompt::class)->makePartial();
        $aiPromptMock->model = AiModelEnum::Gpt_3_5->value;
        $aiPromptMock->system = "_RESPONSE_JSON_STRUCTURE_ _JOB_TITLE_ JSON";
        $aiPromptMock->content = "_QUESTION_TEXT_ _INTERVIEWEE_ANSWER_ JSON";

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($this->question_variant_id)
            ->andReturn($questionVariantMock);

        $expectedResult = [
            [
                "is_logical" => "true",
                "correctness_rate" => "3",
                "is_correct" => "false",
                "answer_analysis" => "The interviewee's response partially addresses the question, but there are several inaccuracies and misunderstandings in the explanation provided. It shows some awareness of the topic but lacks precise information.",
                "english_score" => "4",
                "english_score_analysis" => "The English language used is fluent and the structure of the sentences is clear. Despite the inaccuracies, the response is well-articulated and coherent.",
            ]
        ];

        $this->assertEquals($expectedResult, $action->promptResponse($this->question_variant_id, $this->answer, $this->vacancy_name));
    }

    public function testPromptResponseHandleSpaces()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '  \t\t\t\t\t {"is_logical":"false","correctness_rate":"2","is_correct":"false","answer_analysis":"The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.","english_score":"4","english_score_analysis":"The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."}'
                        ],
                    ]
                ]
            ]),
        ]);

        $aiPromptMock = Mockery::mock(AIPrompt::class)->makePartial();
        $aiPromptMock->model = AiModelEnum::Gpt_3_5->value;
        $aiPromptMock->system = "_RESPONSE_JSON_STRUCTURE_ _JOB_TITLE_ JSON";
        $aiPromptMock->content = "_QUESTION_TEXT_ _INTERVIEWEE_ANSWER_ JSON";

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($this->question_variant_id)
            ->andReturn($questionVariantMock);

        $expectedResult = [
            [
                "is_logical" => "false",
                "correctness_rate" => "2",
                "is_correct" => "false",
                "answer_analysis" => "The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.",
                "english_score" => "4",
                "english_score_analysis" => "The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."
            ]
        ];

        $this->assertEquals($expectedResult, $action->promptResponse($this->question_variant_id, $this->answer, $this->vacancy_name));
    }

    public function testPromptResponseError()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not the response we expected",
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
                                "error" => "not the response we expected",
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
                                "error" => "not the response we expected",
                            ])
                        ],
                    ]
                ]
            ]),
        ]);

        $aiPromptMock = Mockery::mock(AIPrompt::class)->makePartial();
        $aiPromptMock->model = AiModelEnum::Gpt_3_5->value;
        $aiPromptMock->system = "_RESPONSE_JSON_STRUCTURE_ _JOB_TITLE_ JSON";
        $aiPromptMock->content = "_QUESTION_TEXT_ _INTERVIEWEE_ANSWER_ JSON";

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($this->question_variant_id)
            ->andReturn($questionVariantMock);

        $expectedResult = [
            [
                "is_logical" => false,
                "correctness_rate" => 0,
                "is_correct" => false,
                "answer_analysis" => "No analysis available.",
                "english_score" => 0,
                "english_score_analysis" => "No analysis available.",
            ]
        ];

        $this->assertEquals($expectedResult, $action->promptResponse($this->question_variant_id, $this->answer, $this->vacancy_name));
    }

    public function testPromptResponseRetryAfterWrongResponse()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                "error" => "not the response we expected",
                            ])
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '  \t\t\t\t\t {"is_logical":"false","correctness_rate":"2","is_correct":"false","answer_analysis":"The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.","english_score":"4","english_score_analysis":"The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."}'
                        ],
                    ]
                ]
            ]),
        ]);

        $aiPromptMock = Mockery::mock(AIPrompt::class)->makePartial();
        $aiPromptMock->model = AiModelEnum::Gpt_3_5->value;
        $aiPromptMock->system = "_RESPONSE_JSON_STRUCTURE_ _JOB_TITLE_ JSON";
        $aiPromptMock->content = "_QUESTION_TEXT_ _INTERVIEWEE_ANSWER_ JSON";

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($this->question_variant_id)
            ->andReturn($questionVariantMock);

        $expectedResult = [
            [
                "is_logical" => "false",
                "correctness_rate" => "2",
                "is_correct" => "false",
                "answer_analysis" => "The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.",
                "english_score" => "4",
                "english_score_analysis" => "The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."
            ]
        ];

        $this->assertEquals($expectedResult, $action->promptResponse($this->question_variant_id, $this->answer, $this->vacancy_name));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    private function promptResponseFake()
    {
        return [
            [
                "is_logical" => "true",
                "correctness_rate" => "3",
                "is_correct" => "false",
                "answer_analysis" => "The interviewee's response partially addresses the question, but there are several inaccuracies and misunderstandings in the explanation provided. It shows some awareness of the topic but lacks precise information.",
                "english_score" => "4",
                "english_score_analysis" => "The English language used is fluent and the structure of the sentences is clear. Despite the inaccuracies, the response is well-articulated and coherent."
            ],
            [
                "is_logical" => "true",
                "correctness_rate" => "1",
                "is_correct" => "false",
                "answer_analysis" => "The response is logical in structure but fundamentally incorrect. The interviewee appears to have misunderstood the key concepts required to answer the question appropriately. The explanation deviates significantly from the correct information.",
                "english_score" => "3",
                "english_score_analysis" => "The language used is understandable, but there are some grammatical errors and awkward phrasing that slightly obscure the intended meaning."
            ],
            [
                "is_logical" => "false",
                "correctness_rate" => "2",
                "is_correct" => "false",
                "answer_analysis" => "The response lacks relevance and accuracy. The interviewee's answer does not align with the question, indicating a misunderstanding or a lack of knowledge on the topic. There is no logical flow to the explanation provided.",
                "english_score" => "5",
                "english_score_analysis" => "The English is excellent, with no noticeable grammatical errors and a sophisticated use of vocabulary. However, the quality of English does not compensate for the lack of relevant content."
            ]
        ];
    }
}
