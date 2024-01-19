<?php

namespace App\Organization\CandidateManagement\Controllers;

use Domain\Candidate\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Support\Controllers\Controller;

class TotalCandidatesController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'total' => Candidate::query()->count(),
            'last_updated_at' => now()->format('Y-m-d H:i')
        ]);
    }
}
