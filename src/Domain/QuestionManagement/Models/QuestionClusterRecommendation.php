<?php

namespace Domain\QuestionManagement\Models;

use Domain\QuestionManagement\Builders\QuestionClusterRecommendationBuilder;
use Domain\QuestionManagement\Enums\QuestionClusterRecommendationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionClusterRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_cluster_id',
        'type',
        'statement',
        'min_score',
        'max_score'
    ];

    protected $casts = [
        'type'  => QuestionClusterRecommendationEnum::class
    ];

    public function questionCluster():BelongsTo
    {
        return $this->belongsTo(QuestionCluster::class,'question_cluster_id');
    }

    public function newEloquentBuilder($query):QuestionClusterRecommendationBuilder
    {
        return new QuestionClusterRecommendationBuilder($query);
    }
}
