@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $newsUser)
            <div class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-shadow duration-200">
                
                <!-- Thumbnail -->
                <a href="{{ route('news.show', $newsUser->slug) }}" class="block relative aspect-video">
                    <img 
                        src="{{ $newsUser->thumbnail_url}}" 
                        alt="{{ $newsUser->title }}" 
                        class="w-full h-full object-cover"
                    >
                </a>

                <!-- Content -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-gray-800 font-semibold text-lg leading-snug mb-4">
                        {{ $newsUser->title }}
                    </p>
                    <a href="{{ route('news.show', $newsUser->slug) }}"
                       class="text-red-600 font-bold hover:underline mt-auto">
                       WATCH NOW
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
