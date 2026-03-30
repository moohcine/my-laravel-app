<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Intern;
use App\Observers\InternObserver;

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
        Intern::observe(InternObserver::class);
    }
}
