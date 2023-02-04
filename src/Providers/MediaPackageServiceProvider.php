<?php

namespace KieranFYI\Media\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use KieranFYI\Admin\Events\RegisterAdminNavigationEvent;
use KieranFYI\Media\Listeners\RegisterAdminNavigationListener;

class MediaPackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $root = realpath(__DIR__ . '/../..');
        config([
            'ziggy.groups.media' => ['admin.media.*'],
            'ziggy.groups.media-api' => ['admin.api.media.*']
        ]);

        $this->publishes([
            $root . '/public' => public_path('vendor/kieranfyi/media'),
        ], ['laravel-assets']);

        $this->loadViewsFrom($root . '/resources/views', 'media');
        $this->loadRoutesFrom($root . '/routes/web.php');

        Event::listen(RegisterAdminNavigationEvent::class, RegisterAdminNavigationListener::class);
    }
}
