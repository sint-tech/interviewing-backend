<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\DataTransferObjects\CandidateData;
use Domain\Candidate\Models\Candidate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateCandidateAction
{
    public function __construct
    (
        public CandidateData $data
    )
    {

    }

    public function execute():Candidate
    {
        $data = $this->data->toArray();

        $data["password"] = Hash::make($data['password']);

        $candidate = new Candidate($data);

        $candidate->save();

        return $candidate;
    }
}
