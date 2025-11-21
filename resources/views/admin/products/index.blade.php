@extends('layouts.store')

@section('title', 'Admin - Produk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Manajemen Produk</h1>
            <p class="text-muted small mb-0">Kelola katalog produk yang tampil di toko.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Produk</a>
    </div>

    <form method="GET" class="card shadow-sm border-0 mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama produk">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status')==='active')>Aktif</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sortir</label>
                <select name="sort" class="form-select">
                    <option value="">Terbaru</option>
                    <option value="name_asc" @selected(request('sort')==='name_asc')>Nama A-Z</option>
                    <option value="name_desc" @selected(request('sort')==='name_desc')>Nama Z-A</option>
                    <option value="date_desc" @selected(request('sort')==='date_desc')>Tanggal terbaru</option>
                    <option value="date_asc" @selected(request('sort')==='date_asc')>Tanggal terlama</option>
                    <option value="status" @selected(request('sort')==='status')>Status</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-secondary flex-grow-1" type="submit">Terapkan</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Reset</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th class="text-center">Tanggal Muat</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <div class="text-muted small">{{ $product->slug }}</div>
                        </td>
                        <td class="text-center text-muted small">
                            {{ $product->published_at ? \Carbon\Carbon::parse($product->published_at)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $product->stock }}</td>
                        <td class="text-center">
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success-emphasis border border-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection
