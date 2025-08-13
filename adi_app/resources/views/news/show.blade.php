@extends('layouts.newsApp')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

    @if($news->embed_url)
        <div class="flex justify-center">
            <div class="relative pt-[56.25%] w-full max-w-lg sm:max-w-full">
                <iframe 
                    src="{{ $news->embed_url }}"
                    class="absolute top-0 left-0 w-full h-full rounded-lg shadow-md"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    @endif

    @if($news->url_thumbnail && !$news->embed_url)
        <div class="flex justify-center">
            <img src="{{ $news->url_thumbnail }}" 
                 alt="Video Thumbnail"
                 class="w-full max-w-lg sm:max-w-3xl h-auto rounded-lg shadow-md">
        </div>
    @endif

    <div class="text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-bold mb-3">{{ $news->title }}</h1>
        <p class="text-gray-700 leading-relaxed">{{ $news->content }}</p>
    </div>

</div>
@endsection
