<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\DataTransferObjects\CandidateData;
use Domain\Candidate\Models\Candidate;
use Illuminate\Support\Facades\Hash;

class CreateCandidateAction
{
    public function __construct(
        public CandidateData $data
    ) {
    }

    public function execute(): Candidate
    {
        $data = $this->data->toArray();

        $data['password'] = Hash::make($data['password']);

        if (array_key_exists('mobile_number', $data)) {
            $data['mobile_dial_code'] = $this->data->mobile_number->dialCode;
            $data['mobile_number'] = $this->data->mobile_number->number;
        }

        $candidate = new Candidate($data);

        $candidate->save();

        return $candidate;
    }
}
