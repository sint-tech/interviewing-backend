<?php

namespace Tests\Unit\Domains\Domain\InterviewManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Mockery;
use PHPUnit\Framework\TestCase;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;
use Domain\QuestionManagement\Models\QuestionVariant;

class SubmitInterviewQuestionAnswerActionTest extends TestCase
{
    public function testCalculateAverageScore()
    {
        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('promptResponse')->andReturn($this->promptResponseFake());

        $this->assertEquals(2, $action->calculateAverageScore(1, 'answer'));
    }

    public function testCalculateAverageEnglishScore()
    {
        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('promptResponse')->andReturn($this->promptResponseFake());

        $this->assertEquals(4, $action->calculateAverageEnglishScore(1, 'answer'));
    }

    public function testPromptResponse()
    {
        $questionVariantId = 1;
        $answer = 'test answer';

        $aiPromptMock = Mockery::mock(AIPrompt::class);
        $aiPromptMock->shouldReceive('prompt')
            ->with('test question', $answer)
            ->andReturn('{
                "is_logical":"false",
                "correctness_rate":"2",
                "is_correct":"false",
                "answer_analysis":"The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.",
                "english_score":"4",
                "english_score_analysis":"The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."
            }');

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($questionVariantId)
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
        $this->assertEquals($expectedResult, $action->promptResponse($questionVariantId, $answer));
    }

    public function testPromptResponseMissingOpeningBrace()
    {
        $questionVariantId = 1;
        $answer = 'test answer';

        $aiPromptMock = Mockery::mock(AIPrompt::class);
        $aiPromptMock->shouldReceive('prompt')
            ->with('test question', $answer)
            ->andReturn('"is_logical":`false`,"correctness_rate":"2","is_correct":"false","answer_analysis":"The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.","english_score":"4","english_score_analysis":"The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."}');

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($questionVariantId)
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
        $this->assertEquals($expectedResult, $action->promptResponse($questionVariantId, $answer));
    }

    public function testPromptResponseHandleSpaces()
    {
        $questionVariantId = 1;
        $answer = 'test answer';

        $aiPromptMock = Mockery::mock(AIPrompt::class);
        $aiPromptMock->shouldReceive('prompt')
            ->with('test question', $answer)
            ->andReturn('  \t\t\t\t\t
            {"is_logical":"false","correctness_rate":"2","is_correct":"false","answer_analysis":"The answer provided by the interviewee is not related to the question asked. It seems like the interviewee misunderstood the question or is not sure how to respond. The response does not address the content of the question at all.","english_score":"4","english_score_analysis":"The English language used by the interviewee is clear and coherent, however, the response lacks relevance to the question. The response does not demonstrate an understanding of the task at hand."}
            ');

        $questionVariantMock = Mockery::mock(QuestionVariant::class);
        $questionVariantMock->shouldReceive('setAttribute')->andReturnNull();
        $questionVariantMock->shouldReceive('getAttribute')->with('text')->andReturn('test question');
        $questionVariantMock->shouldReceive('getAttribute')->with('aiPrompts')->andReturn(collect([$aiPromptMock]));

        $action = Mockery::mock(SubmitInterviewQuestionAnswerAction::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $action->shouldReceive('questionVariant')
            ->with($questionVariantId)
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
        $this->assertEquals($expectedResult, $action->promptResponse($questionVariantId, $answer));
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
