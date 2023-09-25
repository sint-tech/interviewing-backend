<?php

namespace Domain\Invitation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitation extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'email',
        'mobile_country_code',
        'mobile_number',
        'batch',
        'last_invited_at',
        'expired_at',
    ];

    protected $casts = [
        'last_invited_at' => 'datetime',
        'expired_at' => 'datetime',
    ];
}
