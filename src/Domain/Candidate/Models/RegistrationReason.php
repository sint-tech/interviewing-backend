<?php

namespace Domain\Candidate\Models;

use Database\Factories\RegistrationReasonsFactory;
use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationReason extends Model
{
    use HasFactory;

    protected $fillable = [
        "reason",
        "availability_status"
    ];

    protected $table = "registration_reasons";

    protected $casts = [
        "availability_status"   => RegistrationReasonsAvailabilityStatusEnum::class
    ];

    protected static function newFactory()
    {
        return new RegistrationReasonsFactory();
    }
}
