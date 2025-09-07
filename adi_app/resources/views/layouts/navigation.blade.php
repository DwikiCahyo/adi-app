@if(Auth::check())
<nav x-data="{ open: false }" class="bg-blue-500 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('Images/logo.png') }}" alt="Logo" class="h-10 w-auto">
                </a>
                <div class="hidden sm:flex space-x-8">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" 
                        class="flex items-center space-x-2 font-medium text-white relative group px-2 py-1">
                        <span>Home</span>
                    </x-nav-link>

                    <x-nav-link :href="route('ministry')" :active="request()->routeIs('ministry')" 
                        class="flex items-center space-x-2 font-medium text-white relative group px-2 py-1">
                        <span>Ministry</span>
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex items-center space-x-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-2 px-3 py-2 rounded-md bg-white text-gray-700 hover:bg-gray-100 transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06 0L10 10.92l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden flex items-center">
                <button @click="open = !open" class="p-2 rounded-md hover:bg-blue-600 transition">
                    <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" class="sm:hidden bg-blue-400">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <!-- Home -->
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M3 9.75V21h6v-6h6v6h6V9.75l-9-7.5-9 7.5z" />
                    </svg>
                    <span>Home</span>
                </div>
            </x-responsive-nav-link>

            <!-- Ministry -->
            <x-responsive-nav-link :href="route('ministry')" :active="request()->routeIs('ministry')">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M5 12h14M5 6h14M5 18h14" />
                    </svg>
                    <span>Ministry</span>
                </div>
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-blue-300 px-4 py-3">
            <div class="text-white font-semibold">{{ Auth::user()->name }}</div>
            <div class="text-sm text-blue-100">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif
