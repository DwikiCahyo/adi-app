<div class="flex justify-around bg-blue-400 shadow">
    <a href="{{ route('news.index') }}" 
       class="flex flex-col items-center py-2 
              {{ request()->routeIs('news.index') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">News Feed</span>
    </a>

    <a href="{{ route('resource') }}" 
       class="flex flex-col items-center py-2 
              {{ request()->routeIs('resource') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">Resources</span>
    </a>

    <a href="{{ route('events.index') }}" 
        class="flex flex-col items-center py-2 
            {{ request()->routeIs('events.index') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">Events</span>
    </a>
</div>
