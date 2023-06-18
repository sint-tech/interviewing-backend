<?php

namespace Domain\QuestionManagement\Models;

use Domain\Skill\Models\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QuestionCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'creator_type',
    ];

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class,'question_cluster_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'question_cluster_skill',
            'question_cluster_id',
            'skill_id',
        )->withTimestamps();
    }
}
