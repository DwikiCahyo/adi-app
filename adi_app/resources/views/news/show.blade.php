@extends('layouts.newsApp')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

    {{-- Tombol Back --}}
    <div>
        <a href="{{ route('index') }}" 
           class="inline-flex items-center gap-2 text-gray-700 hover:text-black font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor" 
                 class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

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

    <div class="text-left sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $news->title }}</h1>
        
        {{-- Tanggal --}}
        <p class="text-sm text-gray-500 mb-4">
            {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y, H:i') }} WIB
        </p>

        <p class="text-gray-700 leading-relaxed">{{ $news->content }}</p>
    </div>

</div>
@endsection
