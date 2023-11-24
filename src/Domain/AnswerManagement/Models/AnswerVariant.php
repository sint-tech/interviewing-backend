<?php

namespace Domain\AnswerManagement\Models;

use Database\Factories\AnswerVariantFactory;
use Domain\InterviewManagement\Models\Answer as InterviewAnswer;
use Domain\InterviewManagement\Models\Interview;
use Domain\Organization\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Staudenmeir\EloquentHasManyDeep\HasOneDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class AnswerVariant extends Model
{
    use HasFactory,SoftDeletes,HasRelationships;

    protected $table = 'answer_variants';

    protected $fillable = [
        'text',
        'description',
        'score',
        'answer_id',
        'creator_id',
        'creator_type',
        'organization_id',
    ];

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }

    public function questionVariant(): HasOneDeep
    {
        return $this->hasOneDeepFromRelations(
            $this->answer(), (new Answer())->questionVariant()
        );
    }

    public function interviews(): BelongsToMany
    {
        $pivot_columns = Schema::getColumnListing('interview_answers');

        return $this->belongsToMany(
            Interview::class,
            'interview_answers',
            'answer_variant_id',
            'interview_id',
        )
            ->using(InterviewAnswer::class)
            ->withPivot($pivot_columns);
    }

    protected static function newFactory(): AnswerVariantFactory
    {
        return new AnswerVariantFactory();
    }
}
