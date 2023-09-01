<?php

namespace App\Admin\CandidateManagement\Controllers;

use App\Admin\CandidateManagement\Resources\CandidateResource;
use Domain\Candidate\Models\Candidate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class CandidateController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index():AnonymousResourceCollection
    {
        return CandidateResource::collection(
            Candidate::query()->paginate(request()->integer('per_page',25))
        );
    }

    public function show($id):CandidateResource
    {
        return CandidateResource::make(
            Candidate::query()->findOrFail($id)
        );
    }

    public function store()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
