<div class="flex justify-around bg-blue-400 shadow">
    <a href="/news" 
       class="flex flex-col items-center py-2 
              {{ request()->is('news') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">News Feed</span>
    </a>

    <a href="/resources" 
       class="flex flex-col items-center py-2 
              {{ request()->is('resources') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">Resources</span>
    </a>

    <a href="/events" 
       class="flex flex-col items-center py-2 
              {{ request()->is('events') ? 'text-white border-b-2 border-white font-medium' : 'text-white hover:text-gray-200' }}">
        <span class="text-sm">Events</span>
    </a>
</div>
