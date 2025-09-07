@extends('layouts.newsApp')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8" x-data="{ active: 0 }">

    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-black font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Carousel Gambar -->
    @if($event->images->isNotEmpty())
    <div class="relative w-full rounded-lg shadow-md mb-6">
        <div class="overflow-hidden rounded-lg">
            <template x-for="(img, i) in {{ json_encode($event->images->pluck('image')) }}" :key="i">
                <div x-show="active === i" class="transition-all duration-500">
                    <img :src="'{{ asset('storage/events/') }}/' + img" 
                         alt="{{ $event->title }}" 
                         class="w-full h-auto max-h-[600px] object-contain mx-auto">
                </div>
            </template>
        </div>

        <!-- Prev / Next Buttons -->
        <button @click="active = active === 0 ? {{ $event->images->count() - 1 }} : active - 1" 
                class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-gray-800/50 text-white p-2 rounded-full hover:bg-gray-800/70">
            &#10094;
        </button>
        <button @click="active = active === {{ $event->images->count() - 1 }} ? 0 : active + 1" 
                class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-gray-800/50 text-white p-2 rounded-full hover:bg-gray-800/70">
            &#10095;
        </button>

        <!-- Dots Indicator -->
        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-2">
            <template x-for="(img, i) in {{ json_encode($event->images->pluck('image')) }}" :key="i">
                <button @click="active = i" 
                        :class="{'bg-red-500': active === i, 'bg-white/50': active !== i}" 
                        class="w-3 h-3 rounded-full"></button>
            </template>
        </div>
    </div>
    @endif

    <!-- Agenda -->
    <div class="text-3xl md:text-4xl font-semibold text-gray-700 mb-0.5 md:mb-2">{{ $event->agenda }}</div>

    <!-- Title Event -->
    <h1 class="text-xl font-bold mb-2 md:mb-6">{{ $event->title }}</h1>


    <!-- Garis Pembatas -->
    <hr class="border-gray-300 mb-6">

    <!-- Topics & Konten -->
    @if($event->topics->isNotEmpty())
        <div class="space-y-6">
            @foreach($event->topics as $topic)
                <div>
                    <p class="font-bold text-lg mb-1">{{ $topic->topic }}</p>
                    <p class="text-gray-700 text-justify leading-relaxed tracking-wide">
                        {!! nl2br(e($topic->content)) !!}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- Alpine.js untuk carousel -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
