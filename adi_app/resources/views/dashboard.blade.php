<x-app-layout>
    <x-slot name="header"> 
        <div class="flex items-center justify-between sm:-my-px sm:ms-10 sm:flex"> 
            <div class="flex space-x-8"> 
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> 
                    {{ __('News Feed') }} 
                </x-nav-link>
            </div> 
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
            <h1 class="text-2xl font-bold">Daftar News</h1>
            <button 
                onclick="openModal('createModal')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
            >
                + Tambah News
            </button>
        </div>

        {{-- Tabel DataTables --}}
        <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
            <table id="newsTable" class="min-w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Content</th>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->id }}</td>
                            <td class="px-4 py-3 font-medium whitespace-normal break-words max-w-xs">
                                {{ $item->title }}
                            </td>
                            <td class="px-4 py-3 whitespace-normal break-words max-w-md">
                                {{ Str::limit($item->content, 200) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($item->thumbnail_url && filter_var($item->thumbnail_url, FILTER_VALIDATE_URL))
                                    <img 
                                        src="{{ $item->thumbnail_url }}" 
                                        alt="{{ $item->title }}" 
                                        class="w-20 h-14 object-cover rounded"
                                        onerror="this.onerror=null;this.src='no-image.png';"
                                    >
                                @else
                                    Gambar tidak tersedia
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 space-x-2">
                                <button onclick="openModal('editModal-{{ $item->id }}')" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg">
                                    Edit
                                </button>
                                <form action="{{ route('news.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                                <h2 class="text-xl font-bold mb-4">Edit News</h2>
                                <form action="{{ route('news.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Title</label>
                                        <input type="text" name="title" class="w-full border rounded p-2" value="{{ $item->title }}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Content</label>
                                        <textarea name="content" class="w-full border rounded p-2" rows="4">{{ $item->content }}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">URL</label>
                                        <input type="text" name="url" class="w-full border rounded p-2" value="{{ $item->url }}">
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
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h2 class="text-xl font-bold mb-4">Tambah News</h2>
            <form action="{{ route('store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Content</label>
                    <textarea name="content" class="w-full border rounded p-2" rows="4">{{ old('content') }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">URL</label>
                    <input type="text" name="url" value="{{ old('url') }}" class="w-full border rounded p-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTable CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#newsTable').DataTable({
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
                }
            });

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

        setTimeout(() => {
            let flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0'; 
                setTimeout(() => flash.remove(), 500); 
            }
        }, 3000);
    </script>
</x-app-layout>

