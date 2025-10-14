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
                            <x-nav-link :href="route('admin.resource.index')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                + Add Latest Sermon
                            </x-nav-link>
                            <x-nav-link :href="route('admin.resourcefile.file')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">
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
            <h1 class="text-2xl font-bold">📖 Latest Sermon</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah Resource
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="resourceTable" class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Status</th>
                        <th class="px-4 py-3 border border-gray-300">Tanggal Publish</th>
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Thumbnail</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($resource as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300 text-center"></td>
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-xs break-words">
                                {{ $item->title }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->status === 'published')
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Published</span>
                                @elseif($item->status === 'scheduled')
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Scheduled</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Draft</span>
                                @endif
                            </td>
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
                                    <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="w-20 h-14 object-cover rounded border" onerror="this.onerror=null;this.src='no-image.png';">
                                @else
                                    <span class="text-gray-400 italic">Gambar tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">Edit</button>
                                <form action="{{ route('admin.resource.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
                                <h2 class="text-xl font-bold mb-4">Edit Resource</h2>
                                <form id="edit-form-{{ $item->id }}" action="{{ route('admin.resource.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" value="{{ old('title', $item->title) }}" class="w-full border rounded p-2">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal" id="edit-tanggal-{{ $item->id }}" value="{{ old('tanggal', $item->publish_at ? $item->publish_at->format('Y-m-d') : '') }}" class="w-full border rounded p-2">
                                        <p class="text-xs text-gray-500 mt-1">📅 Pilih hari ini untuk publish sekarang, atau pilih tanggal lain untuk publish jam 00:00 WIB</p>
                                        <div id="edit-tanggal-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                                        <textarea id="edit-content-{{ $item->id }}" name="content" class="ckeditor-edit">{{ old('content', $item->content) }}</textarea>
                                        <div id="edit-content-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">URL Video/Thumbnail <span class="text-red-500">*</span></label>
                                        <input type="text" name="url" id="edit-url-{{ $item->id }}" value="{{ old('url', $item->url) }}" class="w-full border rounded p-2" placeholder="https://youtube.com/watch?v=...">
                                        <div id="edit-url-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                        @if($item->thumbnail_url)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Preview gambar saat ini:</p>
                                                <img src="{{ $item->thumbnail_url }}" alt="Preview" class="w-32 h-20 object-cover rounded border mt-1">
                                            </div>
                                        @endif
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

    {{-- Modal Create --}}
    <div id="createModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Tambah Resource</h2>
            <form id="create-form" action="{{ route('admin.resource.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2" placeholder="Masukkan judul sermon">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('title')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="create-tanggal" value="{{ old('tanggal', now('Asia/Jakarta')->format('Y-m-d')) }}" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">📅 Pilih hari ini untuk publish sekarang, atau pilih tanggal lain untuk publish jam 00:00 WIB</p>
                    <div id="create-tanggal-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('tanggal')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                    <textarea id="create-content" name="content" class="ckeditor-create">{{ old('content') }}</textarea>
                    <div id="create-content-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">URL Video/Thumbnail <span class="text-red-500">*</span></label>
                    <input type="text" name="url" id="create-url" value="{{ old('url') }}" class="w-full border rounded p-2" placeholder="https://youtube.com/watch?v=... atau https://example.com/image.jpg">
                    <p class="text-xs text-gray-500 mt-1">Masukkan URL YouTube atau URL gambar yang valid untuk thumbnail</p>
                    <div id="create-url-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('url')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTables CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
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

        $(document).ready(function () {
            // Initialize DataTable
            let table = $('#resourceTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "Semua"] ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari total _MAX_ data)",
                    emptyTable: "Belum ada data Latest Sermon."
                },
                columnDefs: [{ targets: 0, orderable: false, searchable: false }],
                order: [[3, 'desc']] // Sort by publish date
            });

            // Reindex nomor urut
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each((cell, i) => cell.innerHTML = i + 1);
            }).draw();

            @if($errors->any())
                openModal('createModal');
            @endif

            initializeCreateEditor();
            setupFormValidation();
        });

        function initializeCreateEditor() {
            ClassicEditor
                .create(document.querySelector('#create-content'), {
                    toolbar: {
                        items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo']
                    },
                    language: 'id',
                    table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
                })
                .then(editor => { createEditor = editor; })
                .catch(error => { console.error('CKEditor init failed:', error); });
        }

        function initializeEditEditor(itemId) {
            if (editEditors[itemId]) return;

            ClassicEditor
                .create(document.querySelector(`#edit-content-${itemId}`), {
                    toolbar: {
                        items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo']
                    },
                    language: 'id',
                    table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
                })
                .then(editor => { editEditors[itemId] = editor; })
                .catch(error => { console.error(`CKEditor init failed for edit-${itemId}:`, error); });
        }

        function setupFormValidation() {
            $('#create-form').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');

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

                const tanggal = $('#create-tanggal').val();
                if (!tanggal) {
                    showError('create-tanggal-error', 'Tanggal publish harus diisi');
                    isValid = false;
                }

                const content = createEditor ? createEditor.getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError('create-content-error', 'Content harus diisi');
                    isValid = false;
                } else if (content.length < 10) {
                    showError('create-content-error', 'Content minimal 10 karakter');
                    isValid = false;
                }

                const url = $('#create-url').val().trim();
                if (!url) {
                    showError('create-url-error', 'URL harus diisi');
                    isValid = false;
                } else if (!isValidUrl(url)) {
                    showError('create-url-error', 'URL harus berupa alamat yang valid');
                    isValid = false;
                }

                if (!isValid) e.preventDefault();
            });

            $('[id^="edit-form-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[2];
                let isValid = true;
                clearErrors('edit', itemId);

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

                const tanggal = $(`#edit-tanggal-${itemId}`).val();
                if (!tanggal) {
                    showError(`edit-tanggal-error-${itemId}`, 'Tanggal publish harus diisi');
                    isValid = false;
                }

                const content = editEditors[itemId] ? editEditors[itemId].getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError(`edit-content-error-${itemId}`, 'Content harus diisi');
                    isValid = false;
                } else if (content.length < 10) {
                    showError(`edit-content-error-${itemId}`, 'Content minimal 10 karakter');
                    isValid = false;
                }

                const url = $(`#edit-url-${itemId}`).val().trim();
                if (!url) {
                    showError(`edit-url-error-${itemId}`, 'URL harus diisi');
                    isValid = false;
                } else if (!isValidUrl(url)) {
                    showError(`edit-url-error-${itemId}`, 'URL harus berupa alamat yang valid');
                    isValid = false;
                }

                if (!isValid) e.preventDefault();
            });
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                const inputId = elementId.replace('-error', '');
                const inputElement = document.getElementById(inputId);
                if (inputElement) inputElement.classList.add('border-red-500');
            }
        }

        function clearErrors(type, itemId = '') {
            const suffix = itemId ? `-${itemId}` : '';
            const fields = ['title', 'tanggal', 'content', 'url'];
            
            fields.forEach(field => {
                const errorId = `${type}-${field}-error${suffix}`;
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
                const inputId = `${type}-${field}${suffix}`;
                const inputElement = document.getElementById(inputId);
                if (inputElement) inputElement.classList.remove('border-red-500');
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

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            clearErrors('create');
            
            if (id === 'createModal') {
                document.getElementById('create-form').reset();
                document.getElementById('create-tanggal').value = new Date().toISOString().split('T')[0];
                if (createEditor) createEditor.setData('');
            }
        }

        function openEditModal(itemId) {
            const modalId = `editModal-${itemId}`;
            document.getElementById(modalId).classList.remove('hidden');
            clearErrors('edit', itemId);
            initializeEditEditor(itemId);
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            if (id === 'createModal') {
                clearErrors('create');
            } else if (id.startsWith('editModal-')) {
                const itemId = id.split('-')[1];
                clearErrors('edit', itemId);
            }
        }

        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['createModal'].forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) closeModal(modalId);
                });
                document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
                    if (!modal.classList.contains('hidden')) closeModal(modal.id);
                });
            }
        });

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
    </script>
</x-app-layout>