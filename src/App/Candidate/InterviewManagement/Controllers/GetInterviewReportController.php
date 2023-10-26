<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Support\Controllers\Controller;

class GetInterviewReportController extends Controller
{
    public function __invoke(Interview $interview)
    {
        try {
            return InterviewReportResource::make(
                new InterviewReportValueObject(
                    $interview
                )
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
