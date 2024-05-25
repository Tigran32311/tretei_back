<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

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
        //
        Collection::macro('sortByDate', function (string $column = 'created_at', bool $descending = true) {
            return $this->sortBy(function ($datum) use ($column) {
                return strtotime(((object)$datum)->$column);
            }, SORT_REGULAR, $descending);
        });
    }
}
