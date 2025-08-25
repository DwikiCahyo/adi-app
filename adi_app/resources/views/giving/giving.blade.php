@extends('layouts.newsApp')
@section('content')

<div class="p-4">
    {{-- Card Gabungan --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-6xl mx-auto">

        {{-- Banner --}}
        <div class="relative">
            <img src="{{ asset('images/menabur.jpg') }}" 
                alt="Giving" 
                class="w-full h-64 md:h-80 object-cover">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

            {{-- Teks --}}
            <div class="absolute inset-0 flex items-center justify-center p-6">
                <p class="text-white text-2xl md:text-3xl font-bold text-center max-w-2xl leading-relaxed drop-shadow-lg">
                    Menabur adalah tindakan iman untuk melihat adanya pertumbuhan.
                </p>
            </div>
        </div>


        {{-- Rekening --}}
        <div class="p-6 md:p-10 space-y-6">
            <div>
                <h3 class="text-2xl md:text-3xl font-bold">BCA</h3>
                <p class="font-semibold text-gray-700 text-lg md:text-xl">a/n GBDI JEMAAT KASIH KARUNIA</p>
            </div>

            <div class="space-y-4 text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Perpuluhan, Ucapan Syukur, dll</span>
                    <button onclick="copyToClipboard('4878888829')" 
                            class="flex items-center gap-2 text-sm md:text-base border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-100">
                        487 8888 829
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <span>Dana Gedung</span>
                    <button onclick="copyToClipboard('4875555538')" 
                            class="flex items-center gap-2 text-sm md:text-base border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-100">
                        487 5555 538
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <span>Dana Misi</span>
                    <button onclick="copyToClipboard('4875555571')" 
                            class="flex items-center gap-2 text-sm md:text-base border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-100">
                        487 5555 571
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toast" 
     class="fixed inset-0 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M5 13l4 4L19 7" />
        </svg>
        <span>Nomor rekening berhasil disalin</span>
    </div>
</div>

<script>
    function copyToClipboard(accountNumber) {
        navigator.clipboard.writeText(accountNumber).then(() => {
            const toast = document.getElementById('toast');
            toast.classList.remove('opacity-0', 'pointer-events-none');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
                toast.classList.remove('opacity-100');
            }, 2000);
        });
    }
</script>

@endsection
