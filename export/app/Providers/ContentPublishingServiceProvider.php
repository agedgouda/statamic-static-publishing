<?php

namespace App\Providers;

use App\ContentPublishing\PublishOnContentChange;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ContentPublishingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(PublishOnContentChange::class);

        $this->registerNotFoundPage();
    }

    /**
     * Render the 404 template as a normal page, so the static build emits it
     * and the deploy workflow can serve it as 404.html. The page lives under
     * the APP_URL path, matching where the static build writes everything else.
     */
    protected function registerNotFoundPage(): void
    {
        if (! View::exists('errors.404')) {
            return;
        }

        $base = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');

        Route::middleware('web')->get(ltrim($base.'/404', '/'), fn () => (new \Statamic\View\View)
            ->template('errors.404')
            ->layout('layout')
            ->with(['title' => 'Page Not Found']));
    }
}
