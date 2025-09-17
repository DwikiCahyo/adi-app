<x-app-layout>
    <x-slot name="header"> 
        <div class="flex items-center sm:-my-px sm:ms-10">
            <nav class="flex gap-4">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('News Feed') }}
                </x-nav-link>
                
                {{-- Dropdown Resource --}}
                <div x-data="{ open: false }" class="relative">
                    <!-- Toggle Button -->
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

                    <!-- Dropdown Menu -->
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
            <h1 class="text-2xl font-bold">Good News</h1>
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
                        <th class="px-4 py-3 border border-gray-300">Content</th>
                        <th class="px-4 py-3 border border-gray-300">Nama File</th>
                        <th class="px-4 py-3 border border-gray-300">File</th>
                        <th class="px-4 py-3 border border-gray-300">Created At</th>
                        <th class="px-4 py-3 border border-gray-300 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @foreach($resourcefile as $item)
                        <tr class="divide-x divide-gray-300 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border border-gray-300 text-center"></td>
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-normal break-words max-w-xs">
                                {{ $item->title }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-normal break-words max-w-md">
                                {{ Str::limit($item->content, 200) }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-normal break-words max-w-md">
                                {{ Str::limit($item->nama_file, 200) }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300">
                                @if($item->file_path)
                                    <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="text-blue-600 underline">Download</a>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-gray-600">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 border border-gray-300 space-x-2">
                                <button onclick="openModal('editModal-{{ $item->id }}')" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.resourcefile.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div id="editModal-{{ $item->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                                <h2 class="text-xl font-bold mb-4">Edit Resource</h2>
                                <form action="{{ route('admin.resourcefile.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                                        <label class="block text-sm font-medium">Nama File</label>
                                        <input type="text" name="nama_file" class="w-full border rounded p-2" value="{{ $item->nama_file }}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Upload File Baru (opsional)</label>
                                        <input type="file" name="file_path" class="w-full border rounded p-2">
                                        @if($item->file_path)
                                            <p class="text-xs text-gray-500 mt-1">
                                                File lama: <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="text-blue-600 underline">Download</a>
                                            </p>
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

    {{-- Modal Create --}}
    <div id="createModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h2 class="text-xl font-bold mb-4">Tambah Resource</h2>
            <form action="{{ route('admin.resourcefile.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label class="block text-sm font-medium">Nama File</label>
                    <input type="text" name="nama_file" value="{{ old('nama_file') }}" class="w-full border rounded p-2">

                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Upload File (opsional)</label>
                    <input type="file" name="file_path" class="w-full border rounded p-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            let table = $('#resourceTable').DataTable({
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
                    emptyTable: "Belum ada data resource."
                },
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false
                }],
                order: [[1, 'asc']]
            });
    
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each((cell, i) => {
                    cell.innerHTML = i + 1;
                });
            }).draw();
    
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
