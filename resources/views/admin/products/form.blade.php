@extends('layouts.store')

@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
    <div class="mb-3">
        <h1 class="h4 mb-1">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>
    </div>

    <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($product->exists)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="form-control">
                            <div class="form-text">Gunakan huruf kecil dan tanda minus (-), misalnya: kaos-basic-hitam.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga</label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berat (gram)</label>
                                <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Muat / Tayang</label>
                                <input type="date" name="published_at" value="{{ old('published_at', $product->published_at ? \Carbon\Carbon::parse($product->published_at)->format('Y-m-d') : '') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <label class="form-label">Gambar Produk</label>
                        @php
                            $imgSrc = $product->image
                                ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('storage/' . $product->image))
                                : null;
                        @endphp
                        @if($imgSrc)
                            <div class="mb-2">
                                <img src="{{ $imgSrc }}" class="img-fluid rounded border" alt="">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold small">Status Produk</div>
                            <div class="text-muted small">Centang untuk menampilkan di katalog.</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
        </div>
    </form>
@endsection
