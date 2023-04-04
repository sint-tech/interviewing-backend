<?php

namespace Domain\JobTitle\Models;

use Database\Factories\JobTitleFactory;
use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'availability_status',
    ];

    protected $casts = [
        'availability_status' => AvailabilityStatusEnum::class,
    ];

    protected static function newFactory(): JobTitleFactory
    {
        return new JobTitleFactory();
    }
}
