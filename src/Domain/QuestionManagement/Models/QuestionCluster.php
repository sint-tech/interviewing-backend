<?php

namespace Domain\QuestionManagement\Models;

use Database\Factories\QuestionClusterFactory;
use Domain\QuestionManagement\Enums\QuestionClusterRecommendationEnum;
use Domain\Skill\Models\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class QuestionCluster extends Model
{
    use HasFactory,SoftDeletes,HasRelationships;

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
        return $this->hasMany(Question::class, 'question_cluster_id');
    }

    public function questionVariants(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->questions(), (new Question())->questionVariants());
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

    public function recommendations(): HasMany
    {
        return $this->hasMany(QuestionClusterRecommendation::class, 'question_cluster_id');
    }

    public function advices(): HasMany
    {
        return $this
            ->recommendations()
            ->where('type', QuestionClusterRecommendationEnum::Advice);
    }

    public function impacts(): HasMany
    {
        return $this
            ->recommendations()
            ->where('type', QuestionClusterRecommendationEnum::Impact);
    }

    protected static function newFactory()
    {
        return new QuestionClusterFactory();
    }
}
