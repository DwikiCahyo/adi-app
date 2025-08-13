@extends('layouts.newsApp')

@section('content')
    @foreach($news as $newsUser)
        <div class="max-w-sm mx-auto rounded-lg overflow-hidden shadow bg-white">
            <div class="bg-gray-100 p-6 flex items-center justify-between">
                <img src="{{ asset('images/logo.png') }}" alt="Abbalove" class="h-8">
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    {{ $newsUser->title }}
                </p>
                <a href="#" class="text-red-600 font-bold hover:underline">WATCH NOW</a>
            </div>
        </div>
    @endforeach
@endsection