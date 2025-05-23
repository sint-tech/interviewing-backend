<?php

namespace App\Admin\InterviewManagement\Factories;

use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Requests\InterviewTemplateUpdateRequest;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InterviewTemplateDataFactory
{
    public function fromRequest(Request $request): InterviewTemplateDto
    {
        if ($request instanceof InterviewTemplateUpdateRequest) {
            return $this->fromUpdateRequest($request);
        } elseif ($request instanceof InterviewTemplateStoreRequest) {
            return $this->fromStoreRequest($request);
        }

        throw new \InvalidArgumentException('not handled request type: '.$request::class);
    }

    protected function fromUpdateRequest(InterviewTemplateUpdateRequest $request): InterviewTemplateDto
    {
        $interview_template = $request->interviewTemplate()->load(['creator']);

        $relationData = [
            'creator' => $interview_template->creator,
            'question_variants' => [],
        ];

        if ($request->filled('question_variant_ids')) {
            $relationData['question_variants'] = QuestionVariant::query()
                ->whereKey($request->validated('question_variant_ids'))
                ->with('questionCluster')
                ->get();
        }

        $request_data = array_merge(
            $relationData,
            $interview_template->unsetRelations()->toArray(),
            Arr::except($request->validated(), 'question_variant_ids'),
        );

        return InterviewTemplateDto::from($request_data);
    }

    protected function fromStoreRequest(InterviewTemplateStoreRequest $request): InterviewTemplateDto
    {
        $request_data = array_merge($request->validated(), [
            'creator' => auth()->user(),
            'question_variant_ids' => $request->questionVariants(),
            'targeted_job_title_id' => $request->validated('job_profile_id'),
        ]);

        return InterviewTemplateDto::from($request_data);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if ($name === 'fromRequest' || $name === 'from') {
            return (new self())->fromRequest($arguments[0]);
        }
    }
}
