<?php

namespace Domain\Candidate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateRegistrationReason extends Model
{
    use HasFactory;

    protected $fillable = [
        "candidate_id",
        "registration_reason_id"
    ];
}
