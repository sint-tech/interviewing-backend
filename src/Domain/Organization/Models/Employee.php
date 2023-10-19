<?php

namespace Domain\Organization\Models;

use Database\Factories\EmployeeFactory;
use Domain\Organization\Builders\EmployeeBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * @property Organization $organization
 */
class Employee extends Authenticatable
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'is_organization_manager',
        'organization_id',
    ];

    protected $table = 'organization_employees';

    protected $casts = [
        'is_organization_manager' => 'boolean',
        'password' => 'hashed',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    protected static function newFactory()
    {
        return new EmployeeFactory();
    }

    public function newEloquentBuilder($query)
    {
        return new EmployeeBuilder($query);
    }
}
