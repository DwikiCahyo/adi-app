@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $newsUser)
            <div class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-shadow duration-200">
                <!-- Logo -->
                <div class="bg-gray-100 p-4 flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Abbalove" class="h-10 object-contain">
                </div>

                <!-- Content -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-gray-800 font-semibold text-lg leading-snug mb-4">
                        {{ $newsUser->title }}
                    </p>
                    <a href="{{ route('show', $newsUser->slug) }}"
                       class="text-red-600 font-bold hover:underline mt-auto">
                       WATCH NOW
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
