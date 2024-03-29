<?php

use Illuminate\Support\Facades\Route;
use KieranFYI\Admin\Facades\Admin;
use KieranFYI\Media\Http\Controllers\MediaAPIController;
use KieranFYI\Media\Http\Controllers\MediaController;

Admin::route(function () {
    Route::resource('media', MediaController::class)
        ->parameter('medium', 'media')
        ->only('index');
});

Admin::api(function () {
    Route::resource('media', MediaAPIController::class)
        ->parameter('media', 'media')
        ->except('create', 'edit');
    Route::post('media/search', [MediaAPIController::class, 'search'])
        ->name('media.search');
});