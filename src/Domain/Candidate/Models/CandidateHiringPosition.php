<?php

namespace Domain\Candidate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateHiringPosition extends Model
{
    use HasFactory;

    protected $table = "candidate_desire_hiring_positions";

    protected $fillable = [
        "candidate_id",
        "job_title_id"
    ];
}
