<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

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
        Paginator::useBootstrapFive();

        View::composer('public.partials.footer', function ($view) {
            $view->with('footerCategories',
                Category::whereHas('articles', fn ($q) => $q->published())
                    ->orderBy('name')->limit(6)->get(['id', 'name', 'slug']));
        });

        Carbon::setLocale(config('app.locale'));
    }
}
