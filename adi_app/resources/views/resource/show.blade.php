@extends('layouts.newsApp')

@section('content')
<div 
    x-data="{
        current: {
            id: '{{ $resource->id }}',
            title: '{{ $resource->title }}',
            embed: '{{ $resource->embed_url ?? '' }}',
            thumb: '{{ $resource->thumbnail_url ?? asset('images/default-thumbnail.jpg') }}',
            content: `{!! nl2br(e($resource->content ?? '')) !!}`,
            date: '{{ $resource->publish_at?->translatedFormat('d F Y') }}'
        },
        recents: [
            @foreach ($related as $item)
            {
                id: '{{ $item->id }}',
                title: '{{ $item->title }}',
                embed: '{{ $item->embed_url ?? '' }}',
                thumb: '{{ $item->thumbnail_url ?? asset('images/default-thumbnail.jpg') }}',
                content: `{!! nl2br(e($item->content ?? '')) !!}`,
                date: '{{ $item->publish_at?->translatedFormat('d F Y') }}'
            },
            @endforeach
        ],
        swap(index) {
            let temp = this.current;
            this.current = this.recents[index];
            this.recents[index] = temp;
        }
    }"
    class="max-w-6xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-3 gap-8"
>
    {{-- MAIN CONTENT --}}
    <div class="lg:col-span-2">
        <div class="mb-4">
            <a href="{{ route('resource.index') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-black font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Video utama --}}
        <div 
            key="video"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="mt-4"
        >
            <template x-if="current.embed">
                <div class="aspect-video mb-6">
                    <iframe :src="current.embed" class="w-full h-full rounded" allowfullscreen></iframe>
                </div>
            </template>

            <template x-if="!current.embed">
                <div class="aspect-video mb-6 bg-gray-100 flex items-center justify-center">
                    <img :src="current.thumb" :alt="current.title" class="w-full h-full object-cover rounded">
                </div>
            </template>

            {{-- Judul + konten --}}
            <h1 class="text-2xl font-bold mb-2" x-text="current.title"></h1>
            <p class="text-sm text-gray-500 mb-4" x-text="current.date"></p>
            <div 
                x-data="{ expanded: false, needsToggle: false }" 
                x-init="
                    // cek tinggi konten saat render
                    $nextTick(() => {
                        let el = $refs.content;
                        if (el.scrollHeight > 150) { // 150px ≈ 5-6 baris teks
                            needsToggle = true;
                        }
                    })
                "
            >
                {{-- konten --}}
                <div 
                    x-ref="content"
                    class="prose max-w-none overflow-hidden transition-all duration-300" 
                    :class="expanded ? 'max-h-full' : 'max-h-[150px]'"
                    x-html="current.content"
                ></div>

                {{-- tombol hanya muncul kalau teks panjang --}}
                <template x-if="needsToggle">
                    <button 
                        @click="expanded = !expanded" 
                        class="mt-2 text-blue-600 hover:underline font-medium"
                        x-text="expanded ? 'Show less' : 'Show more'">
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- RECENT LIST --}}
    <aside>
        <h2 class="text-xl font-semibold mb-4">Recent Sermons</h2>
        <div class="space-y-4 max-h-[70vh] overflow-auto pr-2">
            <template x-for="(item, index) in recents" :key="item.id">
                <div 
                    @click="swap(index)"
                    class="flex gap-4 hover:bg-gray-100 p-2 rounded cursor-pointer transition transform hover:scale-[1.02]"
                >
                    <img :src="item.thumb" class="w-32 h-20 object-cover rounded" :alt="item.title">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 line-clamp-2" x-text="item.title"></p>
                        <p class="text-sm text-gray-600" x-text="item.date"></p>
                    </div>
                </div>
            </template>
        </div>
    </aside>
</div>
@endsection
