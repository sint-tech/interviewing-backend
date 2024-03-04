<?php

namespace Tests\Unit\Support\Actions;

use Tests\TestCase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\ReportManagement\Models\InterviewReport;
use Domain\InterviewManagement\Actions\GenerateInterviewReport;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\ReportManagement\Models\Report;
use Domain\Users\Models\User;

class GenerateInterviewReportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Interview $interview;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->interview = Interview::factory()->create([
            'ended_at' => now(),
        ]);

        QuestionVariant::factory()->createOne([
            'creator_id' => User::factory()->createOne()->id,
            'creator_type' => User::class,
        ]);

        $this->interview->answers()->create([
            'question_variant_id' => 1,
            'score' => 10,
            'max_score' => 10,
        ]);

        $this->seed('RecommendationsPromptTemplateSeeder');

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Impacts: This is a test message'
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Candidate advices: This is a test message'
                        ],
                    ]
                ]
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Reqcruited advices: This is a test message'
                        ],
                    ]
                ]
            ]),
        ]);
    }

    /** @test */
    public function itShouldGeneratesAReportForAFinsishedInterview()
    {
        $report = app(GenerateInterviewReport::class)->execute($this->interview);

        $this->assertInstanceOf(Report::class, $report);
    }

    /** @test */
    public function itShouldThrowAnExceptionIfTheInterviewIsNotFinished()
    {
        $this->expectException(InterviewNotFinishedException::class);

        $this->interview->update(['ended_at' => null]);

        app(GenerateInterviewReport::class)->execute($this->interview);
    }

    /** @test */
    public function itShouldCreateAReportForTheInterview()
    {
        app(GenerateInterviewReport::class)->execute($this->interview);

        $this->assertDatabaseHas('reports', [
            'reportable_id' => $this->interview->id,
            'reportable_type' => $this->interview->getMorphClass(),
            'name' => InterviewReport::DEFAULT_REPORT_NAME,
        ]);
    }

    /** @test */
    public function itShouldCreateAReportWithTheCorrectValues()
    {
        app(GenerateInterviewReport::class)->execute($this->interview);

        $this->assertDatabaseHas('report_values', [
            'key' => 'avg_score',
            'value' => 100,
        ]);

        $this->assertDatabaseHas('report_values', [
            'key' => 'impacts',
            'value' => "[\"Impacts: This is a test message\"]",
        ]);

        $this->assertDatabaseHas('report_values', [
            'key' => 'candidate_advices',
            'value' => "[\"Candidate advices: This is a test message\"]",
        ]);
    }
}
