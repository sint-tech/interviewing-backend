<?php

namespace Domain\Organization\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Support\Interfaces\OwnerInterface;

class Organization extends Model implements OwnerInterface
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'organization_id');
    }

    public function oldestManager(): HasOne
    {
        return $this->hasOne(Employee::class, 'organization_id')
            ->where('is_organization_manager', true)
            ->oldestOfMany();
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
