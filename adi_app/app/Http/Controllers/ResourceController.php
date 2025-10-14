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
use Carbon\Carbon;

class ResourceController extends Controller
{
    /**
     * Display listing for frontend (hanya yang published)
     */
    public function index(Request $request): View|JsonResponse
    {
        $resource = Resource::with(['creator', 'updater'])
                    ->active()
                    ->published()
                    ->orderBy('publish_at', 'desc')
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
     * Display single resource (frontend - hanya yang published)
     */
    public function show(Request $request)
    {
        // Ambil semua resource yang published
        $resources = Resource::with(['creator','updater'])
            ->active()
            ->published()
            ->orderBy('publish_at', 'desc')
            ->get();
    
        if ($resources->isEmpty()) {
            abort(404, 'No published resources found');
        }
    
        // Tambahkan embed_url + thumbnail_url pada tiap item
        $resources->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            return $item;
        });
    
        // Main resource (yang pertama)
        $resource = $resources->first();
    
        // Related = semua selain yang pertama
        $related = $resources->slice(1);
    
        return view('resource.show', compact('resource', 'related', 'resources'));
    }

    /**
     * Ambil thumbnail dari URL video
     */
    private function getVideoThumbnail($url)
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://vumbnail.com/' . $matches[1] . '.jpg';
        }

        return asset('images/default-thumbnail.jpg');
    }
    
    /**
     * Convert video link to embeddable link
     */
    private function convertVideoToEmbed($url)
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $url; 
    }

    //==================== ADMIN SECTION ====================

    /**
     * Display all resources for admin (termasuk draft & scheduled)
     */
    public function ResourceAdmin()
    {
        $resource = Resource::with(['creator', 'updater'])
            ->active()
            ->orderBy('publish_at', 'desc')
            ->get();

        // Tambahin embed & thumbnail
        $resource->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            return $item;
        });

        return view('admin.resource.index', compact('resource'));
    }

    /**
     * Store new resource
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url' => 'required|url',
            'tanggal' => 'required|date',
        ]);

        $now = now('Asia/Jakarta');
        $selectedDate = Carbon::parse($validatedData['tanggal'])->setTimezone('Asia/Jakarta');
        
        // Logika publish
        $publishDate;
        if ($selectedDate->isToday()) {
            // Jika pilih hari ini, publish SEKARANG JUGA
            $publishDate = $now;
        } else {
            // Jika pilih tanggal lain, set ke jam 00:00
            $publishDate = $selectedDate->startOfDay();
        }

        $status = $publishDate->lte($now) ? 'published' : 'scheduled';
        
        $dataToSave = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'url' => $validatedData['url'],
            'publish_at' => $publishDate,
            'status' => $status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        $resource = Resource::create($dataToSave);
    
        Log::info("Resource created", [
            'resource_id' => $resource->id,
            'title' => $resource->title,
            'status' => $status,
            'created_by' => auth()->id()
        ]);

        $message = $status === 'scheduled' 
            ? "Latest Sermon berhasil dibuat dan dijadwalkan publish pada {$publishDate->format('d M Y, H:i')} WIB!"
            : "Latest Sermon berhasil dibuat dan langsung dipublish!";
    
        return redirect()->route('admin.resource.index')->with('success', $message);
    }

    /**
     * Update existing resource
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url' => 'required|url',
            'tanggal' => 'required|date',
        ]);

        $now = now('Asia/Jakarta');
        $selectedDate = Carbon::parse($validatedData['tanggal'])->setTimezone('Asia/Jakarta');
        
        // Logika publish
        $publishDate;
        if ($selectedDate->isToday()) {
            $publishDate = $now;
        } else {
            $publishDate = $selectedDate->startOfDay();
        }

        $status = $publishDate->lte($now) ? 'published' : 'scheduled';

        DB::beginTransaction();
        try {
            $resource->fill($validatedData);
            $resource->publish_at = $publishDate;
            $resource->status = $status;
            $resource->updated_by = auth()->id();

            // Update slug jika title berubah
            if ($resource->isDirty('title')) {
                $resource->slug = $validatedData['title'];
            }

            $resource->save();

            Log::info("Resource updated", [
                'resource_id' => $resource->id,
                'new_status' => $status,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            $message = $status === 'scheduled' 
                ? "Latest Sermon berhasil diupdate dan dijadwalkan publish pada {$publishDate->format('d M Y, H:i')} WIB!"
                : "Latest Sermon berhasil diupdate dan dipublish!";

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Resource update failed", [
                'resource_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors('Terjadi kesalahan saat update resource: ' . $e->getMessage());
        }
    }

    /**
     * Delete resource
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();
        
        Log::info("Resource deleted", [
            'resource_id' => $id,
            'deleted_by' => auth()->id()
        ]);
        
        return back()->with('success', 'Latest Sermon berhasil dihapus!');
    }
}