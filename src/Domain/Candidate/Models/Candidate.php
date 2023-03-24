<?php

namespace Domain\Candidate\Models;

use Database\Factories\CandidateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\Types\This;

class Candidate extends Authenticatable
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = "candidate";
    
    protected $fillable = [
        "first_name",
        "last_name",
        "full_name",
        "email",
        "password",
    ];

    protected static function newFactory()
    {
        return (new CandidateFactory());
    }
}
