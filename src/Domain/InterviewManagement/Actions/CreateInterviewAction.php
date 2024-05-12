<?php

namespace Domain\InterviewManagement\Actions;

use Exception;
use Illuminate\Support\Facades\DB;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;

class CreateInterviewAction
{
    /**
     * @throws Exception
     */
    public function execute(InterviewDto $interviewDto): Interview
    {
        return DB::transaction(function () use ($interviewDto) {
            $this->noInterviewsRunningForCandidate($interviewDto->candidate_id);

            $interview = (new Interview())->fill($interviewDto->toArray());

            $interview->save();

            $interview->setInvitationUsed();

            $interview->refresh()->load('questionVariants');

            return $interview;
        });
    }

    /**
     * @throws Exception
     */
    protected function noInterviewsRunningForCandidate(int $candidateId): void
    {
        if (app(HasRunningInterviewsForCandidateAction::class)->execute($candidateId)) {
            throw new Exception(
                sprintf('can\'t create or start an interview until the running interview for candidate with id: %s is finished', $candidateId)
            );
        }
    }
}
