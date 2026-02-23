<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="grid md:grid-cols-3 gap-6">

            {{-- PRODUCTS --}}
            <a class="bg-white shadow-xl rounded-2xl p-6 border" href="{{ route('products.index') }}">
                <p class="text-sm text-gray-500">Products</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ $count_of_products }}
                </p>
            </a>

            {{-- ORDERS --}}
            <a href="{{ route('orders.index') }}" class="bg-white shadow-xl rounded-2xl p-6 border">
                <p class="text-sm text-gray-500">Orders</p>
                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ $count_of_orders }}
                </p>
            </a>

            {{-- CATEGORIES --}}
            <a href="{{ route('categories.index') }}" class="bg-white shadow-xl rounded-2xl p-6 border">
                <p class="text-sm text-gray-500">Categories</p>
                <p class="text-3xl font-bold text-purple-600 mt-2">
                    {{ $count_of_categories }}
                </p>
            </a>

        </div>

    </div>
</div>
</x-app-layout>
