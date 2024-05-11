<?php

namespace Domain\Organization\Models;

use Database\Factories\OrganizationFactory;
use Domain\Invitation\Models\Invitation;
use Domain\Organization\Enums\OrganizationEmployeesRangeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Support\Interfaces\OwnerInterface;
use Domain\Vacancy\Models\Vacancy;

/**
 * @property OrganizationEmployeesRangeEnum $number_of_employees
 */
class Organization extends Model implements OwnerInterface, HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'website_url',
        'address',
        'contact_email',
        'industry',
        'number_of_employees',
        'limit',
        'interview_consumption',
    ];

    protected $casts = [
        'number_of_employees' => OrganizationEmployeesRangeEnum::class,
    ];

    public function logos(): MorphMany
    {
        return $this->media()->where('collection_name', 'logo');
    }

    public function logo(): MorphOne
    {
        return $this
            ->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', 'logo')
            ->latestOfMany();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'organization_id');
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'organization_id');
    }

    public function oldestManager(): HasOne
    {
        return $this->hasOne(Employee::class, 'organization_id')
            ->where('is_organization_manager', true)
            ->oldestOfMany();
    }

    public function limitExceeded(): bool
    {
        $vacancies = $this->vacancies()->whereEnded()->pluck('id');

        $invitations = Invitation::query()
            ->whereIn('vacancy_id', $vacancies)
            ->get();

        $used_invitations = $invitations->whereNotNull('used_at')->count();
        $unused_invitations = $invitations->whereNull('used_at')->count();

        $rest = $used_invitations - $unused_invitations;

        return $this->limit <= $this->interview_consumption - $rest;
    }

    public function invitationsLeft(): int
    {
        return $this->limit - $this->interview_consumption;
    }

    protected static function newFactory()
    {
        return new OrganizationFactory();
    }

    public function organizationName(): string
    {
        return $this->name;
    }
}
