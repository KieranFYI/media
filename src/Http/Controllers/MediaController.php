<?php

namespace KieranFYI\Media\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use KieranFYI\Media\Core\Facades\MediaStorage;
use KieranFYI\Media\Core\Models\Media;
use KieranFYI\Media\Core\Models\MediaVersion;
use KieranFYI\Misc\Traits\ResponseCacheable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MediaController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use ResponseCacheable;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Media::class, 'media');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     * @throws Throwable
     */
    public function index(): View
    {
        $this->cached();
        return view('media::index');
    }

    /**
     * Display the specified resource.
     *
     * @param MediaVersion $version
     * @param string $extension
     * @return StreamedResponse
     * @throws Throwable
     */
    public function show(MediaVersion $version, string $extension): StreamedResponse
    {
        return MediaStorage::response($version, $extension);
    }

    /**
     * @return string[]
     */
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'viewAny',
            'show' => 'viewAny',
        ];
    }
}