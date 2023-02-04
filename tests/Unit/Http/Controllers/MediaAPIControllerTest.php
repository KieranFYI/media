<?php

namespace KieranFYI\Tests\Media\Unit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use KieranFYI\Media\Http\Controllers\MediaAPIController;
use KieranFYI\Tests\Media\TestCase;

class MediaAPIControllerTest extends TestCase
{

    /**
     * @var MediaAPIController
     */
    private MediaAPIController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new MediaAPIController();
    }

    public function testIndex()
    {
        $this->artisan('migrate');
        $response = $this->controller->index();
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}