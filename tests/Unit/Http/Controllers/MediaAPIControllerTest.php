<?php

namespace KieranFYI\Tests\Media\Unit\Http\Controllers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\UploadedFile;
use KieranFYI\Media\Core\Facades\MediaStorage;
use KieranFYI\Roles\Core\Http\Middleware\HasPermission;
use KieranFYI\Tests\Media\TestCase;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;

class MediaAPIControllerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex()
    {
        $this->artisan('migrate');
        $response = $this->withoutMiddleware()
            ->get(route('admin.api.media.index'))
            ->json();
        $this->assertIsArray($response);
    }

    public function testStore()
    {
        $this->artisan('migrate');
        $response = $this->withoutMiddleware()
            ->post(route('admin.api.media.store'), [
                'files' => [
                    UploadedFile::fake()->image('test.png')
                ]
            ])
            ->json();
        $this->assertIsArray($response);
        $this->assertEquals('test.png', $response[0]['file_name']);
    }

    public function testShow()
    {
        $this->artisan('migrate');
        $media = MediaStorage::store(UploadedFile::fake()->image('test.png'));
        $response = $this
            ->withoutMiddleware([
                HasPermission::class,
                Authenticate::class,
                VerifyCsrfToken::class,
                \Orchestra\Testbench\Http\Middleware\Authenticate::class,
                Authorize::class,
            ])
            ->get(route('admin.api.media.show', $media))
            ->json();
        $this->assertEquals($media->toArray(), $response);
    }

    public function testDestroy()
    {
        $this->artisan('migrate');
        $media = MediaStorage::store(UploadedFile::fake()->image('test.png'));
        $response = $this
            ->withoutMiddleware([
                HasPermission::class,
                Authenticate::class,
                VerifyCsrfToken::class,
                \Orchestra\Testbench\Http\Middleware\Authenticate::class,
                Authorize::class,
            ])
            ->delete(route('admin.api.media.destroy', $media))
            ->json();
        $this->assertIsArray($response);
        $this->assertEmpty($response);
        $this->assertTrue($media->refresh()->trashed());
    }

    public function testSearch()
    {
        $this->artisan('migrate');
        $media = MediaStorage::store(UploadedFile::fake()->image('test.png'));
        $response = $this
            ->withoutMiddleware([
                HasPermission::class,
                Authenticate::class,
                VerifyCsrfToken::class,
                \Orchestra\Testbench\Http\Middleware\Authenticate::class,
                Authorize::class,
            ])
            ->post(route('admin.api.media.search', $media), [
                'search' => 'test.png'
            ])
            ->json();
        $this->assertIsArray($response);
        $this->assertNotEmpty($response['data']);
        $this->assertCount(1, $response['data']);
        $this->assertEquals('test.png', $response['data'][0]['file_name']);
    }
}