<?php

namespace KieranFYI\Media\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
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
     * Show the form for creating a new resource.
     *
     * @return View
     * @throws Throwable
     */
    public function create(): View
    {
        $this->cached();
        return view('media::create');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Media $media
     * @param MediaVersion $version
     * @param string|null $extension
     * @return StreamedResponse
     * @throws Throwable
     */
    public function show(Request $request, Media $media, MediaVersion $version, string $extension = null): StreamedResponse
    {
        $this->cached($media->updated_at);

        $storage = MediaStorage::storage($media->storage);
        $stream = $storage->stream($version->file_name);
        return response()->stream(
            function () use ($stream, $storage) {
                while (ob_get_level() > 0) ob_end_flush();
                fpassthru($stream);
            },
            200,
            [
                'Content-Type' => $version->content_type,
                'Content-Disposition' => $storage->disposition() . '; filename="' . $media->file_name . '"',
            ]);
    }

    /**
     * @return string[]
     */
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'viewAny',
            'show' => 'viewAny',
            'create' => 'create',
        ];
    }
}