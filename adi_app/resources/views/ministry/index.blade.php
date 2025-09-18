@extends('layouts.newsApp')

@section('content')
    <div class="container mx-auto p-6">

        <div class="flex justify-center border-b border-gray-200 mb-6">
            <a href="{{ route('ministry.index') }}" 
               class="px-4 py-2 font-medium text-lg {{ is_null($category) ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500 hover:text-gray-700' }}">
                Semua
            </a>
            <a href="{{ route('ministry.index', ['category' => 'Kids']) }}" 
               class="px-4 py-2 font-medium text-lg {{ $category == 'Kids' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500 hover:text-gray-700' }}">
                Kids
            </a>
            <a href="{{ route('ministry.index', ['category' => 'Youth Generation']) }}" 
               class="px-4 py-2 font-medium text-lg {{ $category == 'Youth Generation' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500 hover:text-gray-700' }}">
                Youth Gen
            </a>
            <a href="{{ route('ministry.index', ['category' => 'General']) }}" 
               class="px-4 py-2 font-medium text-lg {{ $category == 'General' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-500 hover:text-gray-700' }}">
                General
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($ministry as $item)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                @if($item->images->count() > 0)
                    <div class="relative w-full h-56 overflow-hidden">
                        <img src="{{ asset('storage/' . $item->images->first()->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        </div>
                @else
                    <div class="bg-gray-200 w-full h-56 flex items-center justify-center text-gray-500">
                        No Image
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $item->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($item->content, 150) }}</p>
                    <a href="{{ route('ministry.showUser', $item->id) }}" class="text-blue-600 hover:underline">Baca Selengkapnya</a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 text-lg">
                Belum ada ministry untuk kategori ini.
            </div>
            @endforelse
        </div>

    </div>
@endsection
