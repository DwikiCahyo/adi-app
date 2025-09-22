<x-app-layout>
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
            <h1 class="text-2xl font-bold">🙏 Daftar Ministry</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah Ministry
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="ministryTable" class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">No</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Images</th>
                        <th class="px-4 py-3 border border-gray-300">Category</th>
                        <th class="px-4 py-3 border border-gray-300">Created At</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($ministry as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 border border-gray-300 font-medium text-gray-900">{{ $item->title }}</td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-700">
                                <div class="content-wrapper">
                                    <div class="content-preview">
                                        {!! Str::limit(strip_tags($item->content), 100) !!}
                                        @if(strlen(strip_tags($item->content)) > 100)
                                            <span class="read-more cursor-pointer text-blue-600 hover:underline ml-1">[Selengkapnya]</span>
                                        @endif
                                    </div>
                                    <div class="content-full hidden">
                                        {!! $item->content !!}
                                        <span class="read-less cursor-pointer text-blue-600 hover:underline ml-1">[Sembunyikan]</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->images->count())
                                    @php $firstImage = $item->images->first(); @endphp
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/' . $firstImage->image) }}" 
                                             alt="Ministry Image" 
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
                            <td class="px-4 py-3 border border-gray-300">{{ $item->category ?? '-' }}</td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-700">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openEditModal('{{ $item->id }}')" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.ministry.destroy', $item->id) }}" method="POST" 
                                      class="inline-block" 
                                      onsubmit="return confirm('Yakin mau hapus ministry ini?')">
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
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
                                <h2 class="text-xl font-bold mb-4">Edit Ministry</h2>
                                <form id="edit-form-{{ $item->id }}" action="{{ route('admin.ministry.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    {{-- Title Field --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="edit-title-{{ $item->id }}" value="{{ $item->title }}" 
                                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <div id="edit-title-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Content Field with CKEditor --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                                        <textarea id="edit-content-{{ $item->id }}" name="content" class="ckeditor-edit" data-item-id="{{ $item->id }}">{{ $item->content }}</textarea>
                                        <div id="edit-content-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>

                                    {{-- Images Field --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Upload Images Baru (Opsional)</label>
                                        <input type="file" name="images[]" id="edit-images-{{ $item->id }}" 
                                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               multiple accept="image/*">
                                        <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</p>
                                        <div id="edit-images-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                        @if($item->images->count())
                                            <div class="mt-3">
                                                <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($item->images as $image)
                                                        <img src="{{ asset('storage/' . $image->image) }}" 
                                                             alt="Current Image" 
                                                             class="w-20 h-20 object-cover rounded border">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Category Field --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium mb-2">Category <span class="text-red-500">*</span></label>
                                        <select name="category" id="edit-category-{{ $item->id }}" 
                                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">-- Pilih Category --</option>
                                            <option value="Kids" {{ $item->category == 'Kids' ? 'selected' : '' }}>Kids</option>
                                            <option value="Youth Generation" {{ $item->category == 'Youth Generation' ? 'selected' : '' }}>Youth Generation</option>
                                            <option value="General" {{ $item->category == 'General' ? 'selected' : '' }}>General</option>
                                        </select>
                                        <div id="edit-category-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="closeModal('editModal-{{ $item->id }}')" 
                                                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" 
                                                class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                                            Update Ministry
                                        </button>
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
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-xl font-bold mb-4">Tambah Ministry</h2>
            <form id="create-form" action="{{ route('admin.ministry.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror" 
                           placeholder="Masukkan judul ministry">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('title')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                    <textarea id="create-content" name="content" class="ckeditor-create @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                    <div id="create-content-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Images --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Upload Images (Opsional)</label>
                    <input type="file" name="images[]" id="create-images" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('images') border-red-500 @enderror" 
                           multiple accept="image/*">
                    <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</p>
                    <div id="create-images-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('images.*')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Category --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category" id="create-category"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror">
                        <option value="">-- Pilih Category --</option>
                        <option value="Kids" {{ old('category') == 'Kids' ? 'selected' : '' }}>Kids</option>
                        <option value="Youth Generation" {{ old('category') == 'Youth Generation' ? 'selected' : '' }}>Youth Generation</option>
                        <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                    </select>
                    <div id="create-category-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('category')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" 
                            class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Simpan Ministry
                    </button>
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
        let createEditor, editEditors = {};

        $(document).ready(function () {
            // Initialize DataTable
            $('#ministryTable').DataTable({
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
                    emptyTable: "Belum ada data ministry."
                }
            });

            // Open modal jika ada error validasi
            @if($errors->any())
                openModal('createModal');
            @endif

            // Initialize create editor
            initializeCreateEditor();

            // Setup form validation
            setupFormValidation();

            // Expand/Collapse Content
            $(document).on('click', '.read-more', function() {
                var wrapper = $(this).closest('.content-wrapper');
                wrapper.find('.content-preview').addClass('hidden');
                wrapper.find('.content-full').removeClass('hidden');
            });

            $(document).on('click', '.read-less', function() {
                var wrapper = $(this).closest('.content-wrapper');
                wrapper.find('.content-full').addClass('hidden');
                wrapper.find('.content-preview').removeClass('hidden');
            });
        });

        // Initialize CKEditor for create form
        function initializeCreateEditor() {
            const element = document.getElementById('create-content');
            if (element && !createEditor) {
                ClassicEditor
                    .create(element, getEditorConfig())
                    .then(editor => {
                        createEditor = editor;
                    })
                    .catch(error => {
                        console.error('CKEditor initialization failed for create-content:', error);
                    });
            }
        }

        // Initialize CKEditor for edit form
        function initializeEditEditor(itemId) {
            const editorId = `edit-content-${itemId}`;
            const element = document.getElementById(editorId);
            if (element && !editEditors[editorId]) {
                ClassicEditor
                    .create(element, getEditorConfig())
                    .then(editor => {
                        editEditors[editorId] = editor;
                    })
                    .catch(error => {
                        console.error(`CKEditor initialization failed for ${editorId}:`, error);
                    });
            }
        }

        // CKEditor configuration
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

        // Form validation setup
        function setupFormValidation() {
            // Create form validation
            $('#create-form').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');

                // Title validation
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

                // Content validation
                const content = createEditor ? createEditor.getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError('create-content-error', 'Content harus diisi');
                    isValid = false;
                } else if (content.length < 10) {
                    showError('create-content-error', 'Content minimal 10 karakter');
                    isValid = false;
                }

                // Category validation
                const category = $('#create-category').val();
                if (!category) {
                    showError('create-category-error', 'Category harus dipilih');
                    isValid = false;
                }

                // Images validation
                const imagesInput = document.getElementById('create-images');
                if (imagesInput.files.length > 0) {
                    for (let file of imagesInput.files) {
                        if (!file.type.startsWith('image/')) {
                            showError('create-images-error', 'Semua file harus berupa gambar');
                            isValid = false;
                            break;
                        }
                        if (file.size > 5 * 1024 * 1024) { // 5MB
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

            // Edit form validation
            $('[id^="edit-form-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[2];
                let isValid = true;
                clearErrors('edit', itemId);

                // Title validation
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

                // Content validation
                const editorId = `edit-content-${itemId}`;
                const content = editEditors[editorId] ? editEditors[editorId].getData().trim() : '';
                if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>') {
                    showError(`edit-content-error-${itemId}`, 'Content harus diisi');
                    isValid = false;
                } else if (content.length < 10) {
                    showError(`edit-content-error-${itemId}`, 'Content minimal 10 karakter');
                    isValid = false;
                }

                // Category validation
                const category = $(`#edit-category-${itemId}`).val();
                if (!category) {
                    showError(`edit-category-error-${itemId}`, 'Category harus dipilih');
                    isValid = false;
                }

                // Images validation (for edit)
                const imagesInput = document.getElementById(`edit-images-${itemId}`);
                if (imagesInput && imagesInput.files.length > 0) {
                    for (let file of imagesInput.files) {
                        if (!file.type.startsWith('image/')) {
                            showError(`edit-images-error-${itemId}`, 'Semua file harus berupa gambar');
                            isValid = false;
                            break;
                        }
                        if (file.size > 5 * 1024 * 1024) { // 5MB
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

        // Utility functions
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                
                // Add red border to input
                const inputId = elementId.replace('-error', '');
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                }
            }
        }

        function clearErrors(type, itemId = '') {
            const suffix = itemId ? `-${itemId}` : '';
            const fields = ['title', 'content', 'images', 'category'];
            
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

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            clearErrors('create');
            
            if (id === 'createModal') {
                // Reset form
                document.getElementById('create-form').reset();
                // Clear CKEditor content
                if (createEditor) {
                    createEditor.setData('');
                }
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
            
            if (id === 'createModal') {
                clearErrors('create');
            } else if (id.startsWith('editModal-')) {
                const itemId = id.split('-')[1];
                clearErrors('edit', itemId);
            }
        }

        // Auto hide flash message
        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);

        // Gallery functions
        let galleries = @json($ministry->mapWithKeys(fn($m) => [$m->id => $m->images->pluck('image')]));
        let currentGallery = [];
        let currentIndex = 0;

        function openGallery(ministryId, index) {
            currentGallery = galleries[ministryId].map(img => '/storage/' + img);
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

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close any open modals
                const modals = ['createModal', 'galleryModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
                
                // Close edit modals
                const editModals = document.querySelectorAll('[id^="editModal-"]');
                editModals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                });
            } else if (!document.getElementById('galleryModal').classList.contains('hidden')) {
                if (e.key === 'ArrowLeft') {
                    prevImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                }
            }
        });

        // Show existing validation errors on page load
        @if($errors->any())
            @foreach($errors->all() as $error)
                console.log('Validation error: {{ $error }}');
            @endforeach
        @endif
    </script>
</x-app-layout>