<?php

namespace KieranFYI\Tests\Media\Unit\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use KieranFYI\Media\Core\Facades\MediaStorage;
use KieranFYI\Media\Http\Controllers\MediaController;
use KieranFYI\Tests\Media\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

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



    public function testShow()
    {
        $this->artisan('migrate');
        $media = MediaStorage::store(UploadedFile::fake()->image('test.png'));
        $response = $this->controller->show($media->versions->first(), 'png');
        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

}