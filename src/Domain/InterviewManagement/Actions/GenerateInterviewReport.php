<?php

namespace Domain\InterviewManagement\Actions;

use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Domain\ReportManagement\Actions\CreateReportAction;
use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\DataTransferObjects\ReportValueDto;
use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Support\ValueObjects\PromptMessage;

class GenerateInterviewReport
{
    protected Interview $interview;

    public function __construct(
    ) {
    }

    public function execute(
        Interview $interview
    ): InterviewReportValueObject {
        if ($this->interviewStillRunning($interview)) {
            throw new InterviewNotFinishedException();
        }

        $this->interview = $interview;

        $reportDto = ReportDto::from([
            'name' => InterviewReport::DEFAULT_REPORT_NAME,
            'reportable' => $interview,
            'values' => [
                ReportValueDto::from([
                    'key' => 'avg_score',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->score / $answer->max_score) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'impacts',
                    'value' => $this->getRecommendations($interview, 'impacts'),
                ]),
                ReportValueDto::from([
                    'key' => 'candidate_advices',
                    'value' => $this->getRecommendations($interview, 'candidate_advices'),
                ]),
                ReportValueDto::from([
                    'key' => 'question_clusters_stats',
                    'value' => $this->getQuestionClustersStats($interview),
                ]),
                ReportValueDto::from([
                    'key' => 'language_fluency_score',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->english_score / 10) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'recruiter_advices',
                    'value' => $this->getRecommendations($interview, 'recruiter_advices'),
                ]),
            ],
        ]);

        app(CreateReportAction::class)->execute($reportDto);

        return new InterviewReportValueObject($interview);
    }

    public function interviewStillRunning(Interview $interview): bool
    {
        return is_null($interview->ended_at);
    }

    private function getRecommendations(Interview $interview, string $recommendation_type): array
    {
        return match ($recommendation_type) {
            'candidate_advices' => Arr::wrap($this->getCandidateAdvices($this->getQuestionClustersStats($interview))),
            'impacts' => Arr::wrap($this->getImpacts($this->getQuestionClustersStats($interview))),
            'recruiter_advices' => Arr::wrap($this->getRecruiterAdvices($this->getQuestionClustersStats($interview))),
        };
    }

    public function getQuestionClustersStats(Interview $interview): array
    {
        $clustersStats = [];

        foreach ($interview->answers as $answer) {
            if (is_null($answer->questionCluster)) {
                continue;
            }

            $clustersStats[$answer->question_cluster_id] = [
                'total_scores' => +($answer->score / $answer->max_score),
                'name' => $answer->questionCluster->name,
                'description' => $answer->questionCluster->description,
            ];
        }

        array_walk($clustersStats, function (&$question_cluster) {
            $question_cluster['avg_score'] = $question_cluster['total_scores'] * 100;
            unset($question_cluster['total_scores']);
        });

        return $clustersStats;
    }

    private function getImpacts(array $questionClusters): string
    {
        $impact_template = PromptTemplate::query()->latestTemplateOrFail('impacts');

        //todo use @PromptMessage valueObject
        $impact_template_content = str_replace(
            [PromptTemplateVariableEnum::JobTitle->value],
            [$this->interview->vacancy->interviewTemplate->jobTitle->title],
            $impact_template->text
        );

        foreach ($questionClusters as $questionClustersStat) {
            $impact_template_content .= str_replace([
                PromptTemplateVariableEnum::QuestionClusterName->value,
                PromptTemplateVariableEnum::QuestionClusterAvgScore->value,
            ], [
                $questionClustersStat['name'],
                $questionClustersStat['avg_score'],
            ], $impact_template->stats_text);
        }

        $impact_template_content .= $impact_template->conclusion_text;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $impact_template_content,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }

    protected function getCandidateAdvices(array $questionClusters): string
    {
        $candidate_template = PromptTemplate::query()->latestTemplateOrFail('candidate_advices');

        $candidate_template_content = str_replace(
            [PromptTemplateVariableEnum::JobTitle->value],
            [$this->interview->vacancy->interviewTemplate->jobTitle->title],
            $candidate_template->text
        );

        foreach ($questionClusters as $questionClustersStat) {
            $candidate_template_content .= str_replace([
                PromptTemplateVariableEnum::QuestionClusterName->value,
                PromptTemplateVariableEnum::QuestionClusterAvgScore->value,
            ], [
                $questionClustersStat['name'],
                $questionClustersStat['avg_score'],
            ], $candidate_template->stats_text);
        }

        $candidate_template_content .= $candidate_template->conclusion_text;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $candidate_template_content,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }

    protected function getRecruiterAdvices(array $questionClusters): string
    {
        $recruiter_template = PromptTemplate::query()->latestTemplateOrFail('recruiter_advices');

        $recruiter_template_content = str_replace(
            [PromptTemplateVariableEnum::JobTitle->value],
            [$this->interview->vacancy->interviewTemplate->jobTitle->title],
            $recruiter_template->text
        );

        foreach ($questionClusters as $questionClustersStat) {
            $recruiter_template_content .= str_replace([
                PromptTemplateVariableEnum::QuestionClusterName->value,
                PromptTemplateVariableEnum::QuestionClusterAvgScore->value,
            ], [
                $questionClustersStat['name'],
                $questionClustersStat['avg_score'],
            ], $recruiter_template->stats_text);
        }

        $recruiter_template_content .= $recruiter_template->conclusion_text;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $recruiter_template_content,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }
}
