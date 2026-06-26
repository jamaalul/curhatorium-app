@extends('layouts.dashboard')

@section('title', 'Marketplace | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="Marketplace">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <!-- Banner Section -->
    <div class="relative bg-teal-500 pt-16 pb-20 text-center">
        <h1 class="z-10 relative drop-shadow-md font-extrabold text-white text-4xl md:text-5xl tracking-wider">Marketplace Coooy</h1>
        <div class="bottom-0 left-0 z-20 absolute bg-yellow-400 py-2 w-full">
            <p class="text-black text-sm md:text-base">Made with the best quality materials, modern design, and great prices.</p>
        </div>
    </div>

    <div class="flex md:flex-row flex-col gap-10 mx-auto px-4 sm:px-6 lg:px-8 py-12 max-w-7xl">
        <!-- Sidebar -->
        <div class="flex-shrink-0 w-full md:w-1/4">
            <h2 class="mb-6 font-bold text-gray-900 text-lg">Kategori produk</h2>
            <ul class="space-y-4 text-gray-700">
                <li><a href="#" class="hover:text-black transition-colors">All Products</a></li>
                <li><a href="#" class="hover:text-black transition-colors">T-Shirt</a></li>
                <li><a href="#" class="hover:text-black transition-colors">Long Sleeve</a></li>
                <li><a href="#" class="hover:text-black transition-colors">Hoodie</a></li>
                <li><a href="#" class="hover:text-black transition-colors">Sweater</a></li>
                <li><a href="#" class="hover:text-black transition-colors">Totebag</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <!-- Top Bar -->
            <div class="flex justify-between items-center mb-8 pb-4 border-gray-200 border-b">
                <div></div>
                <div class="flex items-center gap-3">
                    <label for="sort" class="font-bold text-gray-900 text-sm">Sortir</label>
                    <select id="sort" class="px-3 py-1.5 border border-gray-300 focus:border-black rounded-sm outline-none focus:ring-black text-sm">
                        <option>Produk Terbaru</option>
                        <option>Harga Terendah</option>
                        <option>Harga Tertinggi</option>
                    </select>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-gray-500">No products available.</p>
                </div>
            @else
                <div class="gap-x-8 gap-y-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($products as $product)
                        <div class="group flex flex-col">
                            <!-- Image -->
                            <a href="#" class="block relative flex justify-center items-center bg-[#f8f8f8] mb-4 aspect-square overflow-hidden">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->publicUrl() }}" alt="{{ $product->name }}" class="w-[80%] h-[80%] object-contain group-hover:scale-105 transition-transform duration-300 mix-blend-multiply">
                                @else
                                    <div class="text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </a>

                            <!-- Details -->
                            <h3 class="mb-1 font-bold text-gray-900 text-sm leading-snug">
                                <a href="#" class="hover:underline">{{ $product->name }}</a>
                            </h3>
                            <p class="mb-4 text-gray-700 text-sm">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

                            <!-- Links -->
                            <div class="flex flex-col gap-1 mt-auto text-[13px]">
                                <a href="#" class="text-green-500 hover:text-green-600 transition-colors">Tokopedia</a>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <span class="border-gray-400 border-t w-6"></span>
                                    <a href="#" class="text-[#ee4d2d] hover:text-[#d74223] transition-colors">Shopee</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection