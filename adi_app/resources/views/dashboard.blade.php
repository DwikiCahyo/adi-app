<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Welcome Message --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">Welcome back, {{ Auth::user()->name }}! 🎉</h3>
                        <p class="text-sm text-gray-600">You're logged in as admin.</p>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-indigo-100 p-4 rounded-lg shadow">
                            <h4 class="text-sm text-gray-600">Total Users</h4>
                            <p class="text-2xl font-bold text-indigo-800">1,245</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg shadow">
                            <h4 class="text-sm text-gray-600">Transactions</h4>
                            <p class="text-2xl font-bold text-green-800">538</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg shadow">
                            <h4 class="text-sm text-gray-600">Products</h4>
                            <p class="text-2xl font-bold text-yellow-800">78</p>
                        </div>
                        <div class="bg-red-100 p-4 rounded-lg shadow">
                            <h4 class="text-sm text-gray-600">Revenue</h4>
                            <p class="text-2xl font-bold text-red-800">$12,345</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

