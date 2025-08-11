<x-app-layout>
    {{-- <div class="flex flex-col min-h-screen bg-gray-100"> --}}

        <header class="relative flex justify-center items-center bg-blue-400 shadow px-4 py-3">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" alt="Abbalove" class="h-8">
                <a href="#" class="text-lg font-bold text-gray-800">MYCDC</a>
            </div>
        </header>
        


        <!-- Top Menu -->
        <div class="flex justify-around bg-blue-400 shadow">
            <a href="/news" 
               class="flex flex-col items-center py-2 
                      {{ request()->is('news') ? 'text-red-500 border-b-2 border-red-500 font-medium' : 'text-gray-500 hover:text-red-500' }}">
                <span class="text-sm">News Feed</span>
            </a>
        
            <a href="/resources" 
               class="flex flex-col items-center py-2 
                      {{ request()->is('resources') ? 'text-red-500 border-b-2 border-red-500 font-medium' : 'text-gray-500 hover:text-red-500' }}">
                <span class="text-sm">Resources</span>
            </a>
        
            <a href="/events" 
               class="flex flex-col items-center py-2 
                      {{ request()->is('events') ? 'text-red-500 border-b-2 border-red-500 font-medium' : 'text-gray-500 hover:text-red-500' }}">
                <span class="text-sm">Events</span>
            </a>
        </div>
        

        <!-- Main Content -->
        <main class="flex-grow p-4 space-y-4">

            <!-- Latest Sermon Card -->
            @foreach($news as $newsUser)
                <div class="max-w-sm mx-auto rounded-lg overflow-hidden shadow bg-white">
                    <!-- Bagian atas -->
                    <div class="bg-gray-100 p-6 flex items-center justify-between">
                        
                    <img src="{{ asset('images/logo.png') }}" alt="Abbalove" class="h-8">

                    </div>

                    <div class="p-6">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            {{$newsUser->title}}
                        </p>
                        <a href="#" class="text-red-600 font-bold hover:underline">WATCH NOW</a>
                    </div>
                </div>
            @endforeach
        </main>

        <nav class="bg-white shadow fixed bottom-0 inset-x-0 flex justify-around py-2">
            <a href="#" class="flex flex-col items-center text-red-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L2 8h3v8h4V12h2v4h4V8h3L10 2z"/></svg>
                <span class="text-xs">Home</span>
            </a>
            <a href="#" class="flex flex-col items-center text-gray-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M12 2a1 1 0 00-1 1v1.586l-6.707 6.707a1 1 0 000 1.414l5 5a1 1 0 001.414 0l6.707-6.707V3a1 1 0 00-1-1h-4z"/></svg>
                <span class="text-xs">Discover</span>
            </a>
            <a href="#" class="flex flex-col items-center text-gray-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a1 1 0 00-.894.553l-3 6a1 1 0 00.276 1.276L5 12.618V17a1 1 0 001.447.894l4-2a1 1 0 00.553-.894V12.618l3.618-1.789a1 1 0 00.276-1.276l-3-6A1 1 0 0011 3H5z"/></svg>
                <span class="text-xs">Locations</span>
            </a>
            <a href="#" class="flex flex-col items-center text-gray-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4h12v12H4z"/></svg>
                <span class="text-xs">Giving</span>
            </a>
        </nav>

    {{-- </div> --}}
</x-app-layout>
