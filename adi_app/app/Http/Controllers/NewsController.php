<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\News;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class NewsController extends Controller {


    public function index(Request $request): View|JsonResponse{
        $news = News::with(['creator', 'updater'])
                    ->active()
                    ->recent()
                    ->get()
                    ->map(function ($item) {
                        $item->thumbnail_url = $this->getVideoThumbnail($item->url);
                        return $item;
                    });

        Log::info("News index request", [
            'total_news' => $news->count(),
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => NewsResource::collection($news),
                'total'   => $news->count(),
                'message' => 'Data news berhasil ditampilkan'
            ]);
        }

        return view('news.index', compact('news'));
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

    public function show(Request $request, News $news){
        $news->load(['creator', 'updater']);

        Log::info("News show request", [
            'news_id'      => $news->id,
            'news_slug'    => $news->slug,
            'title'        => $news->title,
            'expects_json' => $request->expectsJson(),
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent()
        ]);

        $news->embed_url = $news->url
            ? $this->convertVideoToEmbed($news->url)
            : null;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => new NewsResource($news),
                'message' => 'Data news berhasil ditampilkan'
            ]);
        }

        return view('news.show', compact('news'));
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

    public function NewsAdmin(){
        $news = News::with(['creator', 'updater'])
            ->active()
            ->get();

        // tambahin embed & thumbnail
        $news->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            return $item;
        });

        return view('admin.dashboard', compact('news'));
    }

    public function store(StoreNewsRequest $request){
        $validatedData = $request->validated();
        $validatedData['created_by'] = auth()->id();
        $validatedData['updated_by'] = auth()->id();
    
        $news = News::create($validatedData);
    
        Log::info("News created", [
            'news_id' => $news->id,
            'title' => $news->title,
            'created_by' => auth()->id()
        ]);
    
        // Redirect ke dashboard yang otomatis ambil semua news
        return redirect()->route('admin.dashboard')->with('success', 'News berhasil dibuat!');
    }
    
    public function update(UpdateNewsRequest $request, $id)
    {
        $news = News::findOrFail($id);

        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $validatedData['updated_by'] = auth()->id();

            $oldData = $news->only(['title', 'url', 'content', 'slug']);

            $news->fill($validatedData);

            // supaya slug tetap konsisten
            if (!empty($validatedData['title']) && $validatedData['title'] !== $oldData['title']) {
                $news->slug = $validatedData['title'];
            }

            $news->save();

            Log::info("News updated", [
                'news_id'   => $news->id,
                'old_data'  => $oldData,
                'new_data'  => $news->only(['title', 'url', 'content', 'slug']),
                'updated_by'=> auth()->id()
            ]);

            DB::commit();

            return back()->with('success', 'News berhasil diedit!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("News update failed", [
                'news_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return back()->withErrors('Terjadi kesalahan saat update news');
        }
    }

    public function destroy($id){
        $news = News::findOrFail($id);
        $news->delete();
        return back()->with('success', 'News berhasil didelete!');
    }
}
