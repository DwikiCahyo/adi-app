<nav class="bg-white shadow fixed bottom-0 inset-x-0 flex justify-around py-2">
    <a href="{{ url('news') }}" 
       class="flex flex-col items-center {{ request()->is('news') ? 'text-red-500' : 'text-gray-500' }}">
        <img src="{{ asset('images/homelogo2.png') }}" alt="Home" class="w-6 h-6">
        <span class="text-xs">Home</span>
    </a>

    <a href="{{ url('ministry') }}" 
       class="flex flex-col items-center {{ request()->is('ministry') ? 'text-red-500' : 'text-gray-500' }}">
        <img src="{{ asset('images/Ministry_Black.png') }}" alt="Ministry" class="w-6 h-6">
        <span class="text-xs">Ministry</span>
    </a>

    <a href="{{ route('location') }}" 
       class="flex flex-col items-center {{ request()->routeIs('location') ? 'text-red-500' : 'text-gray-500' }}">
        <img src="{{ asset('images/Location.png') }}" alt="Locations" class="w-6 h-6">
        <span class="text-xs">Locations</span>
    </a>

    <a href="{{ url('giving') }}" 
       class="flex flex-col items-center {{ request()->is('giving') ? 'text-red-500' : 'text-gray-500' }}">
        <img src="{{ asset('images/Giving.png') }}" alt="Giving" class="w-6 h-6">
        <span class="text-xs">Giving</span>
    </a>
</nav>
