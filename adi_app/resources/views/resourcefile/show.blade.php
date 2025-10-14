@extends('layouts.newsApp')

@section('content')
<div class="bg-blue-100 min-h-screen px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    <div class="mb-4">
        <a href="{{ route('resource.index') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-black font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Archive Header --}}
    <div class="bg-white shadow-sm rounded-lg px-4 py-3 sm:px-6 sm:py-4 mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Devotion Archive</h2>
    </div>

    {{-- Archive List --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($resourcefile as $item)
            <a href="{{ route('resourcefile.showfile', $item->id) }}" 
               class="block bg-white shadow rounded-xl p-4 sm:p-5 md:p-6 hover:shadow-lg hover:-translate-y-1 transition transform">
                
                {{-- Tanggal --}}
                <p class="text-xs sm:text-sm text-gray-500 mb-2">
                    {{ $item->publish_at?->translatedFormat('d F Y') }}
                </p>

                {{-- Judul --}}
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 line-clamp-2">
                    {{ $item->title }}
                </h3>

                {{-- Link --}}
                <span class="text-indigo-600 text-xs sm:text-sm font-medium">Baca selengkapnya →</span>
            </a>
        @empty
            <p class="text-gray-500 col-span-full">Belum ada arsip lain.</p>
        @endforelse
    </div>
</div>
@endsection
