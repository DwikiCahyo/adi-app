@extends('layouts.newsApp')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-shadow duration-200">
                
                <a :href="route('resource.show')" class="block relative aspect-video">
                    <img 
                        src="{{ asset('Images/latestsermon.png') }}" 
                        alt="Latest Sermon" 
                        class="w-full h-full object-cover"
                    >
                </a>

                <!-- Content -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-gray-800 font-semibold text-lg leading-snug mb-4">
                        Kumpulan khotbah ini akan menolong kita untuk mendapatkan pengertian yang lebih baik akan Firman Tuhan, serta mengingatkan kita akan prinsip-prinsip kebenaran yang perlu diterapkan didalam kehidupan.
                    </p>
                    <a href="{{ route('resource.show') }}" class="text-red-600 font-bold hover:underline mt-auto">
                        WATCH NOW
                    </a>
                </div>
            </div>

            <div class="flex flex-col rounded-lg overflow-hidden shadow bg-white hover:shadow-lg transition-shadow duration-200">
                
                <a :href="route('resourcefile.show')" class="block relative aspect-video">
                    <img 
                        src="{{ asset('Images/GoodNews.png') }}" 
                        alt="Good News" 
                        class="w-full h-full object-cover"
                    >
                </a>

                <!-- Content -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-gray-800 font-semibold text-lg leading-snug mb-4">
                        Mulai harimu dengan Firman Tuhan! Renungan harian ini akan menuntun kita untuk mengalami perjumpaan dengan kebenaran sehingga kita dipimpin oleh hikmat Tuhan dalam menjalani kehidupan sehari-hari.
                    </p>
                    <a href="{{ route('resourcefile.show') }}" class="text-red-600 font-bold hover:underline mt-auto">
                       FIND OUT MORE
                    </a>
                </div>
            </div>
    </div>
</div>
@endsection
