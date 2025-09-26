@extends('layouts.newsApp')

@section('content')
<div class="w-full min-h-screen"
     x-data="{
        category: 'Kids',
        ministry: @js($ministry),
        cleanContent(html) {
            if (!html) return '';

            let doc = new DOMParser().parseFromString(html, 'text/html');
            let nodes = doc.body.childNodes;
            let output = '';

            nodes.forEach(node => {
                if (node.nodeName === 'DIV' || node.nodeName === 'P') {
                    let content = node.innerHTML
                        .replace(/(<br\s*\/?>\s*){2,}/gi, '</p><p>') // ganti <br><br> → paragraf baru
                        .replace(/&nbsp;/g, ' ')                     // ganti nbsp jadi spasi
                        .trim();                                     // trim spasi di awal/akhir

                    if (content.length > 0) {
                        output += `<p>${content}</p>`;
                    }
                } else {
                    let raw = (node.outerHTML || node.textContent || '').trim();
                    if (raw.length > 0) output += raw;
                }
            });

            return output;
        },
        truncateContent(html, maxWords = 50) {
            if (!html) return '';
            
            // Strip HTML tags untuk counting words
            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            let text = tempDiv.textContent || tempDiv.innerText || '';
            
            let words = text.trim().split(/\s+/);
            if (words.length <= maxWords) return this.cleanContent(html);
            
            // Jika terlalu panjang, potong dan tambah ellipsis
            let truncated = words.slice(0, maxWords).join(' ') + '...';
            return `<p>${truncated}</p>`;
        }
     }">

    {{-- Tabs Modern --}}
    <div class="w-full bg-white shadow mb-6 sticky top-0 z-10">
        <div class="flex w-full">
            <button 
                @click="category = 'Kids'"
                :class="category === 'Kids' 
                    ? 'border-b-4 border-blue-500 text-blue-600' 
                    : 'text-gray-600 hover:text-gray-800'"
                class="flex-1 px-6 py-3 font-medium text-lg text-center transition">
                Kids
            </button>
            <button 
                @click="category = 'Youth Generation'"
                :class="category === 'Youth Generation' 
                    ? 'border-b-4 border-blue-500 text-blue-600' 
                    : 'text-gray-600 hover:text-gray-800'"
                class="flex-1 px-6 py-3 font-medium text-lg text-center transition">
                Youth Generation
            </button>
            <button 
                @click="category = 'General'"
                :class="category === 'General' 
                    ? 'border-b-4 border-blue-500 text-blue-600' 
                    : 'text-gray-600 hover:text-gray-800'"
                class="flex-1 px-6 py-3 font-medium text-lg text-center transition">
                General
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div id="ministry-content" class="max-w-2xl mx-auto space-y-8 pb-32">
        <template x-for="item in ministry.filter(m => m.category === category)" :key="item.id">
            {{-- Mengubah div menjadi clickable link dengan cursor pointer --}}
            <a :href="'/ministry/' + item.slug" 
               class="bg-white shadow-sm rounded-lg overflow-hidden block hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer group">

                {{-- Gambar + Carousel --}}
                <template x-if="item.images && item.images.length > 0">
                    <div class="relative w-full bg-white"
                         x-data="{ active: 0, startX: 0, endX: 0 }"
                         @touchstart="startX = $event.touches[0].clientX"
                         @touchend="
                            endX = $event.changedTouches[0].clientX;
                            if (startX - endX > 50) {
                                active = (active + 1) % item.images.length; 
                            } else if (endX - startX > 50) {
                                active = (active - 1 + item.images.length) % item.images.length; 
                            }
                         "
                         @click.stop="">
                        
                        <div class="w-full flex items-center justify-center bg-white">
                            <template x-for="(img, i) in item.images" :key="i">
                                <img x-show="active === i"
                                     :src="'/storage/' + img.image" 
                                     :alt="item.title" 
                                     class="max-h-[500px] w-auto object-contain transition duration-500 ease-in-out group-hover:scale-105">
                            </template>
                        </div>

                        {{-- Prev / Next --}}
                        <template x-if="item.images.length > 1">
                            <div>
                                <button @click.stop="active = (active - 1 + item.images.length) % item.images.length"
                                        class="absolute top-1/2 left-2 -translate-y-1/2 bg-gray-700 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 z-10">
                                    ‹
                                </button>
                                <button @click.stop="active = (active + 1) % item.images.length"
                                        class="absolute top-1/2 right-2 -translate-y-1/2 bg-gray-700 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 z-10">
                                    ›
                                </button>
                            </div>
                        </template>

                        {{-- Indicators --}}
                        <template x-if="item.images.length > 1">
                            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-2">
                                <template x-for="(img, i) in item.images" :key="i">
                                    <div @click.stop="active = i"
                                         class="w-3 h-3 rounded-full cursor-pointer z-10"
                                         :class="active === i ? 'bg-blue-500' : 'bg-gray-300'"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Judul & Konten --}}
                <div class="p-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-red-600 transition-colors duration-300" 
                        x-text="item.title"></h2>

                    {{-- Text indikator yang sekarang hanya untuk visual --}}
                    <div class="mt-auto text-red-600 font-bold group-hover:underline transition-all duration-300">
                        FIND OUT MORE
                    </div>
                </div>
            </a>
        </template>

        {{-- Kalau kosong --}}
        <div class="text-center text-gray-500 text-lg py-10" 
             x-show="ministry.filter(m => m.category === category).length === 0">
            Belum ada ministry untuk kategori ini.
        </div>
    </div>
</div>
@endsection