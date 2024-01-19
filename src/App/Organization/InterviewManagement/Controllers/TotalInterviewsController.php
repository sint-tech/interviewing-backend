<?php

namespace App\Organization\InterviewManagement\Controllers;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\JsonResponse;
use Support\Controllers\Controller;

class TotalInterviewsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'count' => Interview::query()->count(),
            'last_updated_at' => now()->format('Y-m-d H:i')
        ]);
    }
}
