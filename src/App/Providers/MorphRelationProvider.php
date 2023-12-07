<?php

namespace App\Providers;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Models\Interview;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphRelationProvider extends ServiceProvider
{
    protected array $morphMap = [
        'candidate' => Candidate::class,
        'admin' => User::class,
        'employee' => Employee::class,
        'interview' => Interview::class,
        'organization' => Organization::class,
        'question' => Question::class,
        'question_variant' => QuestionVariant::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::requiresMorphMap();

        Relation::enforceMorphMap(
            $this->morphMap
        );
    }
}
