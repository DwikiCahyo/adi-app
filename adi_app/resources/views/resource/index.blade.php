@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Card 1: Latest Sermon --}}
        <a href="{{ route('resource.show') }}" class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-all duration-200 hover:scale-[1.02] group cursor-pointer">
            
            <div class="block relative aspect-video overflow-hidden">
                <img 
                    src="{{ asset('Images/latestsermons.png') }}" 
                    alt="Latest Sermon" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                >
                {{-- Optional overlay for better visual feedback --}}
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
            </div>

            {{-- Content --}}
            <div class="p-6 flex flex-col flex-1">
                <p class="text-gray-800 font-semibold text-lg leading-snug mb-4 group-hover:text-gray-900 transition-colors">
                    Khotbah-khotbah ini akan menolong kita untuk lebih memahami firman Tuhan dan menggingatkan kita untuk menerapkannya di dalam kehidupan sehari-hari.
                </p>
                <span class="text-red-600 font-bold group-hover:text-red-700 group-hover:underline mt-auto transition-colors">
                    WATCH NOW
                </span>
            </div>
        </a>

        {{-- Card 2: Good News --}}
        <a href="{{ route('resourcefile.show') }}" class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-all duration-200 hover:scale-[1.02] group cursor-pointer">
            
            <div class="block relative aspect-video overflow-hidden">
                <img 
                    src="{{ asset('Images/GoodNews1.png') }}" 
                    alt="Good News" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                >
                {{-- Optional overlay for better visual feedback --}}
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
            </div>

            {{-- Content --}}
            <div class="p-6 flex flex-col flex-1">
                <p class="text-gray-800 font-semibold text-lg leading-snug mb-4 group-hover:text-gray-900 transition-colors">
                    Mulailah harimu dengan firman Tuhan. Renungan firman Tuhan dalam Good News akan menolong kita menggalami kebenarannya yang akan mengubahkan kita semakin serupa Kristus.
                </p>
                <span class="text-red-600 font-bold group-hover:text-red-700 group-hover:underline mt-auto transition-colors">
                    FIND OUT MORE
                </span>
            </div>
        </a>

    </div>
</div>

@push('styles')
<style>
    /* Additional hover effects for better UX */
    .group:hover {
        transform: translateY(-2px);
    }
    
    /* Smooth transitions for all interactive elements */
    .group * {
        transition: all 0.2s ease-in-out;
    }
    
    /* Focus states for accessibility */
    .group:focus {
        outline: 2px solid #3B82F6;
        outline-offset: 2px;
    }
    
    /* Prevent text selection on cards for better UX */
    .group {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
</style>
@endpush

@endsection