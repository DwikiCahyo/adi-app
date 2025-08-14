<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\News;
use App\Http\Resources\NewsResource;

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


    public function NewsAdmin(Request $request){
        
        $news = News::with(['creator', 'updater'])
                    ->active()
                    ->recent()
                    ->get();

        if ($request->expectsJson()) {

            $formattedNews = NewsResource::collection($news);

            return response()->json([
                'success' => true,
                'data'    => $formattedNews,
                'total'   => $news->count(),
                'message' => 'Data News berhasil ditampilkan'
            ]);
        }

        return view('dashboard', compact('news'));
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

}
