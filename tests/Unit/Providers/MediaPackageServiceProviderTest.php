<?php

namespace KieranFYI\Tests\Media\Unit\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use KieranFYI\Admin\Events\RegisterAdminNavigationEvent;
use KieranFYI\Media\Listeners\RegisterAdminNavigationListener;
use KieranFYI\Media\Providers\MediaPackageServiceProvider;
use KieranFYI\Tests\Media\TestCase;

class MediaPackageServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBootConfig()
    {
        $admin = config('ziggy.groups.media');
        $this->assertIsArray($admin);
        $this->assertEquals(['admin.media.*'], $admin);
        $api = config('ziggy.groups.media-api');
        $this->assertIsArray($api);
        $this->assertEquals(['admin.api.media.*'], $api);
    }

    public function testPublishes()
    {
        $this->assertEquals([
            realpath(__DIR__ . '/../../..') . '/public' => public_path('vendor/kieranfyi/media'),
        ], ServiceProvider::$publishes[MediaPackageServiceProvider::class]);
    }

    public function testLoadViewsFrom()
    {
        $this->assertTrue(View::exists('media::index'));
    }

    public function testLoadRoutesFrom()
    {
        $this->assertTrue(Route::has('admin.media.index'));
    }

    public function testEventListen()
    {
        $this->assertContains(RegisterAdminNavigationListener::class, Event::getRawListeners()[RegisterAdminNavigationEvent::class]);
    }
}