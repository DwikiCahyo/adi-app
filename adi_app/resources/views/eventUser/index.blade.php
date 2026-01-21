@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($event as $eventItem)
            <a href="{{ route('events.showUser', $eventItem->slug) }}" 
               class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                
                <!-- Image Section -->
                <div class="block relative aspect-video">
                    @php
                        $displayImage = asset('images/default-thumbnail.jpg');
                        $altText = $eventItem->title;
                        $imageCount = $eventItem->images->count() ?? 0;
                        
                        // ✅ PRIORITAS BENAR: Images upload DULU, baru video thumbnail
                        if ($imageCount > 0) {
                            $displayImage = asset('storage/events/' . $eventItem->images->first()->image);
                        } 
                        elseif (!empty($eventItem->thumbnail_url) && filter_var($eventItem->thumbnail_url, FILTER_VALIDATE_URL)) {
                            $displayImage = $eventItem->thumbnail_url;
                        }
                    @endphp
                    
                    <img 
                        src="{{ $displayImage }}" 
                        alt="{{ $altText }}" 
                        class="w-full h-full object-cover transition-opacity duration-200"
                        onerror="this.onerror=null; this.src='{{ asset('images/default-thumbnail.jpg') }}';"
                    >
                    
                    <!-- Badge: Video (HANYA jika tidak ada uploaded images) -->
                    @if($eventItem->hasVideo() && $imageCount == 0)
                        <div class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            VIDEO
                        </div>
                    @endif
                    
                    <!-- Badge: Multiple Images -->
                    @if($imageCount > 1)
                        <div class="absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $imageCount }}
                        </div>
                    @endif
                    
                    <!-- Badge: Single Photo -->
                    @if($imageCount == 1)
                        <div class="absolute top-2 right-2 bg-green-600 bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            FOTO
                        </div>
                    @endif
                </div>

                <!-- Content Section -->
                <div class="p-6 flex flex-col flex-1">
                    <!-- Agenda (sebagai title utama) -->
                    <h3 class="text-gray-800 font-semibold text-lg leading-snug mb-2 line-clamp-2">
                        {{ $eventItem->agenda }}
                    </h3>
                    
                    <!-- Title (sebagai subtitle) -->
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $eventItem->title }}
                    </p>
                    
                    <!-- Content Preview (dari topic pertama jika ada) -->
                    @if($eventItem->topics && $eventItem->topics->first())
                        <div class="text-gray-600 text-sm line-clamp-3 flex-1 mb-4">
                            {!! Str::limit(strip_tags($eventItem->topics->first()->content), 120) !!}
                        </div>
                    @endif
                    
                    <!-- Created Date -->
                    <div class="text-gray-500 text-xs mb-4">
                        {{ $eventItem->created_at->translatedFormat('d F Y') }}
                    </div>
                    
                    <!-- Media Info -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <div class="flex items-center space-x-4">
                            @if($imageCount > 0)
                                {{-- Ada uploaded images --}}
                                <span class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $imageCount }} {{ $imageCount == 1 ? 'foto' : 'foto' }}
                                </span>
                            @elseif($eventItem->hasVideo())
                                {{-- Hanya video, tidak ada images --}}
                                <span class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    Video
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Call to Action -->
                    <div class="inline-flex items-center text-red-600 font-bold group-hover:text-red-700 group-hover:underline transition-colors duration-200">
                        <span>FIND OUT MORE</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection