@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $newsItem)
            <a href="{{ route('news.show', $newsItem->slug) }}" class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                
                <div class="block relative aspect-video">
                    @php
                        $displayImage = asset('images/default-thumbnail.jpg');
                        $altText = $newsItem->title;
                        $imageCount = $newsItem->images->count() ?? 0;
                        
                        if ($imageCount > 0) {
                            $displayImage = asset('storage/' . $newsItem->images->first()->image);
                        } 
                        elseif (!empty($newsItem->thumbnail_url) && filter_var($newsItem->thumbnail_url, FILTER_VALIDATE_URL)) {
                            $displayImage = $newsItem->thumbnail_url;
                        }
                    @endphp
                    
                    <img 
                        src="{{ $displayImage }}" 
                        alt="{{ $altText }}" 
                        class="w-full h-full object-cover transition-opacity duration-200"
                        onerror="this.onerror=null; this.src='{{ asset('images/default-thumbnail.jpg') }}';"
                    >
                    
                    @if($imageCount > 1)
                        <div class="absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                            {{ $imageCount }}
                        </div>
                    @endif
                    
                    @if(!empty($newsItem->url) && $imageCount == 0)
                        <div class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                            VIDEO
                        </div>
                    @endif
                    
                    @if($imageCount == 1)
                        <div class="absolute top-2 right-2 bg-green-600 bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                            FOTO
                        </div>
                    @endif
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <h3 class="text-gray-800 font-semibold text-lg leading-snug mb-4 line-clamp-2">
                        {{ $newsItem->title }}
                    </h3>
                    
                    @if($newsItem->content)
                        <div class="text-gray-600 text-sm line-clamp-3 flex-1">
                            {!! Str::limit(strip_tags($newsItem->content), 120) !!}
                        </div>
                        <div class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                            {{$newsItem->publish_at?->translatedFormat('d F Y')}}
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <div class="flex items-center space-x-4">
                             @if($imageCount > 0)
                                <span class="flex items-center text-green-600"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>{{ $imageCount }} {{ $imageCount == 1 ? 'foto' : 'foto' }}</span>
                            @elseif(!empty($newsItem->url))
                                <span class="flex items-center text-red-600"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>Video</span>
                            @elseif(!empty($newsItem->thumbnail_url))
                                <span class="flex items-center text-blue-600"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>Thumbnail</span>
                            @endif
                        </div>
                        
                    </div>
                    
                    <div class="inline-flex items-center text-red-600 font-bold group-hover:text-red-700 group-hover:underline transition-colors duration-200">
                       <span>WATCH NOW</span>
                       <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

@endsection