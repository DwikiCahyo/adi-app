@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($event as $event)
            <!-- Membuat seluruh card menjadi link yang dapat diklik -->
            <a href="{{ route('events.showUser', $event->slug) }}" 
               class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-300 p-4 flex flex-col group cursor-pointer transform hover:-translate-y-1">
                
                <!-- Thumbnail Gambar Full Responsive -->
                @if($event->images->first())
                    <div class="w-full h-64 sm:h-72 md:h-80 lg:h-64 xl:h-72 rounded overflow-hidden mb-4">
                        <img src="{{ asset('storage/events/' . $event->images->first()->image) }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105">
                    </div>
                @endif

                <!-- Agenda -->
                <h2 class="text-xl font-semibold mb-1 group-hover:text-red-600 transition-colors duration-300">{{ $event->agenda }}</h2>
                
                <!-- Title -->
                <p class="text-gray-700 mb-4 group-hover:text-gray-900 transition-colors duration-300">{{ $event->title }}</p>

                <!-- Tombol Detail (sekarang hanya sebagai indikator visual) -->
                <div class="mt-auto text-red-600 font-bold group-hover:underline">
                   FIND OUT MORE
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection