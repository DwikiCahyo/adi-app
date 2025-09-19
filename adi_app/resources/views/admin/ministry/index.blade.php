<x-app-layout>
    <div class="container mx-auto p-6">
        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-message" class="mb-4 p-4 text-green-800 bg-green-200 rounded-lg transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Ministry</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah Ministry
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="ministryTable" 
                   class="min-w-full text-sm text-left text-gray-700 border border-gray-300">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr class="divide-x divide-gray-300">
                        <th class="px-4 py-3 border border-gray-300 rounded-tl-lg">No</th>
                        <th class="px-4 py-3 border border-gray-300">Title</th>
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Image</th>
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
                                        {{ Str::limit($item->content, 100) }}
                                        @if(strlen($item->content) > 100)
                                            <span class="read-more cursor-pointer text-blue-600 hover:underline ml-1">[Selengkapnya]</span>
                                        @endif
                                    </div>
                                    <div class="content-full hidden">
                                        {{ $item->content }}
                                        <span class="read-less cursor-pointer text-blue-600 hover:underline ml-1">[Sembunyikan]</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-center">
                                @if($item->images->count() > 0)
                                    <div class="flex gap-2 justify-center">
                                        @foreach($item->images->take(2) as $img)
                                            <img src="{{ asset('storage/' . $img->image) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-80"
                                                 onclick="viewImage('{{ asset('storage/' . $img->image) }}')">
                                        @endforeach

                                        @if($item->images->count() > 2)
                                            <div onclick="viewImage('{{ asset('storage/' . $item->images->first()->image) }}')"
                                                 class="w-16 h-16 flex items-center justify-center bg-gray-200 text-gray-600 rounded cursor-pointer hover:bg-gray-300">
                                                +{{ $item->images->count() - 2 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300">{{ $item->category ?? '-' }}</td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-700">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openModal('editModal-{{ $item->id }}')" 
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
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto p-6">
                                <h2 class="text-xl font-bold mb-4">Edit Ministry</h2>
                                <form action="{{ route('admin.ministry.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Title</label>
                                        <input type="text" name="title" value="{{ $item->title }}" 
                                            class="w-full border rounded p-2" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Content</label>
                                        <textarea name="content" rows="4" class="w-full border rounded p-2" required>{{ $item->content }}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Images</label>
                                        @if($item->images->count() > 0)
                                            <div class="flex gap-2 mb-2 flex-wrap">
                                                @foreach($item->images as $img)
                                                    <img src="{{ asset('storage/' . $img->image) }}" 
                                                         alt="{{ $item->title }}" 
                                                         class="w-20 h-20 object-cover rounded">
                                                @endforeach
                                            </div>
                                        @endif
                                        <input type="file" name="images[]" multiple class="w-full border rounded p-2">
                                    </div>                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Category</label>
                                        <select name="category" 
                                            class="w-full border rounded p-2">
                                            <option value="">-- Pilih Category --</option>
                                            <option value="Kids" {{ $item->category == 'Kids' ? 'selected' : '' }}>Kids</option>
                                            <option value="Youth Generation" {{ $item->category == 'Youth Generation' ? 'selected' : '' }}>Youth Generation</option>
                                            <option value="General" {{ $item->category == 'General' ? 'selected' : '' }}>General</option>
                                        </select>
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
            <h2 class="text-xl font-bold mb-4">Tambah Ministry</h2>
            <form action="{{ route('admin.ministry.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border rounded p-2 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Content</label>
                    <textarea name="content" rows="4"
                        class="w-full border rounded p-2 @error('content') border-red-500 @enderror" required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Image</label>
                    <input type="file" name="images[]" multiple
                        class="w-full border rounded p-2 @error('image') border-red-500 @enderror">
                    @error('image')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Category</label>
                    <select name="category" 
                        class="w-full border rounded p-2 @error('category') border-red-500 @enderror" required>
                        <option value="">-- Pilih Category --</option>
                        <option value="Kids" {{ old('category') == 'Kids' ? 'selected' : '' }}>Kids</option>
                        <option value="Youth Generation" {{ old('category') == 'Youth Generation' ? 'selected' : '' }}>Youth Generation</option>
                        <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('category')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Image Viewer (Lightbox) --}}
    <div id="imageViewer" 
         class="hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
        <img id="viewerImg" src="" alt="Preview" class="max-h-[90%] max-w-[90%] rounded shadow-lg">
        <button onclick="closeViewer()" 
                class="absolute top-5 right-5 text-white text-3xl font-bold">&times;</button>
    </div>

    {{-- DataTable CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTables
            $('#ministryTable').DataTable({
                responsive: true,
                pageLength: 10,
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

            // Tampilkan modal jika ada error validasi
            @if($errors->any())
                openModal('createModal');
            @endif

            // Skrip untuk Expand/Collapse Content
            // Menggunakan .on() untuk event delegation agar berfungsi saat DataTables memuat ulang tabel
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

        // Fungsi untuk membuka dan menutup modal
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Image Viewer (Lightbox)
        function viewImage(src) {
            document.getElementById('viewerImg').src = src;
            document.getElementById('imageViewer').classList.remove('hidden');
        }
        function closeViewer() {
            document.getElementById('imageViewer').classList.add('hidden');
        }
        document.getElementById('imageViewer').addEventListener('click', function(e) {
            if (e.target.id === 'imageViewer') closeViewer();
        });

        // Auto-hide flash message
        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);
    </script>
</x-app-layout>