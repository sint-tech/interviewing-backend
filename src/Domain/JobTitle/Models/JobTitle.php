<?php

namespace Domain\JobTitle\Models;

use Database\Factories\JobTitleFactory;
use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Support\Scopes\ForAuthScope;

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

    protected static function booted()
    {
        $scope = new ForAuthScope();

        $scope->forOrganizationEmployee(
            fn (Builder $builder) => $builder->where('availability_status', AvailabilityStatusEnum::Active)
        )->forCandidate(
            fn (Builder $builder) => $builder->where('availability_status', AvailabilityStatusEnum::Active)
        );

        static::addGlobalScope($scope);
    }
}
