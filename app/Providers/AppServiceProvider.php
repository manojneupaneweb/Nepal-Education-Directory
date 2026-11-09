<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Institution;
use App\Models\ProjectError;
use App\Models\SearchKeyword;
use App\Observers\InstitutionSitemapObserver;
use App\Services\SEO\SeoService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Institution::observe(InstitutionSitemapObserver::class);

        View::composer('layouts.public', function ($view): void {
            if ($view->offsetExists('seo')) {
                return;
            }

            $view->with('seo', app(SeoService::class)->forView($view->getData(), request()));
        });

        View::composer('layouts.admin', function ($view): void {
            if (auth()->user()?->is_admin !== true) {
                return;
            }

            $view->with('adminCounts', [
                'admin.institutions.index' => Institution::query()->count(),
                'admin.keywords.index' => SearchKeyword::query()->count(),
                'admin.messages.index' => ContactMessage::query()->count(),
                'admin.developer.index' => ProjectError::query()->open()->count(),
            ]);
        });
    }
}
