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
    
    <div class="container mx-auto p-6">
        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-message" class="mb-4 p-4 text-green-800 bg-green-200 rounded-lg transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        {{-- Flash Error Message --}}
        @if(session('error') || $errors->any())
            <div id="flash-error" class="mb-4 p-4 text-red-800 bg-red-200 rounded-lg transition-opacity duration-500">
                @if(session('error'))
                    {{ session('error') }}
                @endif
                @if($errors->any())
                    Ada kesalahan validasi pada input Anda. Silakan periksa kembali form.
                @endif
            </div>
        @endif

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">📰 News Feed Management</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah News
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="newsTable" class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Status</th>
                        <th class="px-4 py-3 border border-gray-300">Visibility</th>
                        <th class="px-4 py-3 border border-gray-300">Tanggal Publish</th>
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Thumbnail</th>
                        <th class="px-4 py-3 border border-gray-300">Images</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($news as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300 text-center"></td>
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-xs break-words">
                                {{ $item->title }}
                            </td>
                            
                            {{-- Status Column --}}
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->status === 'published')
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Published</span>
                                @elseif($item->status === 'scheduled')
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Scheduled</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Draft</span>
                                @endif
                            </td>

                            {{-- Visibility Column --}}
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->is_displayed_in_frontend)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Ditampilkan
                                        </span>
                                        <span class="text-xs text-blue-600 font-medium">Aktif</span>
                                    </div>
                                @elseif($item->status === 'published')
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                            </svg>
                                            Tidak Ditampilkan
                                        </span>
                                        <span class="text-xs text-gray-500">Tidak Aktif (Hanya bisa di lihat Admin)</span>
                                    </div>
                                @elseif($item->status === 'scheduled')
                                    <span class="px-2 py-1 text-xs font-semibold text-purple-600 bg-purple-100 rounded-full">
                                        ⏳ Akan Ditampilkan Nanti
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-400 bg-gray-50 rounded-full">
                                        📝 Draft
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal Publish Column --}}
                            <td class="px-4 py-3 border border-gray-300 text-gray-600">
                                @if($item->publish_at)
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $item->publish_at->format('d M Y, H:i') }} WIB</span>
                                        @if($item->status === 'scheduled')
                                            <span class="text-xs text-blue-600 font-semibold countdown-timer" 
                                                  data-publish="{{ $item->publish_at->timestamp }}"
                                                  data-id="{{ $item->id }}">
                                                ⏱️ Calculating...
                                            </span>
                                        @elseif($item->status === 'published')
                                            <span class="text-xs text-green-600">
                                                ✅ Published {{ $item->publish_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-700 max-w-md break-words">
                                {!! Str::limit($item->content, 200) !!}
                            </td>
                            
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->thumbnail_url && filter_var($item->thumbnail_url, FILTER_VALIDATE_URL))
                                    <img 
                                        src="{{ $item->thumbnail_url }}" 
                                        alt="{{ $item->title }}" 
                                        class="w-20 h-14 object-cover rounded border"
                                        onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-20 h-14 bg-gray-200 rounded border flex items-center justify-center\'><div class=\'text-center\'><div class=\'text-xs text-gray-500 leading-tight\'>Thumbnail<br>tidak<br>tersedia</div></div></div>';"
                                    >
                                @elseif($item->images && $item->images->count() > 0)
                                    <img 
                                        src="{{ asset('storage/' . $item->images->first()->image) }}" 
                                        alt="{{ $item->title }}" 
                                        class="w-20 h-14 object-cover rounded border"
                                        onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-20 h-14 bg-gray-200 rounded border flex items-center justify-center\'><div class=\'text-center\'><div class=\'text-xs text-gray-500 leading-tight\'>Thumbnail<br>tidak<br>tersedia</div></div></div>';"
                                    >
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
                                @if($item->images && $item->images->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item->images->take(3) as $image)
                                            <img 
                                                src="{{ asset('storage/' . $image->image) }}" 
                                                alt="Image" 
                                                class="w-12 h-12 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                                                title="Image {{ $loop->iteration }} - Klik untuk zoom"
                                                onclick="openImageModal('{{ asset('storage/' . $image->image) }}', 'Image {{ $loop->iteration }}', {{ json_encode($item->images->map(fn($img) => asset('storage/' . $img->image))) }})"
                                            >
                                        @endforeach
                                        @if($item->images->count() > 3)
                                            <div class="w-12 h-12 bg-gray-200 rounded border flex items-center justify-center text-xs text-gray-600">
                                                +{{ $item->images->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $item->images->count() }} gambar</div>
                                @else
                                    <span class="text-gray-400 italic">No Images</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.dashboard.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div 
                            id="editModal-{{ $item->id }}" 
                            class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
                            x-data="{ 
                                url: '{{ old('url', $item->url) }}', 
                                hasNewFiles: false, 
                                hasExistingImages: {{ $item->images->count() > 0 ? 'true' : 'false' }} 
                            }"
                        >
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
                                <h2 class="text-xl font-bold mb-4">Edit News</h2>
                                <form id="editForm-{{ $item->id }}" action="{{ route('admin.dashboard.update', $item) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" class="w-full border rounded p-2" value="{{ old('title', $item->title) }}">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Tanggal Publish --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal" id="edit-tanggal-{{ $item->id }}" value="{{ old('tanggal', $item->publish_at ? $item->publish_at->format('Y-m-d') : '') }}" class="w-full border rounded p-2">
                                        <p class="text-xs text-gray-500 mt-1">📅 Pilih hari ini untuk publish sekarang, atau pilih tanggal lain untuk publish jam 00:00 WIB</p>
                                        <div id="edit-tanggal-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                                        <textarea name="content" id="edit-content-{{ $item->id }}" class="ckeditor-edit">{{ old('content', $item->content) }}</textarea>
                                        <div id="edit-content-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    {{-- Field URL Thumbnail (Conditional) --}}
                                    <div class="mb-4" x-show="!hasNewFiles && !hasExistingImages" x-transition>
                                        <label class="block text-sm font-medium mb-2">URL Thumbnail</label>
                                        <input type="text" name="url" id="edit-url-{{ $item->id }}" class="w-full border rounded p-2" value="{{ old('url', $item->url) }}" placeholder="Isi URL atau Upload Gambar di bawah" x-model="url">
                                        <div id="edit-url-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Existing Images --}}
                                    @if($item->images && $item->images->count() > 0)
                                        <div class="mb-4" id="existing-images-section-{{ $item->id }}">
                                            <label class="block text-sm font-medium mb-2">Gambar Saat Ini ({{ $item->images->count() }} gambar)</label>
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="existing-images-container-{{ $item->id }}">
                                                @foreach($item->images as $image)
                                                    <div class="relative group" id="image-container-{{ $image->id }}" data-image-id="{{ $image->id }}">
                                                        <img 
                                                            src="{{ asset('storage/' . $image->image) }}" 
                                                            alt="Image {{ $loop->iteration }}" 
                                                            class="w-full h-24 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                                                            onclick="openImageModal('{{ asset('storage/' . $image->image) }}', 'Image {{ $loop->iteration }}', {{ json_encode($item->images->map(fn($img) => asset('storage/' . $img->image))) }})"
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
                                        <p x-show="url.trim()">⚠️ Anda tidak bisa mengupload/menambah gambar baru karena field URL Thumbnail terisi.</p>
                                        <p x-show="hasNewFiles">⚠️ Anda tidak bisa mengisi URL Thumbnail karena ada gambar baru yang akan diupload.</p>
                                        <p x-show="hasExistingImages && !url.trim() && !hasNewFiles">ℹ️ Anda tidak dapat mengisi URL Thumbnail karena sudah ada gambar. Hapus semua gambar jika ingin beralih ke thumbnail.</p>
                                        <p x-show="!url.trim() && !hasNewFiles && !hasExistingImages">ℹ️ Silakan isi URL Thumbnail atau Upload Gambar.</p>
                                    </div>

                                    <div class="flex justify-end space-x-2 mt-4">
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

    {{-- Image Zoom Modal --}}
    <div id="imageZoomModal" class="hidden fixed inset-0 z-[9999] bg-black bg-opacity-90 flex items-center justify-center">
        <div class="relative w-full h-full flex items-center justify-center p-4">
            {{-- Close Button --}}
            <button onclick="closeImageModal()" class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Tutup (Esc)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            {{-- Navigation Previous --}}
            <button id="prevImageBtn" onclick="navigateImage(-1)" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Gambar Sebelumnya (←)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            {{-- Navigation Next --}}
            <button id="nextImageBtn" onclick="navigateImage(1)" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Gambar Selanjutnya (→)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            {{-- Zoom Controls --}}
            <div class="absolute top-4 left-4 z-10 flex gap-2">
                <button onclick="zoomImage(0.1)" class="bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Zoom In (+)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
                <button onclick="zoomImage(-0.1)" class="bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Zoom Out (-)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                </button>
                <button onclick="resetZoom()" class="bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-all" title="Reset Zoom (0)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
            {{-- Image Info --}}
            <div class="absolute bottom-4 left-4 z-10 bg-black bg-opacity-50 text-white px-4 py-2 rounded-lg">
                <div id="imageInfo" class="text-sm">
                    <span id="imageTitle">Image Title</span>
                    <div class="text-xs opacity-75">
                        <span id="imageIndex">1</span> / <span id="totalImages">1</span> | Zoom: <span id="zoomLevel">100%</span>
                    </div>
                </div>
            </div>
            {{-- Main Image Container --}}
            <div class="relative w-full h-full flex items-center justify-center overflow-hidden" id="imageContainer">
                <img id="zoomImage" src="" alt="" class="max-w-none max-h-none transition-transform duration-200 ease-out cursor-move" style="transform: scale(1) translate(0, 0);" draggable="false">
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    <div 
        id="createModal" 
        class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        x-data="{ url: '{{ old('url', '') }}', hasFiles: {{ old('images') ? 'true' : 'false' }} }"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Tambah News</h2>
            <form id="createForm" action="{{ route('admin.dashboard.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                {{-- Tanggal Publish --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="create-tanggal" value="{{ old('tanggal', now('Asia/Jakarta')->format('Y-m-d')) }}" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">📅 Pilih hari ini untuk publish sekarang, atau pilih tanggal lain untuk publish jam 00:00 WIB</p>
                    <div id="create-tanggal-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" id="create-content" class="ckeditor-create">{{ old('content') }}</textarea>
                    <div id="create-content-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                {{-- Field URL Thumbnail (Conditional) --}}
                <div class="mb-4" x-show="!hasFiles" x-transition>
                    <label class="block text-sm font-medium mb-2">URL Thumbnail</label>
                    <input type="text" name="url" id="create-url" value="{{ old('url') }}" class="w-full border rounded p-2" placeholder="Isi URL atau Upload Gambar di bawah" x-model="url">
                    <div id="create-url-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                {{-- Field Upload Gambar (Conditional) --}}
                <div class="mb-4" x-show="!url.trim()" x-transition>
                    <label class="block text-sm font-medium mb-2">Upload Gambar</label>
                    <input 
                        type="file" 
                        name="images[]" 
                        id="create-images" 
                        class="w-full border rounded p-2" 
                        multiple 
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        @change="hasFiles = $event.target.files.length > 0"
                        onchange="previewImages(this, 'create-preview')"
                    >
                    <div class="text-xs text-gray-500 mt-1">
                        Pilih maksimal 10 gambar. Format: JPG, PNG, GIF, WEBP. Maksimal 5MB per gambar.
                    </div>
                    <div id="create-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                    <div id="create-images-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                <div class="text-sm text-blue-600 bg-blue-50 p-3 rounded-lg mb-4">
                    <p x-show="url.trim()">⚠️ Anda tidak bisa mengupload gambar karena field URL Thumbnail terisi.</p>
                    <p x-show="hasFiles">⚠️ Anda tidak bisa mengisi URL Thumbnail karena ada gambar yang akan diupload.</p>
                    <p x-show="!url.trim() && !hasFiles">ℹ️ Silakan isi URL Thumbnail atau Upload Gambar (salah satu).</p>
                </div>
                
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    {{-- Custom CSS for Countdown Timer --}}
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .countdown-timer {
            transition: all 0.3s ease;
        }
        .countdown-timer:hover {
            transform: scale(1.05);
        }
    </style>
    
    <script>
        let createEditor, editEditors = {};
        let removedImages = {};

        $(document).ready(function () {
            let table = $('#newsTable').DataTable({
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
                    emptyTable: "Belum ada data news."
                },
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false
                }],
                order: [[4, 'desc']]
            });

            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each((cell, i) => {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            @if($errors->any())
                openModal('createModal');
            @endif

            initializeCreateEditor();
            setupFormValidation();
            autoHideFlashMessages();
        });

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

        function initializeCreateEditor() {
            if (document.querySelector('#create-content')) {
                ClassicEditor
                    .create(document.querySelector('#create-content'), {
                        toolbar: {
                            items: [
                                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                                '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                            ]
                        },
                        language: 'id'
                    })
                    .then(editor => {
                        createEditor = editor;
                    })
                    .catch(error => {
                        console.error('Create editor error:', error);
                    });
            }
        }

        function initializeEditEditor(itemId) {
            if (editEditors[itemId]) {
                return;
            }

            const editorElement = document.querySelector(`#edit-content-${itemId}`);
            if (editorElement) {
                ClassicEditor
                    .create(editorElement, {
                        toolbar: {
                            items: [
                                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                                '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                            ]
                        },
                        language: 'id'
                    })
                    .then(editor => {
                        editEditors[itemId] = editor;
                    })
                    .catch(error => {
                        console.error(`Edit editor error for item ${itemId}:`, error);
                    });
            }
        }

        function setupFormValidation() {
            $('#createForm').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');

                const title = $('#create-title').val().trim();
                if (!title) {
                    showError('create-title-error', 'Title harus diisi');
                    isValid = false;
                }

                const tanggal = $('#create-tanggal').val();
                if (!tanggal) {
                    showError('create-tanggal-error', 'Tanggal publish harus diisi');
                    isValid = false;
                }

                const content = createEditor ? createEditor.getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError('create-content-error', 'Content harus diisi');
                    isValid = false;
                }

                const url = $('#create-url').val().trim();
                if (url && !isValidUrl(url)) {
                    showError('create-url-error', 'URL thumbnail harus berupa URL yang valid');
                    isValid = false;
                }

                const imageInput = document.getElementById('create-images');
                if (imageInput && imageInput.files.length > 10) {
                    showError('create-images-error', 'Maksimal 10 gambar yang dapat diupload');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            $('[id^="editForm-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[1];
                let isValid = true;
                clearErrors('edit', itemId);

                const title = $(`#edit-title-${itemId}`).val().trim();
                if (!title) {
                    showError(`edit-title-error-${itemId}`, 'Title harus diisi');
                    isValid = false;
                }

                const tanggal = $(`#edit-tanggal-${itemId}`).val();
                if (!tanggal) {
                    showError(`edit-tanggal-error-${itemId}`, 'Tanggal publish harus diisi');
                    isValid = false;
                }

                const content = editEditors[itemId] ? editEditors[itemId].getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError(`edit-content-error-${itemId}`, 'Content harus diisi');
                    isValid = false;
                }

                const url = $(`#edit-url-${itemId}`).val().trim();
                if (url && !isValidUrl(url)) {
                    showError(`edit-url-error-${itemId}`, 'URL thumbnail harus berupa URL yang valid');
                    isValid = false;
                }

                const imageInput = document.getElementById(`edit-images-${itemId}`);
                if (imageInput && imageInput.files.length > 10) {
                    showError(`edit-images-error-${itemId}`, 'Maksimal 10 gambar yang dapat diupload');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                
                const inputElement = errorElement.previousElementSibling;
                if (inputElement && inputElement.tagName !== 'DIV') {
                    inputElement.classList.add('border-red-500');
                }
            }
        }

        function clearErrors(type, itemId = '') {
            const suffix = itemId ? `-${itemId}` : '';
            const fields = ['title', 'tanggal', 'content', 'url', 'images'];
            
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
        }

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            
            if (id === 'createModal') {
                clearErrors('create');
                document.getElementById('createForm').reset();
                document.getElementById('create-tanggal').value = new Date().toISOString().split('T')[0];
                
                if (createEditor) {
                    createEditor.setData('');
                }
                
                const fileInput = document.getElementById('create-images');
                const preview = document.getElementById('create-preview');
                if (fileInput) fileInput.value = '';
                if (preview) preview.innerHTML = '';
            }
        }

        function openEditModal(itemId) {
            const modalId = `editModal-${itemId}`;
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error(`Modal ${modalId} not found`);
                return;
            }
            
            modal.classList.remove('hidden');
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
            
            const fileInput = document.getElementById(`edit-images-${itemId}`);
            const preview = document.getElementById(`edit-preview-${itemId}`);
            if (fileInput) fileInput.value = '';
            if (preview) preview.innerHTML = '';
            
            updateImageCounter(itemId);
            
            setTimeout(() => {
                initializeEditEditor(itemId);
            }, 100);
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function autoHideFlashMessages() {
            setTimeout(() => {
                let flash = document.getElementById('flash-message');
                if (flash) {
                    flash.style.opacity = '0'; 
                    setTimeout(() => flash.remove(), 500); 
                }
            }, 3000);

            setTimeout(() => {
                let flashError = document.getElementById('flash-error');
                if (flashError) {
                    flashError.style.opacity = '0'; 
                    setTimeout(() => flashError.remove(), 500); 
                }
            }, 5000);
        }

        // ============================================
        // COUNTDOWN TIMER FOR SCHEDULED POSTS
        // ============================================
        function updateCountdownTimers() {
            const timers = document.querySelectorAll('.countdown-timer');
            const now = Math.floor(Date.now() / 1000);

            timers.forEach(timer => {
                const publishTimestamp = parseInt(timer.getAttribute('data-publish'));
                const diff = publishTimestamp - now;

                if (diff <= 0) {
                    timer.innerHTML = '🔄 Publishing now...';
                    timer.classList.remove('text-blue-600');
                    timer.classList.add('text-green-600', 'animate-pulse');
                    
                    setTimeout(() => { location.reload(); }, 3000);
                } else {
                    const days = Math.floor(diff / 86400);
                    const hours = Math.floor((diff % 86400) / 3600);
                    const minutes = Math.floor((diff % 3600) / 60);
                    const seconds = diff % 60;

                    let timeString = '⏱️ ';
                    
                    if (days > 0) {
                        timeString += `${days} hari ${hours} jam lagi`;
                    } else if (hours > 0) {
                        timeString += `${hours} jam ${minutes} menit lagi`;
                    } else if (minutes > 0) {
                        timeString += `${minutes} menit ${seconds} detik lagi`;
                    } else {
                        timeString += `${seconds} detik lagi`;
                        timer.classList.add('animate-pulse', 'font-bold');
                    }

                    timer.innerHTML = timeString;
                }
            });
        }

        if (document.querySelectorAll('.countdown-timer').length > 0) {
            updateCountdownTimers();
            setInterval(updateCountdownTimers, 1000);
        }

        // ============================================
        // IMAGE ZOOM MODAL FUNCTIONS
        // ============================================
        let currentImages = [];
        let currentImageIndex = 0;
        let currentZoom = 1;
        let isDragging = false;
        let dragStart = { x: 0, y: 0 };
        let imagePosition = { x: 0, y: 0 };

        window.openImageModal = function(imageSrc, imageTitle, imagesArray) {
            currentImages = Array.isArray(imagesArray) ? imagesArray : [imageSrc];
            currentImageIndex = currentImages.indexOf(imageSrc);
            if (currentImageIndex === -1) currentImageIndex = 0;
            
            const modal = document.getElementById('imageZoomModal');
            const zoomImage = document.getElementById('zoomImage');
            const titleElement = document.getElementById('imageTitle');
            const indexElement = document.getElementById('imageIndex');
            const totalElement = document.getElementById('totalImages');
            
            currentZoom = 1;
            imagePosition = { x: 0, y: 0 };
            
            zoomImage.src = imageSrc;
            zoomImage.alt = imageTitle;
            titleElement.textContent = imageTitle;
            indexElement.textContent = currentImageIndex + 1;
            totalElement.textContent = currentImages.length;
            
            updateNavigationButtons();
            updateZoomLevel();
            updateImageTransform();
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            addImageZoomEventListeners();
        };

        window.closeImageModal = function() {
            const modal = document.getElementById('imageZoomModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            removeImageZoomEventListeners();
        };

        window.navigateImage = function(direction) {
            if (currentImages.length <= 1) return;
            
            currentImageIndex += direction;
            if (currentImageIndex >= currentImages.length) currentImageIndex = 0;
            if (currentImageIndex < 0) currentImageIndex = currentImages.length - 1;
            
            const zoomImage = document.getElementById('zoomImage');
            const titleElement = document.getElementById('imageTitle');
            const indexElement = document.getElementById('imageIndex');
            
            currentZoom = 1;
            imagePosition = { x: 0, y: 0 };
            
            zoomImage.src = currentImages[currentImageIndex];
            titleElement.textContent = `Image ${currentImageIndex + 1}`;
            indexElement.textContent = currentImageIndex + 1;
            
            updateImageTransform();
            updateZoomLevel();
        };

        window.zoomImage = function(delta) {
            const newZoom = Math.max(0.1, Math.min(5, currentZoom + delta));
            if (newZoom !== currentZoom) {
                currentZoom = newZoom;
                updateImageTransform();
                updateZoomLevel();
            }
        };

        window.resetZoom = function() {
            currentZoom = 1;
            imagePosition = { x: 0, y: 0 };
            updateImageTransform();
            updateZoomLevel();
        };

        function updateImageTransform() {
            const zoomImage = document.getElementById('zoomImage');
            zoomImage.style.transform = `scale(${currentZoom}) translate(${imagePosition.x}px, ${imagePosition.y}px)`;
        }

        function updateZoomLevel() {
            const zoomLevelElement = document.getElementById('zoomLevel');
            zoomLevelElement.textContent = Math.round(currentZoom * 100) + '%';
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');
            
            if (currentImages.length <= 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'flex';
                nextBtn.style.display = 'flex';
            }
        }

        function addImageZoomEventListeners() {
            document.addEventListener('keydown', handleZoomKeydown);
            
            const imageContainer = document.getElementById('imageContainer');
            imageContainer.addEventListener('wheel', handleMouseWheel, { passive: false });
            
            const zoomImage = document.getElementById('zoomImage');
            zoomImage.addEventListener('mousedown', handleMouseDown);
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
            
            zoomImage.addEventListener('touchstart', handleTouchStart, { passive: false });
            document.addEventListener('touchmove', handleTouchMove, { passive: false });
            document.addEventListener('touchend', handleTouchEnd);
            
            zoomImage.addEventListener('dblclick', resetZoom);
        }

        function removeImageZoomEventListeners() {
            document.removeEventListener('keydown', handleZoomKeydown);
            
            const imageContainer = document.getElementById('imageContainer');
            if (imageContainer) {
                imageContainer.removeEventListener('wheel', handleMouseWheel);
            }
            
            const zoomImage = document.getElementById('zoomImage');
            if (zoomImage) {
                zoomImage.removeEventListener('mousedown', handleMouseDown);
                zoomImage.removeEventListener('touchstart', handleTouchStart);
                zoomImage.removeEventListener('dblclick', resetZoom);
            }
            
            document.removeEventListener('mousemove', handleMouseMove);
            document.removeEventListener('mouseup', handleMouseUp);
            document.removeEventListener('touchmove', handleTouchMove);
            document.removeEventListener('touchend', handleTouchEnd);
        }

        function handleZoomKeydown(e) {
            switch(e.key) {
                case 'Escape':
                    closeImageModal();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    navigateImage(-1);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    navigateImage(1);
                    break;
                case '+':
                case '=':
                    e.preventDefault();
                    zoomImage(0.1);
                    break;
                case '-':
                    e.preventDefault();
                    zoomImage(-0.1);
                    break;
                case '0':
                    e.preventDefault();
                    resetZoom();
                    break;
            }
        }

        function handleMouseWheel(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            zoomImage(delta);
        }

        function handleMouseDown(e) {
            if (currentZoom > 1) {
                isDragging = true;
                dragStart.x = e.clientX - imagePosition.x;
                dragStart.y = e.clientY - imagePosition.y;
                e.preventDefault();
            }
        }

        function handleMouseMove(e) {
            if (isDragging && currentZoom > 1) {
                imagePosition.x = e.clientX - dragStart.x;
                imagePosition.y = e.clientY - dragStart.y;
                updateImageTransform();
            }
        }

        function handleMouseUp() {
            isDragging = false;
        }

        function handleTouchStart(e) {
            if (currentZoom > 1 && e.touches.length === 1) {
                isDragging = true;
                const touch = e.touches[0];
                dragStart.x = touch.clientX - imagePosition.x;
                dragStart.y = touch.clientY - imagePosition.y;
                e.preventDefault();
            }
        }

        function handleTouchMove(e) {
            if (isDragging && currentZoom > 1 && e.touches.length === 1) {
                const touch = e.touches[0];
                imagePosition.x = touch.clientX - dragStart.x;
                imagePosition.y = touch.clientY - dragStart.y;
                updateImageTransform();
                e.preventDefault();
            }
        }

        function handleTouchEnd() {
            isDragging = false;
        }

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('imageZoomModal');
            const imageContainer = document.getElementById('imageContainer');
            const zoomImage = document.getElementById('zoomImage');
            
            if (modal && !modal.classList.contains('hidden') && 
                e.target === imageContainer && 
                e.target !== zoomImage) {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>