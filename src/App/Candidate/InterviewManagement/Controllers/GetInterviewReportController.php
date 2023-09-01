<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Exceptions\InterviewNotFinishedException;
use App\Candidate\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException as InternalInterviewNotFinishedException;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Support\Controllers\Controller;

class GetInterviewReportController extends Controller
{
    public function __invoke(Interview $interview)
    {
        try {
            return InterviewReportResource::make(
                (new InterviewReportValueObject($interview))
            );
        } catch (InternalInterviewNotFinishedException $exception) {
            throw new InterviewNotFinishedException();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
