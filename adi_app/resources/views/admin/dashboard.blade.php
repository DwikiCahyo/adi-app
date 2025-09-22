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

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">📰 Daftar News</h1>
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
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Thumbnail</th>
                        <th class="px-4 py-3 border border-gray-300">Created At</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($news as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            {{-- kosong, nanti diisi nomor urut oleh DataTables --}}
                            <td class="px-4 py-3 border border-gray-300 text-center"></td>
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-normal break-words max-w-xs">
                                {{ $item->title }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-normal break-words max-w-md">
                                {!! Str::limit($item->content, 200) !!}
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->thumbnail_url && filter_var($item->thumbnail_url, FILTER_VALIDATE_URL))
                                    <img 
                                        src="{{ $item->thumbnail_url }}" 
                                        alt="{{ $item->title }}" 
                                        class="w-20 h-14 object-cover rounded border"
                                        onerror="this.onerror=null;this.src='no-image.png';"
                                    >
                                @else
                                    <span class="text-gray-400 italic">Gambar tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-600">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.dashboard.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
                                <h2 class="text-xl font-bold mb-4">Edit News</h2>
                                <form id="editForm-{{ $item->id }}" action="{{ route('admin.dashboard.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" class="w-full border rounded p-2 @error('title') border-red-500 @enderror" value="{{ $item->title }}">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                                        <textarea name="content" id="edit-content-{{ $item->id }}" class="ckeditor-edit">{{ $item->content }}</textarea>
                                        <div id="edit-content-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">URL Thumbnail <span class="text-red-500">*</span></label>
                                        <input type="text" name="url" id="edit-url-{{ $item->id }}" class="w-full border rounded p-2 @error('url') border-red-500 @enderror" value="{{ $item->url }}">
                                        <div id="edit-url-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
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
            <h2 class="text-xl font-bold mb-4">Tambah News</h2>
            <form id="createForm" action="{{ route('admin.dashboard.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2 @error('title') border-red-500 @enderror">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('title')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" id="create-content" class="ckeditor-create">{{ old('content') }}</textarea>
                    <div id="create-content-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">URL Thumbnail <span class="text-red-500">*</span></label>
                    <input type="text" name="url" id="create-url" value="{{ old('url') }}" class="w-full border rounded p-2 @error('url') border-red-500 @enderror">
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

    {{-- DataTable CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    
    {{-- CKEditor CSS & JS --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <script>
        let createEditor, editEditors = {};

        $(document).ready(function () {
            // Initialize DataTable
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
                order: [[1, 'asc']]
            });

            // Reindex nomor urut
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each((cell, i) => {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            // Auto open modal jika ada validation error
            @if($errors->any())
                openModal('createModal');
            @endif

            // Initialize CKEditor for create modal
            initializeCreateEditor();

            // Form validation handlers
            setupFormValidation();
        });

        // Initialize CKEditor for create modal
        function initializeCreateEditor() {
            ClassicEditor
                .create(document.querySelector('#create-content'), {
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
                            'mediaEmbed',
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
                })
                .then(editor => {
                    createEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Initialize CKEditor for edit modal
        function initializeEditEditor(itemId) {
            if (editEditors[itemId]) {
                return; // Editor already initialized
            }

            ClassicEditor
                .create(document.querySelector(`#edit-content-${itemId}`), {
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
                            'mediaEmbed',
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
                })
                .then(editor => {
                    editEditors[itemId] = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Form validation setup
        function setupFormValidation() {
            // Create form validation
            $('#createForm').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');

                // Title validation
                const title = $('#create-title').val().trim();
                if (!title) {
                    showError('create-title-error', 'Title harus diisi');
                    isValid = false;
                }

                // Content validation
                const content = createEditor ? createEditor.getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError('create-content-error', 'Content harus diisi');
                    isValid = false;
                }

                // URL validation (optional but if filled, must be valid URL)
                const url = $('#create-url').val().trim();
                if (url && !isValidUrl(url)) {
                    showError('create-url-error', 'URL thumbnail harus berupa URL yang valid');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Edit form validation
            $('[id^="editForm-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[1];
                let isValid = true;
                clearErrors('edit', itemId);

                // Title validation
                const title = $(`#edit-title-${itemId}`).val().trim();
                if (!title) {
                    showError(`edit-title-error-${itemId}`, 'Title harus diisi');
                    isValid = false;
                }

                // Content validation
                const content = editEditors[itemId] ? editEditors[itemId].getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError(`edit-content-error-${itemId}`, 'Content harus diisi');
                    isValid = false;
                }

                // URL validation (optional but if filled, must be valid URL)
                const url = $(`#edit-url-${itemId}`).val().trim();
                if (url && !isValidUrl(url)) {
                    showError(`edit-url-error-${itemId}`, 'URL thumbnail harus berupa URL yang valid');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }

        // Utility functions
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                
                // Add red border to input
                const inputElement = errorElement.previousElementSibling;
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                }
            }
        }

        function clearErrors(type, itemId = '') {
            const suffix = itemId ? `-${itemId}` : '';
            const fields = ['title', 'content', 'url'];
            
            fields.forEach(field => {
                const errorId = `${type}-${field}-error${suffix}`;
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
                
                // Remove red border
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
            clearErrors('create');
            
            if (id === 'createModal' && createEditor) {
                createEditor.setData('');
            }
        }

        function openEditModal(itemId) {
            const modalId = `editModal-${itemId}`;
            document.getElementById(modalId).classList.remove('hidden');
            clearErrors('edit', itemId);
            
            // Initialize CKEditor for this edit modal if not already initialized
            initializeEditEditor(itemId);
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Auto hide flash message
        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);

        // Show existing validation errors
        @if($errors->any())
            @foreach($errors->all() as $error)
                console.log('Validation error: {{ $error }}');
            @endforeach
        @endif
    </script>
</x-app-layout>