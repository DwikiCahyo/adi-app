@extends('layouts.newsApp')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8" x-data="{ active: 0 }">

    {{-- Tombol Kembali --}}
    <div>
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-black font-medium transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            Kembali
        </a>
    </div>

    @php
        $hasImages = $news->images && $news->images->count() > 0;
        $hasVideo = !empty($news->embed_url);
        $hasThumbnail = !empty($news->thumbnail_url);
        $imageCount = $hasImages ? $news->images->count() : 0;
    @endphp

    @if($hasImages)
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Foto ({{ $imageCount }} gambar)</h2>

            {{-- Jika hanya ada satu gambar --}}
            @if($imageCount == 1)
                <div class="w-full rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('storage/' . $news->images->first()->image) }}" 
                         alt="{{ $news->title }}" 
                         class="w-full h-auto max-h-[600px] object-contain mx-auto">
                </div>
            @endif

            {{-- Jika gambar lebih dari satu (Carousel) --}}
            @if($imageCount > 1)
                <div class="relative w-full rounded-lg shadow-md">
                    <div class="overflow-hidden rounded-lg">
                        <template x-for="(img, i) in {{ json_encode($news->images->pluck('image')) }}" :key="i">
                            <div x-show="active === i" class="transition-all duration-500">
                                <img :src="'{{ asset('storage/') }}/' + img" 
                                     alt="{{ $news->title }}" 
                                     class="w-full h-auto max-h-[600px] object-contain mx-auto">
                            </div>
                        </template>
                    </div>

                    {{-- Prev / Next Buttons --}}
                    <button @click="active = active === 0 ? {{ $imageCount - 1 }} : active - 1" 
                            class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-gray-800/50 text-white p-2 rounded-full hover:bg-gray-800/70 transition">
                        &#10094;
                    </button>
                    <button @click="active = active === {{ $imageCount - 1 }} ? 0 : active + 1" 
                            class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-gray-800/50 text-white p-2 rounded-full hover:bg-gray-800/70 transition">
                        &#10095;
                    </button>

                    {{-- Dots Indicator --}}
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-2">
                        <template x-for="(img, i) in {{ json_encode($news->images->pluck('image')) }}" :key="i">
                            <button @click="active = i" 
                                    :class="{'bg-blue-500': active === i, 'bg-white/50': active !== i}" 
                                    class="w-3 h-3 rounded-full transition-all"></button>
                        </template>
                    </div>

                    {{-- Image counter --}}
                    <div class="absolute top-2 right-2 bg-black/60 text-white px-2 py-1 rounded text-sm">
                        <span x-text="active + 1"></span>/<span x-text="{{ $imageCount }}"></span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if(!$hasImages && $hasVideo)
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Video</h2>
            <div class="relative w-full rounded-lg shadow-md overflow-hidden">
                <div class="relative pb-[56.25%] h-0">
                    <iframe src="{{ $news->embed_url }}" 
                            class="absolute top-0 left-0 w-full h-full" 
                            frameborder="0" 
                            allowfullscreen 
                            loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    @endif

    @if(!$hasImages && !$hasVideo && $hasThumbnail)
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Preview</h2>
            <div class="relative w-full rounded-lg shadow-md overflow-hidden">
                <img src="{{ $news->thumbnail_url }}" 
                     alt="{{ $news->title }}" 
                     class="w-full h-auto max-h-[600px] object-contain mx-auto">
                @if($news->url)
                    <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" 
                       class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 hover:bg-opacity-60 transition-all group">
                        <div class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold group-hover:bg-red-700 transition-colors shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            Tonton Video
                        </div>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <article class="prose prose-lg max-w-none">
        <header class="not-prose mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">{{ $news->title }}</h1>
            <div class="text-gray-700 text-justify leading-relaxed tracking-wide mb-4">
                {{$news->publish_at?->translatedFormat('d F Y')}}
            </div>
        </header>
        <div class="text-gray-700 text-justify leading-relaxed tracking-wide">
            {!! ($news->content) !!}
        </div>
    </article>

    @if($hasImages && $news->url)
        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-6 border-l-4 border-red-500 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                </svg>
                Video Terkait
            </h3>
            <p class="text-gray-600 mb-4">Artikel ini juga memiliki konten video yang dapat Anda tonton</p>
            <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                </svg>
                Tonton Video
            </a>
        </div>
    @endif

</div>

{{-- Alpine.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@endsection