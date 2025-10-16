@extends('layouts.newsApp')

@section('content')
<div class="w-full min-h-screen mb-6">
    {{-- Back Button --}}
    <div class="max-w-2xl mx-auto pt-6 pb-4 px-4">
        <a href="{{ route('ministry.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Main Content Card --}}
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        
        {{-- Website/Source Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-medium border border-blue-100">
                {{ $ministry->category }}
            </span>
        </div>

        {{-- Images Carousel Section --}}
        @if($ministry->images && $ministry->images->count() > 0)
            <div class="relative bg-white" 
                 x-data="{ 
                    active: 0, 
                    images: @js($ministry->images),
                    startX: 0, 
                    endX: 0 
                 }"
                 @touchstart="startX = $event.touches[0].clientX"
                 @touchend="
                    endX = $event.changedTouches[0].clientX;
                    if (startX - endX > 50) {
                        active = (active + 1) % images.length; 
                    } else if (endX - startX > 50) {
                        active = (active - 1 + images.length) % images.length; 
                    }
                 ">

                {{-- Main Image Container --}}
                <div class="relative w-full" style="aspect-ratio: 16/10;">
                    <template x-for="(img, i) in images" :key="i">
                        <div x-show="active === i" 
                             class="absolute inset-0 flex items-center justify-center bg-gray-50">
                            <img :src="'/storage/' + img.image" 
                                 :alt="'{{ $ministry->title }}'"
                                 class="w-full h-full object-cover"
                                 x-transition:enter="transition ease-in-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100">
                        </div>
                    </template>
                </div>

                {{-- Navigation Arrows --}}
                <template x-if="images.length > 1">
                    <div>
                        <button @click="active = (active - 1 + images.length) % images.length"
                                class="absolute top-1/2 left-4 -translate-y-1/2 w-10 h-10 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full flex items-center justify-center shadow-md transition-all duration-200">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button @click="active = (active + 1) % images.length"
                                class="absolute top-1/2 right-4 -translate-y-1/2 w-10 h-10 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full flex items-center justify-center shadow-md transition-all duration-200">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Dots Indicators --}}
                <template x-if="images.length > 1">
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                        <template x-for="(img, i) in images" :key="i">
                            <button @click="active = i"
                                    class="w-2.5 h-2.5 rounded-full transition-all duration-200"
                                    :class="active === i ? 'bg-red-500' : 'bg-white bg-opacity-60'">
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        @else
            {{-- Placeholder jika tidak ada gambar --}}
            <div class="w-full bg-gray-200 flex items-center justify-center" style="aspect-ratio: 16/10;">
                <div class="text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm">No Image Available</p>
                </div>
            </div>
        @endif

        {{-- Content Section --}}
        <div class="p-6">
            {{-- Title --}}
            <h1 class="text-2xl font-bold text-gray-900 mb-4 leading-tight">
                {{ $ministry->title }}
            </h1>

            {{-- Category and Date Info --}}
            {{-- <div class="flex flex-wrap items-center gap-3 mb-6 text-sm text-gray-600">
                
                @if($ministry->created_at)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $ministry->created_at->format('M d, Y') }}
                    </span>
                @endif
            </div> --}}

            {{-- Main Content --}}
            <div class="prose prose-gray max-w-none">
                <div class="text-gray-700 leading-relaxed text-base space-y-4">
                    {!! $ministry->content !!}
                </div>
            </div>
        </div>

      
    </div>

    
</div>

{{-- Scripts --}}
<script>
function shareContent() {
    const title = '{{ $ministry->title }}';
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(console.error);
    } else {
        // Fallback untuk browser yang tidak support Web Share API
        navigator.clipboard.writeText(url).then(function() {
            // Buat toast notification sederhana
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm z-50';
            toast.textContent = 'Link copied to clipboard!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }).catch(function() {
            alert('Unable to copy link');
        });
    }
}
</script>

<style>
/* Custom styling untuk line-clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth scrolling untuk mobile */
@media (max-width: 768px) {
    .prose {
        font-size: 16px;
        line-height: 1.6;
    }
}
</style>
@endsection