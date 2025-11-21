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
                            <button type="submit" class="btn btn-primary w-100" @if($product->stock <= 0) disabled @endif>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mt-3">
                            Silakan <a href="{{ route('login') }}">login</a> untuk membeli produk ini.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
