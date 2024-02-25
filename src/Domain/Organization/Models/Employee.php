<?php

namespace Domain\Organization\Models;

use App\Organization\Auth\Notifications\ResetPasswordNotification;
use Database\Factories\EmployeeFactory;
use Domain\Organization\Builders\EmployeeBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Support\Scopes\ForAuthScope;

/**
 * @property Organization $organization
 */
class Employee extends Authenticatable
{
    use HasFactory,SoftDeletes,HasApiTokens, Notifiable;

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

    protected $hidden = [
        'password',
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

    protected static function booted()
    {
        parent::addGlobalScope(ForAuthScope::make()->forOrganizationEmployee(fn (EmployeeBuilder $builder) => $builder->forAuth()));
    }

    public function sendPasswordResetNotification($token): void
{
    $url = url(config('app.organization_website_url') . '/reset-password?token=' . $token . '&email=' . $this->getEmailForPasswordReset());

    $this->notify(new ResetPasswordNotification(urldecode($url), $token));
}
}
