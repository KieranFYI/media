<?php

namespace KieranFYI\Tests\Media\Unit\Http\Controllers;

use Illuminate\Contracts\View\View;
use KieranFYI\Media\Http\Controllers\MediaController;
use KieranFYI\Tests\Media\TestCase;

class MediaControllerTest extends TestCase
{

    /**
     * @var MediaController
     */
    private MediaController $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new MediaController();
    }

    public function testIndex()
    {
        $this->artisan('vendor:publish --tag=laravel-assets --ansi --force');
        $response = $this->controller->index();
        $this->assertInstanceOf(View::class, $response);
        $this->assertIsString($response->render());
        $this->assertStringContainsString('<media-index />', $response);
    }
}