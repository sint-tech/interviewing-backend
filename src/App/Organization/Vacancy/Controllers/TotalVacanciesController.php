<?php

namespace App\Organization\Vacancy\Controllers;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Support\Controllers\Controller;

class TotalVacanciesController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'count' => Vacancy::query()->count(),
            'last_updated_at' => now()->format('Y-m-d H:i')
        ]);
    }
}
