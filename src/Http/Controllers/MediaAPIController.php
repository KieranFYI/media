<?php

namespace KieranFYI\Media\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use KieranFYI\Logging\Traits\LoggableResponse;
use KieranFYI\Media\Core\Facades\MediaStorage;
use KieranFYI\Media\Core\Models\Media;
use KieranFYI\Media\Http\Requests\SearchRequest;
use KieranFYI\Media\Http\Requests\StoreRequest;
use KieranFYI\Misc\Traits\ResponseCacheable;
use Throwable;

class MediaAPIController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use LoggableResponse;
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
     * @return JsonResponse
     * @throws Throwable
     */
    public function index(): JsonResponse
    {
        $this->cached();
        $users = Media::paginate();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $media = collect();
        foreach ($request->file('files') as $file) {
            $media->add(MediaStorage::store($file));
        }
        return response()->json($media);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Media $user
     * @return JsonResponse
     * @throws Throwable
     */
    public function show(Request $request, Media $media): JsonResponse
    {
        $this->cached();
        return response()->json($media);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Media $media
     * @return JsonResponse
     */
    public function destroy(Media $media): JsonResponse
    {
        $media->delete();
        return response()->json();
    }

    /**
     * Display a listing of the resource.
     *
     * @param SearchRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $this->cached();
        $users = Media::query();

        $validated = $request->validated();

        $users->when(!empty($validated['search']), function (Builder $builder) use ($validated) {
            $builder->where(function (Builder $builder) use ($validated) {
                $search = '%' . $validated['search'] . '%';
                $builder->where('file_name', 'LIKE', $search)
                    ->orWhere('key', 'LIKE', $search);
            });
        });

        return response()->json($users->paginate());
    }

    /**\\
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'viewAny',
            'search' => 'viewAny',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['index', 'create', 'store', 'search'];
    }
}