@extends('layouts.newsApp')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-xl p-6">

        {{-- Tanggal dibuat --}}
        <p class="text-sm text-gray-500 mb-2">
            {{ $resourcefile->created_at ? $resourcefile->created_at->translatedFormat('d F Y') : '-' }}
        </p>
        
        {{-- Judul --}}
        <h1 class="text-2xl font-bold mb-4 text-black-600">
            {{ $resourcefile->title }}
        </h1>

       {{-- Nama File + tombol download --}}
       @if($resourcefile->file_path)
       <div class="mt-2 mb-6">
           <p class="text-gray-700 font-semibold">Read:</p>
           <a href="{{ asset('storage/' . $resourcefile->file_path) }}" 
              target="_blank"
              class="inline-flex items-center gap-2 text-blue-600 hover:underline">
               📎 {{ $resourcefile->nama_file ?? basename($resourcefile->file_path) }}
           </a>
       </div>
   @endif
   

        {{-- Konten --}}
        <div class="prose prose-lg max-w-none mb-6">
           <p class="text-gray-700 font-semibold">Reflection:</p>
            {!! nl2br(e($resourcefile->content)) !!}
        </div>

        {{-- Back button --}}
        <div class="mt-6">
            <a href="{{ route('resourcefile.show') }}" 
               class="text-gray-600 hover:text-red-600">← Back to list</a>
        </div>
    </div>
</div>
@endsection
