<?php

namespace Database\Seeders;

use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class RecommendationsPromptTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PromptTemplate::query()->firstOrCreate(['name' => 'impacts'], [
            'text' => "You are an HR Expert, and an interviewee gave you the report they got from, you are explaining to the interviewee ther impacts of his scores based on his job profile and the scores from interviewee's report.
            Generate 3 or 4 impacts in bullet points in html format based on the scores in a professional manner.
            The interviewee is applying for _JOB_TITLE_, take that into consideration while generating the impacts based on the scores from interviewee's report.
            from interviewee's report scores
            ------------------------------",
            'stats_text' => 'you got _QUESTION_CLUSTER_AVG_SCORE_% at _QUESTION_CLUSTER_NAME_ \n',
            'conclusion_text' => 'impacts in bullets point in html format:',
            'is_selected' => true,
        ]);

        PromptTemplate::query()->firstOrCreate(['name' => 'candidate_advices'], [
            'text' => "You are an HR Expert, and an interviewee gave you the report they got from, you are giving advices based the scores from interviewee's report.
        Generate 3 or 4 Advices in bullet points in html format based on the scores in a professional manner.
        The interviewee is applying for _JOB_TITLE_, take that into consideration while evaluating the scores from interviewee's report.
        from interviewee's report scores
        ------------------------------",
            'stats_text' => 'you got _QUESTION_CLUSTER_AVG_SCORE_% at _QUESTION_CLUSTER_NAME_ \n',
            'conclusion_text' => 'HR Expert Advices in html format:',
            'is_selected' => true,
        ]);

        PromptTemplate::query()->firstOrCreate(['name' => 'recruiter_advices'], [
            'text' => 'You are an HR Expert, you are giving advices to junior recruiter about a candidate the junior recruiter wants to hire.
        give advices based the candidate scores report.
        Generate 3 or 4 Advices in bullet point based on the scores in a professional manner.
        The candidate is applying for _JOB_TITLE_, take that into consideration while evaluating the scores from candidate report.
        candidate scores report
        ------------------------------',
            'stats_text' => 'you got _QUESTION_CLUSTER_AVG_SCORE_% at _QUESTION_CLUSTER_NAME_ \n',
            'conclusion_text' => 'HR Expert Advices to the junior recruiter:',
            'is_selected' => true,
        ]);
    }
}
