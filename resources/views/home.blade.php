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

    @if(config('app.telegram_bot_url') || env('TELEGRAM_BOT_URL'))
        <a href="{{ config('app.telegram_bot_url', env('TELEGRAM_BOT_URL')) }}"
           target="_blank" rel="noopener noreferrer"
           class="btn btn-primary position-fixed d-flex align-items-center gap-2 shadow-lg"
           style="bottom: 24px; right: 24px; z-index: 1050;">
            <span class="fs-5">💬</span> Chat Telegram
        </a>
    @endif

    @if($products->isEmpty())
        <div class="alert alert-secondary text-center">Belum ada produk yang tersedia.</div>
    @else
        <div class="row g-3">
            @foreach($products as $product)
                @php
                    $imgSrc = $product->image
                        ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://'])
                            ? $product->image
                            : asset('storage/' . $product->image))
                        : 'https://via.placeholder.com/600x400?text=No+Image';
                @endphp
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="ratio ratio-4x3">
                            <img src="{{ $imgSrc }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">{{ $product->name }}</h5>
                            <p class="text-muted small mb-2">Stok:
                                <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $product->stock > 0 ? $product->stock . ' tersedia' : 'Habis' }}
                                </span>
                            </p>
                            <div class="fw-semibold fs-5 text-primary mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary w-100 mt-auto">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
@endsection
