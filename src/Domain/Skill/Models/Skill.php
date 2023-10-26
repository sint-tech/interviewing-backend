<?php

namespace Domain\Skill\Models;

use Database\Factories\SkillFactory;
use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected static function newFactory()
    {
        return new SkillFactory();
    }

    public function questionClusters(): BelongsToMany
    {
        return $this->belongsToMany(QuestionCluster::class, 'question_cluster_skill');
    }
}
