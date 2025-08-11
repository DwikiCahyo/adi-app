<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\News;
use App\Http\Resources\NewsResource;

class NewsController extends Controller {


    public function index(Request $request):View|JsonResponse {

        $query = News::with(['creator', 'updater'])
                 ->active()
                 ->recent();

        $news = $query -> get();
       

        Log::info("request log" , [
            'request'  => $request,
            'query' => $news
        ]);


        if($request -> expectsJson()){

             $formattedNews = NewsResource::collection($news);

             return response()->json(
                [
                    'success' => true,
                    'data' => $formattedNews,
                    'total' => $news->count(),
                    'message' => 'Data News Barhasil ditampilkan'
                ]
             );
        }

        return view('news.index',compact('news'));
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

    public function show(Request $request , News $news){
        
        $news -> load(['creating', 'updateing']);

        Log::info("News show request", [
            'news_id' => $news->id,
            'news_slug' => $news->slug,
            'title' => $news->title,
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => new NewsResource($news),
                'message' => 'Data news berhasil ditampilkan'
            ]);
        }


    }

}
