{{-- FILE 6: resources/views/admin/resource/file.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center sm:-my-px sm:ms-10">
            <nav class="flex gap-4">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('News Feed') }}
                </x-nav-link>

                {{-- Dropdown Resource --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900">
                        <span>Resource</span>
                        <svg class="w-4 h-4 ml-1 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition @click.away="open = false" class="absolute left-0 mt-2 w-auto min-w-max bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <div class="flex flex-col">
                            <x-nav-link :href="route('admin.resource.index')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">+ Add Latest Sermon</x-nav-link>
                            <x-nav-link :href="route('admin.resourcefile.file')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">+ Add Good News</x-nav-link>
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
            <h1 class="text-2xl font-bold">Good News</h1>
            <button onclick="openModal('createModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + Tambah Resource
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="resourceTable" class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Status</th>
                        <th class="px-4 py-3 border border-gray-300">Tanggal Publish</th>
                        <th class="px-4 py-3 border border-gray-300">Refleksi Diri</th>
                        <th class="px-4 py-3 border border-gray-300">Pengakuan Iman</th>
                        <th class="px-4 py-3 border border-gray-300">Bacaan Alkitab</th>
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($resourcefile as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300 text-center"></td>
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-xs break-words">{{ $item->title }}</td>
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
                            <td class="px-4 py-3 text-gray-700 max-w-md break-words">{!! Str::limit($item->refleksi_diri, 100) !!}</td>
                            <td class="px-4 py-3 text-gray-700 max-w-md break-words">{!! Str::limit($item->pengakuan_iman, 100) !!}</td>
                            <td class="px-4 py-3 text-gray-700 max-w-md break-words">{!! Str::limit($item->bacaan_alkitab, 100) !!}</td>
                            <td class="px-4 py-3 text-gray-700 max-w-md break-words">{!! Str::limit($item->content, 100) !!}</td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">Edit</button>
                                <form action="{{ route('admin.resourcefile.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
                                <h2 class="text-xl font-bold mb-4">Edit Resource</h2>
                                <form id="edit-form-{{ $item->id }}" class="edit-form" action="{{ route('admin.resourcefile.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    {{-- Title --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" class="w-full border rounded p-2" value="{{ old('title', $item->title) }}">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Tanggal Publish --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal" id="edit-tanggal-{{ $item->id }}" class="w-full border rounded p-2" value="{{ old('tanggal', $item->publish_at ? $item->publish_at->format('Y-m-d') : '') }}">
                                        <p class="text-xs text-gray-500 mt-1">📅 Post akan dipublish otomatis pada jam 00:00 WIB di tanggal yang dipilih</p>
                                        <div id="edit-tanggal-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                                        <textarea id="edit-content-{{ $item->id }}" name="content" class="ckeditor-edit" data-field="content">{!! old('content', $item->content) !!}</textarea>
                                        <div id="edit-content-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Refleksi Diri --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Refleksi Diri <span class="text-red-500">*</span></label>
                                        <textarea id="edit-refleksi_diri-{{ $item->id }}" name="refleksi_diri" class="ckeditor-edit" data-field="refleksi_diri">{!! old('refleksi_diri', $item->refleksi_diri) !!}</textarea>
                                        <div id="edit-refleksi_diri-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Pengakuan Iman --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Pengakuan Iman <span class="text-red-500">*</span></label>
                                        <textarea id="edit-pengakuan_iman-{{ $item->id }}" name="pengakuan_iman" class="ckeditor-edit" data-field="pengakuan_iman">{!! old('pengakuan_iman', $item->pengakuan_iman) !!}</textarea>
                                        <div id="edit-pengakuan_iman-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Bacaan Alkitab --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Bacaan Alkitab <span class="text-red-500">*</span></label>
                                        <textarea id="edit-bacaan_alkitab-{{ $item->id }}" name="bacaan_alkitab" class="ckeditor-edit" data-field="bacaan_alkitab">{!! old('bacaan_alkitab', $item->bacaan_alkitab) !!}</textarea>
                                        <div id="edit-bacaan_alkitab-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
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

    {{-- Create Modal --}}
    <div id="createModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Tambah Resource</h2>
            <form id="create-form" action="{{ route('admin.resourcefile.store') }}" method="POST">
                @csrf

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('title')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Publish --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tanggal Publish <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="create-tanggal" value="{{ old('tanggal', now('Asia/Jakarta')->format('Y-m-d')) }}" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">📅 Post akan dipublish otomatis pada jam 00:00 WIB di tanggal yang dipilih</p>
                    <div id="create-tanggal-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('tanggal')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                    <textarea id="create-content" name="content" class="ckeditor-create">{{ old('content') }}</textarea>
                    <div id="create-content-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Refleksi Diri --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Refleksi Diri <span class="text-red-500">*</span></label>
                    <textarea id="create-refleksi_diri" name="refleksi_diri" class="ckeditor-create">{{ old('refleksi_diri') }}</textarea>
                    <div id="create-refleksi_diri-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('refleksi_diri')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Pengakuan Iman --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Pengakuan Iman <span class="text-red-500">*</span></label>
                    <textarea id="create-pengakuan_iman" name="pengakuan_iman" class="ckeditor-create">{{ old('pengakuan_iman') }}</textarea>
                    <div id="create-pengakuan_iman-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('pengakuan_iman')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Bacaan Alkitab --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Bacaan Alkitab <span class="text-red-500">*</span></label>
                    <textarea id="create-bacaan_alkitab" name="bacaan_alkitab" class="ckeditor-create">{{ old('bacaan_alkitab') }}</textarea>
                    <div id="create-bacaan_alkitab-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('bacaan_alkitab')
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
        let createEditors = {}, editEditors = {};

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
                    emptyTable: "Belum ada data Good News."
                },
                columnDefs: [{ targets: 0, orderable: false, searchable: false }],
                order: [[3, 'desc']] // Sort by publish date
            });

            // Reindex nomor urut
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each((cell, i) => { 
                    cell.innerHTML = i + 1; 
                });
            }).draw();

            @if($errors->any())
                openModal('createModal');
            @endif

            initializeCreateEditors();
            setupFormValidation();
        });

        function initializeCreateEditors() {
            const createFields = ['refleksi_diri', 'pengakuan_iman', 'bacaan_alkitab', 'content'];
            
            createFields.forEach(field => {
                const elementId = `create-${field}`;
                const element = document.querySelector(`#${elementId}`);
                
                if (element) {
                    ClassicEditor.create(element, getEditorConfig())
                        .then(editor => { createEditors[field] = editor; })
                        .catch(error => { console.error(`CKEditor init failed for ${elementId}:`, error); });
                }
            });
        }

        function initializeEditEditors(itemId) {
            if (editEditors[itemId]) return;

            editEditors[itemId] = {};
            const editFields = ['refleksi_diri', 'pengakuan_iman', 'bacaan_alkitab', 'content'];
            
            editFields.forEach(field => {
                const elementId = `edit-${field}-${itemId}`;
                const element = document.querySelector(`#${elementId}`);
                
                if (element) {
                    ClassicEditor.create(element, getEditorConfig())
                        .then(editor => { editEditors[itemId][field] = editor; })
                        .catch(error => { console.error(`CKEditor init failed for ${elementId}:`, error); });
                }
            });
        }

        function getEditorConfig() {
            return {
                toolbar: {
                    items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
                },
                language: 'id',
                table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
            };
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

                const requiredFields = ['refleksi_diri', 'pengakuan_iman', 'bacaan_alkitab', 'content'];
                requiredFields.forEach(field => {
                    const content = createEditors[field] ? createEditors[field].getData().trim() : '';
                    if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                        const fieldName = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        showError(`create-${field}-error`, `${fieldName} harus diisi`);
                        isValid = false;
                    } else if (content.length < 10) {
                        const fieldName = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        showError(`create-${field}-error`, `${fieldName} minimal 10 karakter`);
                        isValid = false;
                    }
                });

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

                const requiredFields = ['refleksi_diri', 'pengakuan_iman', 'bacaan_alkitab', 'content'];
                requiredFields.forEach(field => {
                    const content = editEditors[itemId] && editEditors[itemId][field] 
                        ? editEditors[itemId][field].getData().trim() 
                        : '';
                    if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                        const fieldName = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        showError(`edit-${field}-error-${itemId}`, `${fieldName} harus diisi`);
                        isValid = false;
                    } else if (content.length < 10) {
                        const fieldName = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        showError(`edit-${field}-error-${itemId}`, `${fieldName} minimal 10 karakter`);
                        isValid = false;
                    }
                });

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
            const fields = ['title', 'tanggal', 'refleksi_diri', 'pengakuan_iman', 'bacaan_alkitab', 'content'];
            
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

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            clearErrors('create');
            
            if (id === 'createModal') {
                document.getElementById('create-form').reset();
                document.getElementById('create-tanggal').value = new Date().toISOString().split('T')[0];
                Object.keys(createEditors).forEach(field => {
                    if (createEditors[field]) createEditors[field].setData('');
                });
            }
        }

        function openEditModal(itemId) {
            const modalId = `editModal-${itemId}`;
            document.getElementById(modalId).classList.remove('hidden');
            clearErrors('edit', itemId);
            initializeEditEditors(itemId);
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
            const now = Math.floor(Date.now() / 1000); // Current timestamp in seconds

            timers.forEach(timer => {
                const publishTimestamp = parseInt(timer.getAttribute('data-publish'));
                const diff = publishTimestamp - now;

                if (diff <= 0) {
                    // Time's up! Refresh page to update status
                    timer.innerHTML = '🔄 Publishing now...';
                    timer.classList.remove('text-blue-600');
                    timer.classList.add('text-green-600', 'animate-pulse');
                    
                    // Refresh page after 3 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    // Calculate time remaining
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

        // Update countdown every second
        if (document.querySelectorAll('.countdown-timer').length > 0) {
            updateCountdownTimers(); // Initial update
            setInterval(updateCountdownTimers, 1000); // Update every second
        }
    </script>
</x-app-layout>