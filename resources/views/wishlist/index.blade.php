@extends('layouts.store')

@section('title', 'Wishlist Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Wishlist Saya</h1>
    </div>

    @if($wishlists->isEmpty())
        <div class="alert alert-info">
            Wishlist Anda masih kosong. <a href="{{ route('home') }}">Cari produk</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($wishlists as $wishlist)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm border-0">
                        @php
                            $product = $wishlist->product;
                            $imgSrc = $product->image
                                ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://'])
                                    ? $product->image
                                    : asset('storage/' . $product->image))
                                : 'https://via.placeholder.com/600x400?text=No+Image';
                        @endphp
                        <div class="ratio ratio-4x3">
                            <img src="{{ $imgSrc }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title h6 mb-2">{{ $product->name }}</h5>
                            <div class="fw-semibold text-primary mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            
                            <div class="mt-auto d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary flex-grow-1">Lihat</a>
                                <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" onsubmit="return confirm('Hapus dari wishlist?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
