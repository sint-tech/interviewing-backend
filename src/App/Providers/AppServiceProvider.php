<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\Arrayable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Collection::macro('sortToOriginal', function ($ids): Collection
        {
            $ids = $ids instanceof Arrayable ? $ids->toArray() : $ids;

            $models = array_flip($ids);

            foreach ($this as $model) {
                $models[ $model->id ] = $model;
            }

            return Collection::make(array_values($models));
        });
    }
}
