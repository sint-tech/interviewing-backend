<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Domain\ReportManagement\Actions\CreateReportAction;
use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\DataTransferObjects\ReportValueDto;
use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

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
                    'key' => 'advices',
                    'value' => $this->getRecommendations($interview, 'advices'),
                ]),
                ReportValueDto::from([
                    'key' => 'question_clusters_stats',
                    'value' => $this->getQuestionClustersStats($interview),
                ]),
                ReportValueDto::from([
                    'key' => 'language_fluency',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->english_score / 10) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'recruiter_advices',
                    'value' => $this->recruiterAdvices($this->getQuestionClustersStats($interview)),
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

    private function getRecommendations(Interview $interview, string $recommendation_type = 'advices' | 'impacts'): array
    {
        return match ($recommendation_type) {
            'advices' => Arr::wrap($this->getAdvices($this->getQuestionClustersStats($interview))),
            'impacts' => Arr::wrap($this->getImpacts($this->getQuestionClustersStats($interview))),
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
        $userContent = "You are an HR Expert, and an interviewee gave you the report they got from, you are explaining to the interviewee ther impacts of his scores based on his job profile and the scores from interviewee's report.
                        Generate 3 or 4 impacts in bullet points in html format based on the scores in a professional manner.
                        The interviewee is applying for {$this->interview->vacancy->interviewTemplate->jobTitle->title}, take that into consideration while generating the impacts based on the scores from interviewee's report.
                        from interviewee's report scores";

        foreach ($questionClusters as $questionClustersStat) {
            $userContent .= "you got {$questionClustersStat['avg_score']}% at {$questionClustersStat['name']} \n";
        }

        $userContent .= 'impacts in bullets point in html format:';

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userContent,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }

    protected function getAdvices(array $questionClusters): string
    {
        $userContent = "You are an HR Expert, and an interviewee gave you the report they got from, you are giving advices based the scores from interviewee's report.
        Generate 3 or 4 Advices in bullet points in html format based on the scores in a professional manner.
        The interviewee is applying for {$this->interview->vacancy->interviewTemplate->jobTitle->title}, take that into consideration while evaluating the scores from interviewee's report.\n
        from interviewee's report scores
        ------------------------------";

        foreach ($questionClusters as $questionClustersStat) {
            $userContent .= "you got {$questionClustersStat['avg_score']}% at {$questionClustersStat['name']} \n";
        }

        $userContent .= 'HR Expert Advices in html format:';

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userContent,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }

    protected function recruiterAdvices(array $questionClusters): string
    {
        $userContent = "You are an HR Expert, you are giving advices to junior recruiter about a candidate the junior recruiter wants to hire.
        give advices based the candidate scores report.
        Generate 3 or 4 Advices in bullet point based on the scores in a professional manner.
        The candidate is applying for {$this->interview->vacancy->interviewTemplate->jobTitle->title}, take that into consideration while evaluating the scores from candidate report.
        candidate scores report
        ------------------------------";

        foreach ($questionClusters as $questionClustersStat) {
            $userContent .= "you got {$questionClustersStat['avg_score']}% at {$questionClustersStat['name']} \n";
        }

        $userContent .= 'HR Expert Advices to the junior recruiter:';

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userContent,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }
}
