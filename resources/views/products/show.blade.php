@extends('layouts.store')

@section('title', $product->name)

@section('content')
    @php
        $imgSrc = $product->image
            ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://'])
                ? $product->image
                : asset('storage/' . $product->image))
            : 'https://via.placeholder.com/800x600?text=No+Image';
    @endphp

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="ratio ratio-4x3 bg-light">
                    <img src="{{ $imgSrc }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-primary text-uppercase small fw-semibold mb-1">Detail Produk</p>
                    <h1 class="card-title h3 mb-2">{{ $product->name }}</h1>
                    <p class="text-muted small">
                        {{ $product->weight }} gram •
                        @if($product->stock > 0)
                            <span class="text-success fw-semibold">{{ $product->stock }} stok tersedia</span>
                        @else
                            <span class="text-danger fw-semibold">Stok habis</span>
                        @endif
                    </p>
                    <div class="display-6 text-primary fw-semibold mb-3">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    @if($product->description)
                        <p class="text-muted">{!! nl2br(e($product->description)) !!}</p>
                    @endif

                    @auth
                        <form action="{{ route('cart.store') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <label class="form-label mb-0">Qty</label>
                                <input type="number" name="qty" value="1" min="1" max="{{ $product->stock }}" class="form-control w-auto" style="max-width: 120px;">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1" @if($product->stock <= 0) disabled @endif>
                                    Tambah ke Keranjang
                                </button>
                        </form>
                        <form action="{{ route('wishlist.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-danger" title="Tambah ke Wishlist">
                                ❤
                            </button>
                        </form>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            Silakan <a href="{{ route('login') }}">login</a> untuk membeli produk ini.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-8">
            <h3 class="h4 mb-4">Ulasan Produk</h3>

            @if($product->reviews->isEmpty())
                <div class="alert alert-light border">Belum ada ulasan untuk produk ini.</div>
            @else
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($product->reviews as $review)
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">{{ $review->user->name }}</div>
                                    <div class="text-muted small">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="mb-2 text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating) ★ @else ☆ @endif
                                    @endfor
                                </div>
                                <p class="mb-0 text-muted">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @auth
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Tulis Ulasan</h5>
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select w-auto">
                                    <option value="5">5 - Sangat Bagus</option>
                                    <option value="4">4 - Bagus</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="2">2 - Kurang</option>
                                    <option value="1">1 - Buruk</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Komentar</label>
                                <textarea name="comment" rows="3" class="form-control" placeholder="Bagaimana pendapatmu tentang produk ini?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Silakan <a href="{{ route('login') }}">login</a> untuk menulis ulasan.
                </div>
            @endauth
        </div>
    </div>
@endsection
