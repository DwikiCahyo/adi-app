<x-app-layout>
    <x-slot name="header">
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
            {{-- Ganti "Dashboard" jadi "News" --}}
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('News Feed') }}
            </x-nav-link>
            <x-nav-link :href="route('resource')" :active="request()->routeIs('resource')">
                {{ __('Resources') }}
            </x-nav-link>

            
        </div>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi sukses --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Upload Berita --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">Tambah Berita Baru</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" id="title" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    {{-- Content --}}
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700">Konten Berita</label>
                        <textarea name="content" id="content" rows="5" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                    </div>

                    {{-- Image --}}
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">Upload Gambar</label>
                        <input type="file" name="image" id="image" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                        Submit Berita
                    </button>
                </form>
            </div>

            {{-- List Berita --}}
            <div class="mt-10">
                <h3 class="text-lg font-semibold mb-4">Daftar Berita</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- @forelse ($news as $item) --}}
                        <div class="border p-4 rounded bg-white shadow">
                            <img src="" class="w-full h-48 object-cover rounded mb-3">
                            <h4 class="font-bold text-lg mb-2"></h4>
                            <p class="text-sm text-gray-700"></p>
                            <p class="text-xs text-gray-500 mt-2">Diposting: </p>
                        </div>
                    {{-- @empty --}}
                        <p>Tidak ada berita.</p>
                    {{-- @endforelse --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
