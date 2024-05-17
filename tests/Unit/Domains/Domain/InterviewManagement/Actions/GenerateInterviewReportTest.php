<?php

namespace Tests\Unit\Domains\Domain\InterviewManagement\Actions;

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

        QuestionVariant::factory(2)->create([
            'creator_id' => User::factory()->createOne()->id,
            'creator_type' => User::class,
        ]);

        $this->interview->answers()->create([
            'question_variant_id' => 1,
            'score' => 10,
            'max_score' => 10,
            'ml_video_semantics' => '{
                "code": 200,
                "emotions": {
                    "angry": 0.9883678555488586,
                    "disgust": 1.3277789902044505e-15,
                    "fear": 2.383256833127234e-05,
                    "happy": 1.3518533847900471e-08,
                    "neutral": 2.0277220755815506e-06,
                    "sad": 0.011606342159211636,
                    "surprise": 1.5735965319414041e-13
                },
                "frames_analyzed": 1,
                "frames_total": 1,
                "model_name": "DefaultEmotion.h5",
                "state": "true"
            }'
        ]);

        $this->interview->answers()->create([
            'question_variant_id' => 2,
            'score' => 10,
            'max_score' => 10,
            'ml_video_semantics' => '{
                "code": 200,
                "emotions": {
                    "angry": 0.4783678555488586,
                    "disgust": 0.3277789902044505e-15,
                    "fear": 1.383256833127234e-05,
                    "happy": 0.3518533847900471e-08,
                    "neutral": 1.0277220755815506e-06,
                    "sad": 0.311606342159211636,
                    "surprise": 0.5735965319414041e-13
                },
                "frames_analyzed": 1,
                "frames_total": 1,
                "model_name": "DefaultEmotion.h5",
                "state": "true"
            }'
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

        $this->assertDatabaseHas('report_values', [
            'key' => 'emotional_score',
            'value' => $this->getAverageEmotionalScore(),
        ]);
    }

    /** @test */
    public function itShouldReturnAnEmptyArrayIfTheInterviewHasNoEmotionalScores()
    {
        $this->interview->answers->first()->update(['ml_video_semantics' => null]);

        app(GenerateInterviewReport::class)->execute($this->interview);

        $this->assertDatabaseHas('report_values', [
            'key' => 'emotional_score',
            'value' => '[]',
        ]);
    }

    private function getAverageEmotionalScore() {
        if ($this->interview->answers->contains(fn ($answer) => $answer->ml_video_semantics === null)) {
            return [];
        }

        $count = $this->interview->answers->count();

        $emotions = $this->interview->answers
            ->map(fn ($answer) => (array) json_decode($answer->ml_video_semantics)->emotions)
            ->reduce(function ($carry, $emotions) {
                foreach ($emotions as $emotion => $value) {
                    $carry[$emotion] = ($carry[$emotion] ?? 0) + $value;
                }

                return $carry;
            }, []);

        $averageEmotions = collect($emotions)->map(fn ($value) => $value / $count);

        return json_encode($averageEmotions->sortDesc()->take(5)->toArray());
    }
}
