<?php

namespace Domain\Invitation\Models;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitation extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile_country_code',
        'mobile_number',
        'batch',
        'should_be_invited_at',
        'interview_template_id',
        'last_invited_at',
        'expired_at',
    ];

    protected $casts = [
        'should_be_invited_at' => 'datetime',
        'last_invited_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * @deprecated
     */
    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function invitationSent(): bool
    {
        return ! is_null($this->last_invited_at);
    }
}
