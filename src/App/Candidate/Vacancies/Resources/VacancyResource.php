<?php

namespace App\Candidate\Vacancies\Resources;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Organization\SkillManagement\Resources\SkillResource;
use App\Candidate\InterviewManagement\Resources\OrganizationResource;
use App\Candidate\QuestionManagement\Resources\QuestionClusterResource;

/**
 * @property Vacancy $resource
 */
class VacancyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'started_at' => $this->started_at?->format('Y-m-d H:i'),
            'ended_at' => $this->ended_at?->format('Y-m-d H:i'),
            'description' => $this->description,
            'organization' => OrganizationResource::make($this->organization),
            'question_clusters' => QuestionClusterResource::collection($this->interviewTemplate->questionClusters),
            'skills' => SkillResource::collection($this->interviewTemplate->questionClusters->flatMap->skills->unique()),
        ];
    }
}
