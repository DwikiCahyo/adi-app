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

                        {{-- Modal Edit - UPDATED VERSION --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
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
                                    
                                    <div id="edit-topics-{{ $item->id }}" class="mb-4 space-y-4">
                                        <label class="block text-sm font-medium">Topics & Content<span class="text-red-500">*</span></label>
                                        @if($item->topics && $item->topics->count() > 0)
                                            @foreach($item->topics as $tIndex => $topic)
                                                <div class="border p-3 rounded topic-item space-y-2" data-topic-index="{{ $tIndex }}">
                                                    {{-- Hidden ID untuk update --}}
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
                                            {{-- Jika belum ada topics, buat satu topic kosong --}}
                                            <div class="border p-3 rounded topic-item space-y-2" data-topic-index="0">
                                                <input type="text" name="topics[0][topic]" class="w-full border rounded p-2" placeholder="Judul Topic (Opsional)">
                                                <textarea id="edit-content-{{ $item->id }}-0" name="topics[0][content]" class="ckeditor-edit" data-item-id="{{ $item->id }}" data-topic-index="0"></textarea>
                                                <div class="flex justify-end">
                                                    <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus Topic</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Error untuk topics edit --}}
                                    <div id="edit-topics-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden mb-4"></div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Upload Images Baru (Opsional)</label>
                                        <input type="file" name="images[]" id="edit-images-{{ $item->id }}" class="w-full border rounded p-2" multiple accept="image/*">
                                        <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</p>
                                        <div id="edit-images-error-{{ $item->id }}" class="text-red-600 text-sm mt-1 hidden"></div>
                                        @if($item->images->count())
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($item->images as $image)
                                                        <img src="{{ asset('storage/events/' . $image->image) }}" alt="Current Image" class="w-16 h-16 object-cover rounded border">
                                                    @endforeach
                                                </div>
                                                <p class="text-xs text-red-500 mt-1">⚠️ Upload gambar baru akan mengganti semua gambar yang ada</p>
                                            </div>
                                        @endif
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

    {{-- Modal Create - UPDATED VERSION --}}
    <div id="createModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-xl font-bold mb-4">Tambah Event</h2>
            <form id="create-form" action="{{ route('admin.event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Agenda --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Agenda <span class="text-red-500">*</span></label>
                    <input type="text" name="agenda" id="create-agenda" value="{{ old('agenda') }}" class="w-full border rounded p-2" placeholder="Masukkan agenda event">
                    <div id="create-agenda-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('agenda')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="create-title" value="{{ old('title') }}" class="w-full border rounded p-2" placeholder="Masukkan judul event">
                    <div id="create-title-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('title')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- Topics --}}
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
                
                {{-- Error untuk topics create --}}
                <div id="create-topics-error" class="text-red-600 text-sm mt-1 hidden mb-4"></div>

                <div class="flex justify-end mb-4">
                    <button type="button" onclick="addCreateTopic()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">+ Tambah Topic</button>
                </div>

                {{-- Images --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Upload Images (Opsional)</label>
                    <input type="file" name="images[]" id="create-images" class="w-full border rounded p-2" multiple accept="image/*">
                    <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</p>
                    <div id="create-images-error" class="text-red-600 text-sm mt-1 hidden">
                        @error('images.*')
                            {{ $message }}
                        @enderror
                    </div>
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

        $(document).ready(function () {
            // Initialize DataTable
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

            // Open modal jika ada error validasi
            @if($errors->any())
                openModal('createModal');
            @endif

            // Initialize create editors
            initializeCreateEditors();

            // Setup form validation
            setupFormValidation();
        });

        // Initialize CKEditor for create form
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

        // Initialize CKEditor for edit form
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

        // Add new topic in create form
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

            // Initialize CKEditor for new topic
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

        // Remove topic handler
        document.addEventListener('click', function(e) {
            if(e.target.classList.contains('remove-topic')) {
                const topicItem = e.target.closest('.topic-item');
                const textarea = topicItem.querySelector('textarea[id^="create-content-"], textarea[id^="edit-content-"]');
                if (textarea && textarea.id) {
                    // Destroy CKEditor instance
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

        // Form validation setup - UPDATED WITH CONTENT VALIDATION
        function setupFormValidation() {
            // Create form validation
            $('#create-form').on('submit', function(e) {
                let isValid = true;
                clearErrors('create');

                // Update CKEditor data before validation
                Object.keys(createEditors).forEach(editorId => {
                    if (createEditors[editorId]) {
                        const textarea = document.getElementById(editorId);
                        if (textarea) {
                            textarea.value = createEditors[editorId].getData();
                        }
                    }
                });

                // Agenda validation
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

                // CONTENT VALIDATION - IMPROVED VERSION
                let hasValidContent = false;
                let emptyContentCount = 0;
                const topicItems = document.querySelectorAll('#topics-container .topic-item');
                
                topicItems.forEach((item, index) => {
                    const textarea = item.querySelector('textarea[name*="content"]');
                    if (textarea) {
                        const content = textarea.value.trim();
                        // Remove HTML tags and check if there's actual content
                        const textContent = content.replace(/<[^>]*>/g, '').trim();
                        
                        if (textContent && textContent.length >= 10) {
                            hasValidContent = true;
                        } else if (textContent && textContent.length > 0 && textContent.length < 10) {
                            // Content ada tapi terlalu pendek
                            showContentError(textarea.id, 'Content minimal 10 karakter');
                            isValid = false;
                        } else if (!textContent || textContent.length === 0) {
                            // Content kosong
                            emptyContentCount++;
                            showContentError(textarea.id, 'Content wajib diisi');
                            isValid = false;
                        }
                    }
                });

                // Jika semua content kosong atau tidak valid
                if (!hasValidContent && topicItems.length > 0) {
                    showError('create-topics-error', 'Setidaknya satu topic harus memiliki content yang valid (minimal 10 karakter)');
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

            // Edit form validation - UPDATED VERSION
            $('[id^="edit-form-"]').on('submit', function(e) {
                const itemId = this.id.split('-')[2];
                let isValid = true;
                clearErrors('edit', itemId);

                // Update CKEditor data before validation
                Object.keys(editEditors).forEach(editorId => {
                    if (editEditors[editorId] && editorId.includes(`-${itemId}-`)) {
                        const textarea = document.getElementById(editorId);
                        if (textarea) {
                            textarea.value = editEditors[editorId].getData();
                        }
                    }
                });

                // Agenda validation
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

                // CONTENT VALIDATION FOR EDIT - NEW ADDITION
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

                // Check if at least one topic has valid content
                if (!hasValidContent) {
                    showError(`edit-topics-error-${itemId}`, 'Setidaknya satu topic harus memiliki content');
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

        // New function to show content-specific errors
        function showContentError(textareaId, message) {
            // Create error element if it doesn't exist
            let errorId = textareaId + '-error';
            let errorElement = document.getElementById(errorId);
            
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.id = errorId;
                errorElement.className = 'text-red-600 text-sm mt-1';
                
                // Insert after the textarea's parent (CKEditor container)
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
            
            // Add red border to CKEditor container
            const textarea = document.getElementById(textareaId);
            if (textarea) {
                const ckContainer = textarea.nextElementSibling;
                if (ckContainer && ckContainer.classList.contains('ck-editor')) {
                    ckContainer.style.border = '2px solid #ef4444';
                }
            }
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
            const fields = ['agenda', 'title', 'images', 'topics'];
            
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
            
            // Clear individual content errors
            const contentErrors = document.querySelectorAll(`[id$="-content-error"]`);
            contentErrors.forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });
            
            // Remove red borders from CKEditor containers
            const ckEditors = document.querySelectorAll('.ck-editor');
            ckEditors.forEach(editor => {
                editor.style.border = '';
            });
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            clearErrors('create');
            
            if (id === 'createModal') {
                // Reset form
                document.getElementById('create-form').reset();
                // Clear all CKEditor instances
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
            
            // Initialize CKEditor for this edit modal if not already initialized
            setTimeout(() => {
                initializeEditEditors(itemId);
            }, 100);
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

        // Show existing validation errors on page load
        @if($errors->any())
            @foreach($errors->all() as $error)
                console.log('Validation error: {{ $error }}');
            @endforeach
        @endif
    </script>
</x-app-layout>