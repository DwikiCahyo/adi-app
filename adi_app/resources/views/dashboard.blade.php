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

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash message --}}
            <div id="flash-message" class="hidden mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow transition-opacity duration-500"></div>

            {{-- Button show/hide form mini --}}
            <div class="mb-6 flex justify-end">
                <button id="show-add-card" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Tambah News Feed</button>
            </div>

            {{-- Grid berita --}}
            <div id="news-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Card tambah mini akan dimasukkan di sini --}}
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity duration-300 opacity-0">
        <div class="bg-white backdrop-blur-md p-6 rounded-xl shadow-2xl w-full max-w-lg transform scale-95 transition-all duration-300">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Edit News Feed</h3>
            <form id="edit-news-form">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="edit-title" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea id="edit-content" rows="4" class="mt-1 block w-full p-3 text-gray-900 text-sm bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">URL</label>
                        <input type="text" id="edit-url" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" id="close-modal" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
const newsList = document.getElementById('news-list');
const flashMessage = document.getElementById('flash-message');

const modal = document.getElementById('edit-modal');
const modalContent = modal.querySelector('div');
const editForm = document.getElementById('edit-news-form');
const editId = document.getElementById('edit-id');
const editTitle = document.getElementById('edit-title');
const editContent = document.getElementById('edit-content');
const editUrl = document.getElementById('edit-url');
const closeModalBtn = document.getElementById('close-modal');

const showAddCardBtn = document.getElementById('show-add-card');
let addCardVisible = false;

// Fungsi flash message
function showFlash(message) {
    flashMessage.textContent = message;
    flashMessage.classList.remove('hidden');
    flashMessage.classList.add('opacity-100');
    setTimeout(() => {
        flashMessage.classList.add('hidden');
        flashMessage.classList.remove('opacity-100');
    }, 3000);
}

// Load initial news dari backend
const initialNews = @json($news ?? []);
initialNews.forEach(n => newsList.appendChild(createCard(n, true)));

// Fungsi buat card berita
function createCard(news, animate=false) {
    const card = document.createElement('div');
    card.className = 'bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 opacity-0 scale-95';
    card.dataset.id = news.id;
    card.innerHTML = `
        <h4 class="text-lg font-semibold mb-2">${news.title}</h4>
        <p class="text-gray-700 mb-2">${news.content.substring(0,100)}</p>
        <a href="${news.url}" target="_blank" class="text-blue-600 underline mb-4 block">${news.url}</a>
        <div class="flex justify-end gap-2">
            <button class="edit-btn px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
            <button class="delete-btn px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
        </div>
    `;
    if(animate){
        requestAnimationFrame(() => {
            card.classList.remove('opacity-0','scale-95');
            card.classList.add('opacity-100','scale-100');
        });
    }

    // Edit button -> buka modal
    card.querySelector('.edit-btn').addEventListener('click', () => {
        editId.value = news.id;
        editTitle.value = news.title;
        editContent.value = news.content;
        editUrl.value = news.url;
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        });
    });

    // Delete button
    card.querySelector('.delete-btn').addEventListener('click', async () => {
        if(!confirm('Yakin ingin menghapus berita ini?')) return;
        try {
            const resDelete = await fetch(`/news/${news.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });
            const dataDelete = await resDelete.json();
            if(dataDelete.success){
                card.classList.add('opacity-0','scale-95');
                setTimeout(() => card.remove(),300);
                showFlash('Berita berhasil dihapus!');
            } else {
                alert('Gagal menghapus berita');
            }
        } catch(err) {
            console.error(err);
            alert('Terjadi kesalahan saat hapus');
        }
    });

    return card;
}

// Tambah mini card (form)
function createAddCard() {
    const card = document.createElement('div');
    card.className = 'bg-white p-4 rounded-xl shadow-md flex flex-col gap-4';
    card.innerHTML = `
        <input type="text" id="mini-title" placeholder="Title" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        <textarea id="mini-content" rows="3" placeholder="Content" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        <input type="text" id="mini-url" placeholder="URL" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        <div class="flex justify-end gap-2">
            <button id="mini-save" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Simpan</button>
            <button id="mini-cancel" class="px-3 py-1 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
        </div>
    `;
    return card;
}

// Show/hide mini add card
showAddCardBtn.addEventListener('click', () => {
    if(addCardVisible) return;
    const addCard = createAddCard();
    newsList.prepend(addCard);
    addCardVisible = true;

    // Cancel
    addCard.querySelector('#mini-cancel').addEventListener('click', () => {
        addCard.remove();
        addCardVisible = false;
    });

    // Save
    addCard.querySelector('#mini-save').addEventListener('click', async () => {
        const title = addCard.querySelector('#mini-title').value;
        const content = addCard.querySelector('#mini-content').value;
        const url = addCard.querySelector('#mini-url').value;

        try {
            const res = await fetch(`{{ route('store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({title, content, url})
            });
            const data = await res.json();
            if(data.success){
                const card = createCard(data.data, true);
                newsList.replaceChild(card, addCard); // ganti card mini jadi full card
                addCardVisible = false;
                showFlash('Berita berhasil disimpan!');
            } else {
                alert('Gagal menyimpan berita');
            }
        } catch(err){
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan berita');
        }
    });
});

// Close modal
closeModalBtn.addEventListener('click', () => {
    modalContent.classList.add('scale-95');
    modal.classList.add('opacity-0');
    setTimeout(()=> modal.classList.add('hidden'), 300);
});
modal.addEventListener('click', (e) => {
    if(e.target === modal) closeModalBtn.click();
});

// Submit edit modal
editForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = editId.value;
    const updatedData = {
        title: editTitle.value,
        content: editContent.value,
        url: editUrl.value
    };

    try {
        const resUpdate = await fetch(`/news/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(updatedData)
        });
        const dataUpdate = await resUpdate.json();
        if(dataUpdate.success){
            const card = document.querySelector(`.bg-white[data-id='${id}']`);
            card.querySelector('h4').textContent = updatedData.title;
            card.querySelector('p').textContent = updatedData.content.substring(0,100);
            card.querySelector('a').href = updatedData.url;
            card.querySelector('a').textContent = updatedData.url;

            showFlash('Berita berhasil diperbarui!');
            closeModalBtn.click();
        } else {
            alert('Gagal memperbarui berita');
        }
    } catch(err){
        console.error(err);
        alert('Terjadi kesalahan saat update');
    }
});
</script>
</x-app-layout>
