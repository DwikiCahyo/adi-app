<x-app-layout>
    <x-slot name="header"> 
        <div class="flex items-center sm:-my-px sm:ms-10">
            <nav class="flex gap-4">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('News Feed') }}
                </x-nav-link>
               
                <x-nav-link :href="route('resource')" :active="request()->routeIs('resource')">
                    {{ __('Resource') }}
                </x-nav-link>

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

        {{-- Error Global --}}
        {{-- @if ($errors->any())
            <div class="mb-4 p-4 text-red-800 bg-red-200 rounded-lg">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

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
            <table id="eventTable" 
                   class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">#</th>
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
                            <td class="px-4 py-3 border border-gray-300">{{ $item->id }}</td>
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
                                                    {{ Str::limit($topic->content, 120) }}
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
                                <button onclick="openModal('editModal-{{ $item->id }}')" 
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
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto p-6">
                                 @if ($errors->has('general'))
                                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                                            {{ $errors->first('general') }}
                                    </div>
                                @endif
                                <h2 class="text-xl font-bold mb-4">Edit Event</h2>
                                <form action="{{ route('admin.event.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Agenda</label>
                                        <input type="text" name="agenda" value="{{ $item->agenda }}" 
                                            class="w-full border rounded p-2" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Title</label>
                                        <input type="text" name="title" value="{{ $item->title }}" 
                                            class="w-full border rounded p-2" required>
                                    </div>
                                    <div id="editTopics-{{ $item->id }}" class="mb-4 space-y-4">
                                        <label class="block text-sm font-medium">Topics</label>
                                        @foreach($item->topics as $tIndex => $topic)
                                            <div class="border p-3 rounded topic-item space-y-2">
                                                <input type="hidden" name="topics[{{ $tIndex }}][id]" value="{{ $topic->id }}">
                                                <input type="text" name="topics[{{ $tIndex }}][topic]" value="{{ $topic->topic }}" class="w-full border rounded p-2" placeholder="Judul Topic" required>
                                                <textarea name="topics[{{ $tIndex }}][content]" rows="3" class="w-full border rounded p-2" placeholder="Isi Konten" required>{{ $topic->content }}</textarea>
                                                <div class="flex justify-end">
                                                    <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-end mb-4">
                                        <button type="button" onclick="addTopic('editTopics-{{ $item->id }}')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">+ Topic</button>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Upload Images</label>
                                        <input type="file" name="images[]" class="w-full border rounded p-2" multiple>
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
    <div id="createModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto p-6">
            @if ($errors->has('general'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        {{ $errors->first('general') }}
                </div>
            @endif
            <h2 class="text-xl font-bold mb-4">Tambah Event</h2>
            <form action="{{ route('admin.event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Agenda</label>
                    <input type="text" name="agenda" value="{{ old('agenda') }}" class="w-full border rounded p-2">
                    @error('agenda')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div id="topics-container" class="mb-4 space-y-4">
                    <label class="block text-sm font-medium">Topics</label>
                    <div class="border p-3 rounded topic-item space-y-2">
                        <input type="text" name="topics[0][topic]" class="w-full border rounded p-2" placeholder="Judul Topic" >
                        @error('topics.*.topic')
                             <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                         @enderror
                        <textarea name="topics[0][content]" rows="3" class="w-full border rounded p-2" placeholder="Isi Konten" ></textarea>
                        @error('topics.*.content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end">
                            <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus</button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mb-4">
                    <button type="button" onclick="addTopic('topics-container')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">+ Topic</button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Upload Images</label>
                    <input type="file" name="images[]" class="w-full border rounded p-2" multiple>
                     @error('images.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
        <button onclick="closeModal('galleryModal')" class="absolute top-4 right-6 text-white text-2xl">✕</button>
        <button onclick="prevImage()" class="absolute left-4 text-white text-3xl">❮</button>
        <img id="galleryImage" src="" class="max-h-screen max-w-4xl object-contain rounded shadow-lg">
        <button onclick="nextImage()" class="absolute right-4 text-white text-3xl">❯</button>
    </div>

    {{-- DataTable CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#eventTable').DataTable({
                responsive: true,
                pageLength: 10,
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

            // open modal kalau ada error validasi
            @if($errors->any())
                openModal('createModal');
            @endif
        });

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function addTopic(containerId) {
            let container = document.getElementById(containerId);
            let index = container.querySelectorAll('.topic-item').length;
            let div = document.createElement('div');
            div.classList.add('border', 'p-3', 'rounded', 'topic-item', 'space-y-2');
            div.innerHTML = `
                <input type="text" name="topics[${index}][topic]" class="w-full border rounded p-2" placeholder="Judul Topic" >
                <textarea name="topics[${index}][content]" rows="3" class="w-full border rounded p-2" placeholder="Isi Konten" ></textarea>
                <div class="flex justify-end">
                    <button type="button" class="remove-topic bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">Hapus</button>
                </div>
            `;
            container.appendChild(div);
        }

        // tombol hapus topic
        document.addEventListener('click', function(e) {
            if(e.target.classList.contains('remove-topic')) {
                e.target.closest('.topic-item').remove();
            }
        });

        // auto hide flash
        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);

        // Gallery
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
        }

        function nextImage() {
            if (currentGallery.length) {
                currentIndex = (currentIndex + 1) % currentGallery.length;
                showGalleryImage();
            }
        }

        function prevImage() {
            if (currentGallery.length) {
                currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
                showGalleryImage();
            }
        }
    </script>
</x-app-layout>
