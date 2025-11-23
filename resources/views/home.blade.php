@extends('layouts.store')

@section('title', 'Katalog Produk')

@section('content')
    <div class="bg-white rounded-3 shadow-sm p-4 p-md-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <p class="text-primary text-uppercase small fw-semibold mb-1">Selamat datang</p>
                <h1 class="display-6 fw-semibold mb-3">Temukan produk terbaik untuk keseharianmu</h1>
                <p class="text-muted mb-3">Belanja mudah, aman, dan cepat. Semua produk dikurasi langsung oleh pemilik toko.</p>
                <form class="d-flex gap-2" method="GET" action="{{ route('home') }}">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari produk...">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                <div class="text-muted small mt-2">
                    Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="p-4 rounded-3 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <p class="text-uppercase small fw-semibold mb-1">Promo</p>
                    <h2 class="h5 fw-semibold mb-2">Diskon spesial pelanggan baru</h2>
                    <p class="mb-3">Gunakan kode <span class="fw-semibold">WELCOME10</span> saat checkout.</p>
                    <div class="display-6 fw-bold">10% <span class="fs-6 fw-normal">off</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Telegram Button Fixed Bottom Right -->
    <a href="https://t.me/teknofo_bot"
       target="_blank" rel="noopener noreferrer"
       class="btn btn-primary position-fixed d-flex align-items-center gap-2 shadow-lg rounded-pill"
       style="bottom: 20px; right: 20px; z-index: 9999; border: 2px solid white;">
        <span class="fs-5">💬</span> <span class="fw-semibold">Chat Telegram</span>
    </a>

    @if(isset($categories) && $categories->count() > 0)
        <div class="mb-4 overflow-auto">
            <div class="d-flex gap-2 pb-2">
                <a href="{{ route('home', request()->except('category')) }}" 
                   class="btn btn-sm {{ !request('category') ? 'btn-dark' : 'btn-outline-dark' }} text-nowrap rounded-pill px-3">
                    Semua
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('home', array_merge(request()->except('category'), ['category' => $category->slug])) }}" 
                       class="btn btn-sm {{ request('category') == $category->slug ? 'btn-dark' : 'btn-outline-dark' }} text-nowrap rounded-pill px-3">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($products->isEmpty())
        <div class="alert alert-secondary text-center py-5">
            <div class="mb-3">📦</div>
            Belum ada produk yang tersedia saat ini.
        </div>
    @else
        <div class="row g-3 g-md-4">
            @foreach($products as $product)
                @php
                    $imgSrc = $product->image
                        ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://'])
                            ? $product->image
                            : asset('storage/' . $product->image))
                        : 'https://via.placeholder.com/400x400?text=No+Image';
                @endphp
                <!-- Adjusted for 3 per row on medium+ screens, 2 per row on mobile -->
                <div class="col-6 col-md-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden transition-hover">
                        <!-- Changed ratio to 1x1 for square, cleaner look -->
                        <div class="ratio ratio-1x1 bg-light">
                            <img src="{{ $imgSrc }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="card-title h6 fw-bold text-truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-primary fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-muted small mb-3 flex-fill">
                                <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    <i class="bi {{ $product->stock > 0 ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                    {{ $product->stock > 0 ? 'Stok: ' . $product->stock : 'Habis' }}
                                </span>
                            </p>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary w-100 mt-auto stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
@endsection
