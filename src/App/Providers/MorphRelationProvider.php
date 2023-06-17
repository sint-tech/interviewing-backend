<?php

namespace App\Providers;

use Domain\Candidate\Models\Candidate;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphRelationProvider extends ServiceProvider
{
    protected array $morphMap = [
        'candidate' => Candidate::class,
        'admin'   =>  User::class,
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
