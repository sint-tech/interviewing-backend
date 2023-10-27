<?php

namespace App\Admin\InterviewManagement\Factories;

use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Requests\InterviewTemplateUpdateRequest;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateSettingsDto;
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
        $interview_template = $request->interviewTemplate()->load(['owner', 'creator']);

        $relationData = [
            'owner' => $interview_template->owner,
            'creator' => $interview_template->creator,
        ];

        if ($request->filled('question_variant_ids')) {
            $relationData['question_variants'] = QuestionVariant::query()
                ->whereKey($request->validated('question_variant_ids'))
                ->with('questionCluster')
                ->get();
        }

        $request_data = array_merge(
            Arr::except($request->validated(), 'question_variant_ids'),
            $relationData,
            $interview_template->unsetRelations()->toArray()
        );

        return InterviewTemplateDto::from($request_data);
    }

    protected function fromStoreRequest(InterviewTemplateStoreRequest $request): InterviewTemplateDto
    {
        $default_setting_values = InterviewTemplateSettingsDto::defaultValues();

        $request_data = array_merge($request->validated(), [
            'creator' => auth()->user(),
            'owner' => $request->getOwnerInstance(),
            'question_variants' => $request->questionVariants(),
            'interview_template_settings_dto' => InterviewTemplateSettingsDto::from(
                [
                    'started_at' => $request->date('settings.started_at', $default_setting_values->started_at),
                    'ended_at' => $request->date('settings.ended_at', $default_setting_values->ended_at),
                    'max_reconnection_tries' => $request->validated('settings.max_reconnection_tries', $default_setting_values->max_reconnection_tries),
                ]
            ),
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
