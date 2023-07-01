<?php

namespace App\Candidate\Auth\Controllers;

use App\Candidate\Auth\Requests\ValidateNewCandidateUniqueInputsRequest;
use Illuminate\Http\Response;
use Support\Controllers\Controller;

class ValidateNewCandidateUniqueInputsController extends Controller
{
    public function __invoke(
        ValidateNewCandidateUniqueInputsRequest $request
    ) {
        return response()->json(['message' => 'passed'], Response::HTTP_OK);
    }
}
