<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between space-x-8 sm:-my-px sm:ms-10 sm:flex">
            {{-- Nav --}}
            <div class="flex space-x-8">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('News Feed') }}
                </x-nav-link>
                <x-nav-link :href="route('resource')" :active="request()->routeIs('resource')">
                    {{ __('Resources') }}
                </x-nav-link>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash message --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Button tambah --}}
            <button id="add-news-button" title="Tambah Berita Baru"
                class="flex items-center justify-center p-2 rounded-full text-white bg-blue-600 hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/>
                </svg>
            </button>
            <br>
            {{-- Container for new forms --}}
            <div id="news-form-container" class="space-y-8"></div>
        </div>
    </div>

    {{-- Template --}}
    <template id="news-form-template">
        <div class="relative bg-white p-6 rounded-lg shadow-lg mb-8">
            {{-- Tombol close di pojok kanan atas
            <button type="button" class="close-card absolute top-2 right-2 text-gray-400 hover:text-red-500">
                ✕
            </button> --}}
    
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Tambah News Feed</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title[]" required
                            class="mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring focus:border-blue-500" />
                    </div>
    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea name="content[]" rows="4"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border 
                                   border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Write your thoughts here..."></textarea>
                    </div>
    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Url</label>
                        <input type="text" name="url[]" required
                            class="mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring focus:border-blue-500" />
                    </div>
    
                    <div class="flex justify-between gap-4">
                        <button type="button" class="delete-btn px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Hapus
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </template>
    
    <script>
        const addButton = document.getElementById('add-news-button');
        const container = document.getElementById('news-form-container');
        const template = document.getElementById('news-form-template');

        function addNewsForm() {
            const form = template.content.cloneNode(true);
            container.appendChild(form);
        }

        // Klik tombol tambah form
        addButton.addEventListener('click', addNewsForm);

        // Auto tampilkan 1 form saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            addNewsForm();
        });

        // Handler tombol close/delete
        container.addEventListener('click', (e) => {
            if (
                e.target.classList.contains('close-card') ||
                e.target.classList.contains('delete-btn')
            ) {
                const formBox = e.target.closest('.bg-white.p-6');
                if (formBox) {
                    if (e.target.classList.contains('delete-btn')) {
                        if (!confirm('Yakin ingin menghapus form ini?')) return;
                    }
                    formBox.remove();
                }
            }
        });
    </script>
</x-app-layout>
