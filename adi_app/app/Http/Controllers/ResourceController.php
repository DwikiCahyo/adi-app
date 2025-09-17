<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Resource;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ResourceController extends Controller
{
    public function index(Request $request): View|JsonResponse{
        $resource = Resource::with(['creator', 'updater'])
                    ->active()
                    ->recent()
                    ->get()
                    ->map(function ($item) {
                        $item->thumbnail_url = $this->getVideoThumbnail($item->url);
                        return $item;
                    });

        Log::info("Resource index request", [
            'total_resource' => $resource->count(),
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => NewsResource::collection($resource),
                'total'   => $resource->count(),
                'message' => 'Data Resource berhasil ditampilkan'
            ]);
        }

        return view('resource.index', compact('resource'));
    }

     /**
     * Ambil thumbnail dari URL video
     */
    private function getVideoThumbnail($url){
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://vumbnail.com/' . $matches[1] . '.jpg';
        }

        return asset('images/default-thumbnail.jpg');
    }

    public function show(Request $request)
    {
        // ambil semua resource (active + urut terbaru)
        $resources = Resource::with(['creator','updater'])
            ->active()
            ->latest()
            ->get();
    
        if ($resources->isEmpty()) {
            // Jangan tampilkan halaman kosong — kembalikan 404 atau redirect sesuai kebutuhan
            abort(404, 'No resources found');
        }
    
        // tambahkan embed_url + thumbnail_url pada tiap item
        $resources->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            return $item;
        });
    
        // main resource (yang pertama)
        $resource = $resources->first();
    
        // related = semua selain yang pertama (Collection)
        $related = $resources->slice(1);
    
        // kirim semuanya ke view — view kamu memakai $resource (main) dan $related
        return view('resource.show', compact('resource', 'related', 'resources'));
    }
    

    
    /**
     * Convert video link to embeddable link.
     */
    private function convertVideoToEmbed($url){
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $url; 
    }

    //ADMIN

    public function ResourceAdmin(){
        $resource = Resource::with(['creator', 'updater'])
            ->active()
            ->get();

        // tambahin embed & thumbnail
        $resource->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            return $item;
        });

        return view('admin.resource.index', compact('resource'));
    }

    public function store(StoreNewsRequest $request){
        $validatedData = $request->validated();
        $validatedData['created_by'] = auth()->id();
        $validatedData['updated_by'] = auth()->id();
    
        $resource = Resource::create($validatedData);
    
        Log::info("Resource created", [
            'resource_id' => $resource->id,
            'title' => $resource->title,
            'created_by' => auth()->id()
        ]);
    
        // Redirect ke dashboard yang otomatis ambil semua news
        return redirect()->route('admin.resource.index')->with('success', 'Resource berhasil dibuat!');
    }

    public function update(UpdateNewsRequest $request, $id)
    {
        $resource = Resource::findOrFail($id);

        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $validatedData['updated_by'] = auth()->id();

            $oldData = $resource->only(['title', 'url', 'content', 'slug']);

            $resource->fill($validatedData);

            // supaya slug tetap konsisten
            if (!empty($validatedData['title']) && $validatedData['title'] !== $oldData['title']) {
                $resource->slug = $validatedData['title'];
            }

            $resource->save();

            Log::info("Resource updated", [
                'resource_id'   => $resource->id,
                'old_data'  => $oldData,
                'new_data'  => $resource->only(['title', 'url', 'content', 'slug']),
                'updated_by'=> auth()->id()
            ]);

            DB::commit();

            return back()->with('success', 'resource berhasil diedit!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Resource update failed", [
                'resource_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return back()->withErrors('Terjadi kesalahan saat update resource');
        }
    }

    public function destroy($id){
        $resource = Resource::findOrFail($id);
        $resource->delete();
        return back()->with('success', 'Resource berhasil didelete!');
    }
}

