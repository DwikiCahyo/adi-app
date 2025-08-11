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

}
