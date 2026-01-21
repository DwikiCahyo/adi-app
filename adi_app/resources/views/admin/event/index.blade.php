<x-app-layout>
    <x-slot name="header"> 
        <div class="flex items-center sm:-my-px sm:ms-10">
            <nav class="flex gap-4">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('News Feed') }}
                </x-nav-link>
               
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open" 
                        class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none"
                    >
                        <span>Resource</span>
                        <svg 
                            class="w-4 h-4 ml-1 transform transition-transform duration-200"
                            :class="{ 'rotate-180': open }" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        x-transition
                        @click.away="open = false"
                        class="absolute left-0 mt-2 w-auto min-w-max bg-white border border-gray-200 rounded-lg shadow-lg z-50"
                    >
                        <div class="flex flex-col">
                            <x-nav-link 
                                :href="route('admin.resource.index')" 
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg"
                            >
                                + Add Latest Sermon
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('admin.resourcefile.file')" 
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg"
                            >
                                + Add Good News
                            </x-nav-link>
                        </div>
                    </div>
                </div>

                <x-nav-link :href="route('admin.event.index')" :active="request()->routeIs('admin.event.index')">
                    {{ __('Events') }}
                </x-nav-link>

                
            </nav>
        </div> 
    </x-slot>

    {{-- CKEditor CSS & JS --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    
    <div class="container mx-auto p-6">
        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-message" class="mb-4 p-4 text-green-800 bg-green-200 rounded-lg transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">📅 Daftar Events</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah Event
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="eventTable" class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">No</th>
                        <th class="px-4 py-3 border border-gray-300">Agenda</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Topic & Content</th>
                        <th class="px-4 py-3 border border-gray-300">Thumbnail</th>
                        <th class="px-4 py-3 border border-gray-300">Images</th>
                        <th class="px-4 py-3 border border-gray-300">Created At</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($event as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 border border-gray-300 font-medium text-gray-900">{{ $item->agenda }}</td>
                            <td class="px-4 py-3 border border-gray-300 font-medium text-gray-900">{{ $item->title }}</td>
                            <td class="px-4 py-3 border border-gray-300">
                                <ul class="space-y-3">
                                    @foreach($item->topics as $topic)
                                        <li class="border-b pb-2 border-gray-200">
                                            <div class="mb-1">
                                                <span class="font-bold text-black">Topic:</span>
                                                <span class="text-gray-700 text-sm leading-relaxed">{{ $topic->topic }}</span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-black">Content:</span>
                                                <span class="text-gray-700 text-sm leading-relaxed">
                                                    {!! Str::limit($topic->content, 120) !!}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->thumbnail_url)
                                    <img src="{{ $item->thumbnail_url }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-20 h-14 object-cover rounded border" 
                                         onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-20 h-14 bg-gray-200 rounded border flex items-center justify-center\'><div class=\'text-center\'><div class=\'text-xs text-gray-500 leading-tight\'>Thumbnail<br>tidak<br>tersedia</div></div></div>';">
                                @else
                                    <div class="w-20 h-14 bg-gray-200 rounded border flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <div class="text-xs text-gray-500 leading-tight">Thumbnail<br>tidak<br>tersedia</div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->images->count())
                                    @php $firstImage = $item->images->first(); @endphp
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/events/' . $firstImage->image) }}" 
                                             alt="Event Image" 
                                             class="w-16 h-16 object-cover rounded border cursor-pointer hover:scale-105 transition"
                                             onclick="openGallery({{ $item->id }}, 0)">
                                        @if($item->images->count() > 1)
                                            <span onclick="openGallery({{ $item->id }}, 0)"
                                                  class="absolute bottom-0 right-0 bg-black bg-opacity-70 text-white text-xs px-2 py-0.5 rounded cursor-pointer">
                                                +{{ $item->images->count() - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Belum ada gambar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-700">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.event.destroy', $item->id) }}" method="POST" 
                                      class="inline-block" 
                                      onsubmit="return confirm('Yakin mau hapus event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div id="editModal-{{ $item->id }}" 
                             class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
                             x-data="{ 
                                url: '{{ old('url', $item->url) }}', 
                                hasNewFiles: false, 
                                hasExistingImages: {{ $item->images->count() > 0 ? 'true' : 'false' }} 
                             }">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
                                <h2 class="text-xl font-bold mb-4">Edit Event</h2>
                                <form id="edit-form-{{ $item->id }}" action="{{ route('admin.event.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Agenda <span class="text-red-500">*</span></label>
                                        <input type="text" name="agenda" id="edit-agenda-{{ $item->id }}" 
                                               value="{{ old('agenda', $item->agenda) }}" 
                                               class="w-full border rounded p-2">
                                        <div id="edit-agenda-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" 
                                               value="{{ old('title', $item->title) }}" 
                                               class="w-full border rounded p-2">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="mb-4" x-show="!hasNewFiles && !hasExistingImages" x-transition>
                                        <label class="block text-sm font-medium mb-2">URL Video/Thumbnail YouTube (Opsional)</label>
                                        <input type="text" name="url" id="edit-url-{{ $item->id }}" 
                                               value="{{ old('url', $item->url) }}" 
                                               class="w-full border rounded p-2" 
                                               placeholder="https://youtube.com/watch?v=..."
                                               x-model="url">
                                        <p class="text-xs text-gray-500 mt-1">Masukkan URL YouTube atau Vimeo</p>
                                        <div id="edit-url-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                        @if($item->embed_url)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Preview embed saat ini:</p>
                                                <div class="mt-1 w-full aspect-video rounded border overflow-hidden">
                                                    <iframe src="{{ $item->embed_url }}" class="w-full h-full" allowfullscreen="" loading="lazy"></iframe>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div id="edit-topics-{{ $item->id }}" class="mb-4 space-y-4">
                                        <label class="block text-sm font-medium">Topics & Content<span class="text-red-500">*</span></label>
                                        @if($item->topics && $item->topics->count() > 0)
                                            @foreach($item->topics as $tIndex => $topic)
                                                <div class="border p-3 rounded topic-item space-y-2" data-topic-index="{{ $tIndex }}">
                                                    <input type="hidden" name="topics[{{ $tIndex }}][id]" value="{{ $topic->id }}">
                                                    
                                                    <input type="text" 
                                                           name="topics[{{ $tIndex }}][topic]" 
                                                           value="{{ old('topics.'.$tIndex.'.topic', $topic->topic) }}" 
                                                           class="w-full border rounded p-2" 
                                                           placeholder="Judul Topic (Opsional)">
                                                    
                                                    <textarea id="edit-content-{{ $item->id }}-{{ $tIndex }}" 
                                                              name="topics[{{ $tIndex }}][content]" 
                                                              class="ckeditor-edit" 
                                                              data-item-id="{{ $item->id }}" 
                                                              data-topic-index="{{ $tIndex }}">{{ old('topics.'.$tIndex.'.content', $topic->content) }}</textarea>
                                                    
                                                    <div class="flex justify-end">
                                                        <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus Topic</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="border p-3 rounded topic-item space-y-2" data-topic-index="0">
                                                <input type="text" name="topics[0][topic]" class="w-full border rounded p-2" placeholder="Judul Topic (Opsional)">
                                                <textarea id="edit-content-{{ $item->id }}-0" name="topics[0][content]" class="ckeditor-edit" data-item-id="{{ $item->id }}" data-topic-index="0"></textarea>
                                                <div class="flex justify-end">
                                                    <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus Topic</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div id="edit-topics-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden mb-4"></div>

                                    {{-- Existing Images --}}
                                    @if($item->images && $item->images->count() > 0)
                                        <div class="mb-4" id="existing-images-section-{{ $item->id }}">
                                            <label class="block text-sm font-medium mb-2">Gambar Saat Ini ({{ $item->images->count() }} gambar)</label>
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="existing-images-container-{{ $item->id }}">
                                                @foreach($item->images as $image)
                                                    <div class="relative group" id="image-container-{{ $image->id }}" data-image-id="{{ $image->id }}">
                                                        <img 
                                                            src="{{ asset('storage/events/' . $image->image) }}" 
                                                            alt="Image {{ $loop->iteration }}" 
                                                            class="w-full h-24 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                                                        >
                                                        <button 
                                                            type="button"
                                                            onclick="removeExistingImage({{ $image->id }}, {{ $item->id }})"
                                                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-700 opacity-0 group-hover:opacity-100 transition-opacity"
                                                            title="Hapus gambar"
                                                        >
                                                            ×
                                                        </button>
                                                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b truncate">
                                                            {{ basename($image->image) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="remove_images" id="remove-images-{{ $item->id }}" value="">
                                        </div>
                                    @endif

                                    {{-- Field Upload Gambar Baru (Conditional) --}}
                                    <div class="mb-4" x-show="!url.trim()" x-transition>
                                        <label class="block text-sm font-medium mb-2">Tambah Gambar Baru</label>
                                        <input 
                                            type="file" 
                                            name="images[]" 
                                            id="edit-images-{{ $item->id }}" 
                                            class="w-full border rounded p-2" 
                                            multiple 
                                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                            @change="hasNewFiles = $event.target.files.length > 0"
                                            onchange="previewImages(this, 'edit-preview-{{ $item->id }}')"
                                        >
                                        <div class="text-xs text-gray-500 mt-1">
                                            Pilih maksimal 10 gambar. Format: JPG, PNG, GIF, WEBP. Maksimal 5MB per gambar.
                                        </div>
                                        <div id="edit-preview-{{ $item->id }}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                                        <div id="edit-images-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    <div class="text-sm text-blue-600 bg-blue-50 p-3 rounded-lg mb-4">
                                        <p x-show="url.trim()">⚠️ Anda tidak bisa mengupload/menambah gambar baru karena field URL terisi.</p>
                                        <p x-show="hasNewFiles">⚠️ Anda tidak bisa mengisi URL karena ada gambar baru yang akan diupload.</p>
                                        <p x-show="hasExistingImages && !url.trim() && !hasNewFiles">ℹ️ Anda tidak dapat mengisi URL karena sudah ada gambar. Hapus semua gambar jika ingin beralih ke URL.</p>
                                        <p x-show="!url.trim() && !hasNewFiles && !hasExistingImages">ℹ️ Silakan isi URL atau Upload Gambar.</p>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="closeModal('editModal-{{ $item->id }}')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Create --}}
    <div id="createModal" 
         class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
         x-data="{ url: '{{ old('url', '') }}', hasFiles: {{ old('images') ? 'true' : 'false' }} }">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-xl font-bold mb-4">Tambah Event</h2>
            <form id="create-form" action="{{ route('admin.event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Agenda <span class="text-red-500">*</span></label>
                    <input type="text" name="agenda" id="create-agenda" value="{{ old('agenda') }}" class="w-full border rounded p-2" placeholder="Masukkan agenda event">
                    <div id="create-agenda-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2" placeholder="Masukkan judul event">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div class="mb-4" x-show="!hasFiles" x-transition>
                    <label class="block text-sm font-medium mb-2">URL Video/Thumbnail YouTube (Opsional)</label>
                    <input type="text" name="url" id="create-url" value="{{ old('url') }}" class="w-full border rounded p-2" placeholder="https://youtube.com/watch?v=..." x-model="url">
                    <p class="text-xs text-gray-500 mt-1">Masukkan URL YouTube atau Vimeo</p>
                    <div id="create-url-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div id="topics-container" class="mb-4 space-y-4">
                    <label class="block text-sm font-medium">Topics & Content<span class="text-red-500">*</span></label>
                    <div class="border p-3 rounded topic-item space-y-2" data-topic-index="0">
                        <input type="text" name="topics[0][topic]" value="{{ old('topics.0.topic') }}" class="w-full border rounded p-2" placeholder="Judul Topic (Opsional)">
                        <textarea id="create-content-0" name="topics[0][content]" class="ckeditor-create" data-topic-index="0">{{ old('topics.0.content') }}</textarea>
                        <div class="flex justify-end">
                            <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus Topic</button>
                        </div>
                    </div>
                </div>
                
                <div id="create-topics-error" class="text-red-600 text-sm mt-1 hidden mb-4"></div>

                <div class="flex justify-end mb-4">
                    <button type="button" onclick="addCreateTopic()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">+ Tambah Topic</button>
                </div>

                <div class="mb-4" x-show="!url.trim()" x-transition>
                    <label class="block text-sm font-medium mb-2">Upload Images (Opsional)</label>
                    <input type="file" name="images[]" id="create-images" class="w-full border rounded p-2" multiple accept="image/*"
                           @change="hasFiles = $event.target.files.length > 0"
                           onchange="previewImages(this, 'create-preview')">
                    <p class="text-xs text-gray-500 mt-1">Pilih maksimal 10 gambar. Format: JPG, PNG, GIF, WEBP. Maksimal 5MB per gambar.</p>
                    <div id="create-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                    <div id="create-images-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div class="text-sm text-blue-600 bg-blue-50 p-3 rounded-lg mb-4">
                    <p x-show="url.trim()">⚠️ Anda tidak bisa mengupload gambar karena field URL terisi.</p>
                    <p x-show="hasFiles">⚠️ Anda tidak bisa mengisi URL karena ada gambar yang akan diupload.</p>
                    <p x-show="!url.trim() && !hasFiles">ℹ️ Silakan isi URL atau Upload Gambar (salah satu).</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Gallery Modal --}}
    <div id="galleryModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center">
        <button onclick="closeModal('galleryModal')" class="absolute top-4 right-6 text-white text-2xl hover:text-gray-300">✕</button>
        <button onclick="prevImage()" class="absolute left-4 text-white text-3xl hover:text-gray-300">❮</button>
        <img id="galleryImage" src="" class="max-h-screen max-w-4xl object-contain rounded shadow-lg">
        <button onclick="nextImage()" class="absolute right-4 text-white text-3xl hover:text-gray-300">❯</button>
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
            <span id="imageCounter"></span>
        </div>
    </div>

    {{-- DataTable CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        let createEditors = {}, editEditors = {};
        let createTopicIndex = 0, editTopicIndexes = {};
        let removedImages = {};
    
        $(document).ready(function () {
            $('#eventTable').DataTable({
                responsive: {
                    breakpoints: [
                        { name: 'desktop', width: Infinity },
                        { name: 'tablet',  width: 1024 },
                        { name: 'mobile',  width: 640 }
                    ],
                    details: {
                        renderer: function ( api, rowIdx, columns ) {
                            let data = $.map(columns, function (col) {
                                return col.hidden
                                    ? `<div class="flex flex-col sm:flex-row sm:items-start sm:gap-2 py-2 border-b">
                                            <span class="font-bold text-gray-800 min-w-[100px]">${col.title} :</span>
                                            <span class="text-gray-600 break-words">${col.data}</span>
                                       </div>`
                                    : '';
                            }).join('');
                            return data ? $('<div class="p-3"/>').append(data) : false;
                        }
                    }
                },
                pageLength: 10,
                lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "Semua"] ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari total _MAX_ data)",
                    emptyTable: "Belum ada data event."
                }
            });
    
            @if($errors->any())
                openModal('createModal');
            @endif
    
            initializeCreateEditors();
            setupFormValidation();
        });
    
        // ========== CKEditor Initialization ==========
        function initializeCreateEditors() {
            const editorIds = ['create-content-0'];
            editorIds.forEach(id => {
                const element = document.getElementById(id);
                if (element && !createEditors[id]) {
                    ClassicEditor
                        .create(element, getEditorConfig())
                        .then(editor => {
                            createEditors[id] = editor;
                        })
                        .catch(error => {
                            console.error(`CKEditor initialization failed for ${id}:`, error);
                        });
                }
            });
        }
    
        function initializeEditEditors(itemId) {
            const editTopics = document.querySelectorAll(`#edit-topics-${itemId} .ckeditor-edit`);
            editTopics.forEach(textarea => {
                const editorId = textarea.id;
                if (!editEditors[editorId]) {
                    ClassicEditor
                        .create(textarea, getEditorConfig())
                        .then(editor => {
                            editEditors[editorId] = editor;
                            if (textarea.textContent || textarea.value) {
                                editor.setData(textarea.value || textarea.textContent);
                            }
                        })
                        .catch(error => {
                            console.error(`CKEditor initialization failed for ${editorId}:`, error);
                        });
                }
            });
        }
    
        function getEditorConfig() {
            return {
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'outdent',
                        'indent',
                        '|',
                        'blockQuote',
                        'insertTable',
                        'undo',
                        'redo'
                    ]
                },
                language: 'id',
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                }
            };
        }
    
        // ========== Topic Management ==========
        function addCreateTopic() {
            createTopicIndex++;
            const container = document.getElementById('topics-container');
            const div = document.createElement('div');
            div.classList.add('border', 'p-3', 'rounded', 'topic-item', 'space-y-2');
            div.setAttribute('data-topic-index', createTopicIndex);
            div.innerHTML = `
                <input type="text" name="topics[${createTopicIndex}][topic]" class="w-full border rounded p-2" placeholder="Judul Topic (Opsional)">
                <textarea id="create-content-${createTopicIndex}" name="topics[${createTopicIndex}][content]" class="ckeditor-create" data-topic-index="${createTopicIndex}"></textarea>
                <div class="flex justify-end">
                    <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus Topic</button>
                </div>
            `;
            container.appendChild(div);
    
            const editorId = `create-content-${createTopicIndex}`;
            setTimeout(() => {
                ClassicEditor
                    .create(document.getElementById(editorId), getEditorConfig())
                    .then(editor => {
                        createEditors[editorId] = editor;
                    })
                    .catch(error => {
                        console.error(`CKEditor initialization failed for ${editorId}:`, error);
                    });
            }, 100);
        }
    
        document.addEventListener('click', function(e) {
            if(e.target.classList.contains('remove-topic')) {
                const topicItem = e.target.closest('.topic-item');
                const textarea = topicItem.querySelector('textarea[id^="create-content-"], textarea[id^="edit-content-"]');
                if (textarea && textarea.id) {
                    if (createEditors[textarea.id]) {
                        createEditors[textarea.id].destroy();
                        delete createEditors[textarea.id];
                    }
                    if (editEditors[textarea.id]) {
                        editEditors[textarea.id].destroy();
                        delete editEditors[textarea.id];
                    }
                }
                topicItem.remove();
            }
        });

        // ========== Image Functions ==========
        function previewImages(input, previewContainerId) {
            const previewContainer = document.getElementById(previewContainerId);
            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imageDiv = document.createElement('div');
                            imageDiv.className = 'relative group';
                            imageDiv.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-24 object-cover rounded border" alt="Preview ${index + 1}">
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b truncate">
                                    ${file.name}
                                </div>
                            `;
                            previewContainer.appendChild(imageDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        function removeExistingImage(imageId, itemId) {
            if (confirm('Yakin ingin menghapus gambar ini?')) {
                const container = document.getElementById(`image-container-${imageId}`);
                if (container) {
                    container.style.opacity = '0.3';
                    container.innerHTML += '<div class="absolute inset-0 bg-red-100 bg-opacity-75 flex items-center justify-center z-10"><span class="text-red-600 text-sm font-bold">DIHAPUS</span></div>';
                    
                    if (!removedImages[itemId]) {
                        removedImages[itemId] = [];
                    }
                    
                    if (!removedImages[itemId].includes(imageId)) {
                        removedImages[itemId].push(imageId);
                    }
                    
                    const removeInput = document.getElementById(`remove-images-${itemId}`);
                    if (removeInput) {
                        removeInput.value = removedImages[itemId].join(',');
                    }
                    
                    updateImageCounter(itemId);
                }
            }
        }

        function updateImageCounter(itemId) {
            const existingSection = document.getElementById(`existing-images-section-${itemId}`);
            if (existingSection) {
                const container = document.getElementById(`existing-images-container-${itemId}`);
                const totalImages = container.querySelectorAll('[data-image-id]').length;
                const removedCount = removedImages[itemId] ? removedImages[itemId].length : 0;
                const remainingImages = totalImages - removedCount;
                
                const label = existingSection.querySelector('label');
                if (label) {
                    label.textContent = `Gambar Saat Ini (${remainingImages} dari ${totalImages} gambar)`;
                }
            }
        }
    
        // ========== Form Validation ==========
        function setupFormValidation() {
            // Create Form Validation
            $('#create-form').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');
    
                // Sync CKEditor data to textarea
                Object.keys(createEditors).forEach(editorId => {
                    if (createEditors[editorId]) {
                        const textarea = document.getElementById(editorId);
                        if (textarea) {
                            textarea.value = createEditors[editorId].getData();
                        }
                    }
                });
    
                // Validate Agenda
                const agenda = $('#create-agenda').val().trim();
                if (!agenda) {
                    showError('create-agenda-error', 'Agenda harus diisi');
                    isValid = false;
                } else if (agenda.length < 3) {
                    showError('create-agenda-error', 'Agenda minimal 3 karakter');
                    isValid = false;
                } else if (agenda.length > 255) {
                    showError('create-agenda-error', 'Agenda maksimal 255 karakter');
                    isValid = false;
                }
    
                // Validate Title
                const title = $('#create-title').val().trim();
                if (!title) {
                    showError('create-title-error', 'Title harus diisi');
                    isValid = false;
                } else if (title.length < 3) {
                    showError('create-title-error', 'Title minimal 3 karakter');
                    isValid = false;
                } else if (title.length > 255) {
                    showError('create-title-error', 'Title maksimal 255 karakter');
                    isValid = false;
                }

                // Validate URL and Images (Mutual Exclusive)
                const url = $('#create-url').val().trim();
                const imagesInput = document.getElementById('create-images');
                
                if (url && imagesInput.files.length > 0) {
                    showError('create-url-error', 'URL dan Upload gambar tidak boleh diisi bersamaan');
                    showError('create-images-error', 'URL dan Upload gambar tidak boleh diisi bersamaan');
                    isValid = false;
                }
                
                if (url && !isValidUrl(url)) {
                    showError('create-url-error', 'URL harus berupa alamat yang valid');
                    isValid = false;
                }
    
                // Validate Topics Content
                let hasValidContent = false;
                const topicItems = document.querySelectorAll('#topics-container .topic-item');
                
                topicItems.forEach((item, index) => {
                    const textarea = item.querySelector('textarea[name*="content"]');
                    if (textarea) {
                        const content = textarea.value.trim();
                        const textContent = content.replace(/<[^>]*>/g, '').trim();
                        
                        if (textContent && textContent.length >= 10) {
                            hasValidContent = true;
                        } else if (textContent && textContent.length > 0 && textContent.length < 10) {
                            showContentError(textarea.id, 'Content minimal 10 karakter');
                            isValid = false;
                        } else if (!textContent || textContent.length === 0) {
                            showContentError(textarea.id, 'Content wajib diisi');
                            isValid = false;
                        }
                    }
                });
    
                if (!hasValidContent && topicItems.length > 0) {
                    showError('create-topics-error', 'Setidaknya satu topic harus memiliki content yang valid (minimal 10 karakter)');
                    isValid = false;
                }
    
                // Validate Images
                if (imagesInput.files.length > 0) {
                    for (let file of imagesInput.files) {
                        if (!file.type.startsWith('image/')) {
                            showError('create-images-error', 'Semua file harus berupa gambar');
                            isValid = false;
                            break;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            showError('create-images-error', 'Ukuran file maksimal 5MB per gambar');
                            isValid = false;
                            break;
                        }
                    }
                }
    
                if (!isValid) {
                    e.preventDefault();
                }
            });
    
            // Edit Form Validation
            $('[id^="edit-form-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[2];
                let isValid = true;
                clearErrors('edit', itemId);
    
                // Sync CKEditor data to textarea
                Object.keys(editEditors).forEach(editorId => {
                    if (editEditors[editorId] && editorId.includes(`-${itemId}-`)) {
                        const textarea = document.getElementById(editorId);
                        if (textarea) {
                            textarea.value = editEditors[editorId].getData();
                        }
                    }
                });
    
                // Validate Agenda
                const agenda = $(`#edit-agenda-${itemId}`).val().trim();
                if (!agenda) {
                    showError(`edit-agenda-error-${itemId}`, 'Agenda harus diisi');
                    isValid = false;
                } else if (agenda.length < 3) {
                    showError(`edit-agenda-error-${itemId}`, 'Agenda minimal 3 karakter');
                    isValid = false;
                } else if (agenda.length > 255) {
                    showError(`edit-agenda-error-${itemId}`, 'Agenda maksimal 255 karakter');
                    isValid = false;
                }
    
                // Validate Title
                const title = $(`#edit-title-${itemId}`).val().trim();
                if (!title) {
                    showError(`edit-title-error-${itemId}`, 'Title harus diisi');
                    isValid = false;
                } else if (title.length < 3) {
                    showError(`edit-title-error-${itemId}`, 'Title minimal 3 karakter');
                    isValid = false;
                } else if (title.length > 255) {
                    showError(`edit-title-error-${itemId}`, 'Title maksimal 255 karakter');
                    isValid = false;
                }

                // Validate URL and Images (Mutual Exclusive)
                const url = $(`#edit-url-${itemId}`).val().trim();
                const imagesInput = document.getElementById(`edit-images-${itemId}`);
                
                if (url && imagesInput && imagesInput.files.length > 0) {
                    showError(`edit-url-error-${itemId}`, 'URL dan Upload gambar tidak boleh diisi bersamaan');
                    showError(`edit-images-error-${itemId}`, 'URL dan Upload gambar tidak boleh diisi bersamaan');
                    isValid = false;
                }
                
                if (url && !isValidUrl(url)) {
                    showError(`edit-url-error-${itemId}`, 'URL harus berupa alamat yang valid');
                    isValid = false;
                }
    
                // Validate Topics Content
                let hasValidContent = false;
                const topicItems = document.querySelectorAll(`#edit-topics-${itemId} .topic-item`);
                
                topicItems.forEach((item, index) => {
                    const textarea = item.querySelector('textarea[name*="content"]');
                    if (textarea) {
                        const content = textarea.value.trim();
                        const textContent = content.replace(/<[^>]*>/g, '').trim();
                        
                        if (textContent && textContent.length > 0) {
                            hasValidContent = true;
                        } else if (content && content.length > 0 && content.length < 10) {
                            showContentError(textarea.id, 'Content minimal 10 karakter');
                            isValid = false;
                        }
                    }
                });
    
                if (!hasValidContent) {
                    showError(`edit-topics-error-${itemId}`, 'Setidaknya satu topic harus memiliki content');
                    isValid = false;
                }
    
                // Validate Images
                if (imagesInput && imagesInput.files.length > 0) {
                    for (let file of imagesInput.files) {
                        if (!file.type.startsWith('image/')) {
                            showError(`edit-images-error-${itemId}`, 'Semua file harus berupa gambar');
                            isValid = false;
                            break;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            showError(`edit-images-error-${itemId}`, 'Ukuran file maksimal 5MB per gambar');
                            isValid = false;
                            break;
                        }
                    }
                }
    
                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    
        // ========== Error Handling Functions ==========
        function showContentError(textareaId, message) {
            let errorId = textareaId + '-error';
            let errorElement = document.getElementById(errorId);
            
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.id = errorId;
                errorElement.className = 'text-red-600 text-sm mt-1';
                
                const textarea = document.getElementById(textareaId);
                if (textarea) {
                    const ckContainer = textarea.nextElementSibling;
                    if (ckContainer && ckContainer.classList.contains('ck-editor')) {
                        ckContainer.parentNode.insertBefore(errorElement, ckContainer.nextSibling);
                    } else {
                        textarea.parentNode.insertBefore(errorElement, textarea.nextSibling);
                    }
                }
            }
            
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            
            const textarea = document.getElementById(textareaId);
            if (textarea) {
                const ckContainer = textarea.nextElementSibling;
                if (ckContainer && ckContainer.classList.contains('ck-editor')) {
                    ckContainer.style.border = '2px solid #ef4444';
                }
            }
        }
    
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                
                const inputId = elementId.replace('-error', '');
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                }
            }
        }
    
        function clearErrors(type, itemId = '') {
            const suffix = itemId ? `-${itemId}` : '';
            const fields = ['agenda', 'title', 'url', 'images', 'topics'];
            
            fields.forEach(field => {
                const errorId = `${type}-${field}-error${suffix}`;
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
                
                const inputId = `${type}-${field}${suffix}`;
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    inputElement.classList.remove('border-red-500');
                }
            });
            
            // Clear content errors
            const contentErrors = document.querySelectorAll(`[id$="-content-error"]`);
            contentErrors.forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });
            
            // Reset CKEditor borders
            const ckEditors = document.querySelectorAll('.ck-editor');
            ckEditors.forEach(editor => {
                editor.style.border = '';
            });
        }
    
        function isValidUrl(string) {
            try {
                const url = new URL(string);
                return url.protocol === 'http:' || url.protocol === 'https:';
            } catch (_) {
                return false;
            }
        }
    
        // ========== Modal Functions ==========
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            clearErrors('create');
            
            if (id === 'createModal') {
                document.getElementById('create-form').reset();
                Object.keys(createEditors).forEach(editorId => {
                    if (createEditors[editorId]) {
                        createEditors[editorId].setData('');
                    }
                });
            }
        }
    
        function openEditModal(itemId) {
            const modalId = `editModal-${itemId}`;
            document.getElementById(modalId).classList.remove('hidden');
            clearErrors('edit', itemId);
            
            removedImages[itemId] = [];
            const removeInput = document.getElementById(`remove-images-${itemId}`);
            if (removeInput) {
                removeInput.value = '';
            }
            
            const existingImages = document.querySelectorAll(`#editModal-${itemId} [id^="image-container-"]`);
            existingImages.forEach(container => {
                container.style.opacity = '1';
                const overlay = container.querySelector('.absolute.inset-0');
                if (overlay) {
                    overlay.remove();
                }
            });
            
            updateImageCounter(itemId);
            
            setTimeout(() => {
                initializeEditEditors(itemId);
            }, 100);
        }
    
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    
        // ========== Flash Message ==========
        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);
    
        // ========== Gallery Functions ==========
        let galleries = @json($event->mapWithKeys(fn($e) => [$e->id => $e->images->pluck('image')]));
        let currentGallery = [];
        let currentIndex = 0;
    
        function openGallery(eventId, index) {
            currentGallery = galleries[eventId].map(img => '/storage/events/' + img);
            currentIndex = index;
            showGalleryImage();
            openModal('galleryModal');
        }
    
        function showGalleryImage() {
            document.getElementById('galleryImage').src = currentGallery[currentIndex];
            document.getElementById('imageCounter').textContent = `${currentIndex + 1} / ${currentGallery.length}`;
        }
    
        function nextImage() {
            if (currentGallery.length > 0) {
                currentIndex = (currentIndex + 1) % currentGallery.length;
                showGalleryImage();
            }
        }
    
        function prevImage() {
            if (currentGallery.length > 0) {
                currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
                showGalleryImage();
            }
        }
    
        // ========== Debug Errors ==========
        @if($errors->any())
            @foreach($errors->all() as $error)
                console.log('Validation error: {{ $error }}');
            @endforeach
        @endif
    </script>
</x-app-layout>