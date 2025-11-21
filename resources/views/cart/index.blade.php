@extends('layouts.store')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Keranjang Belanja</h1>
        <p class="text-muted small">Kelola produk yang ingin kamu beli sebelum checkout.</p>
    </div>

    @if($items->isEmpty())
        <div class="alert alert-info">
            Keranjang masih kosong. Ayo mulai belanja di halaman <a href="{{ route('home') }}">katalog produk</a>.
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    @php
                                        $stock = $item->product->stock;
                                        $exceed = $item->qty > $stock;
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $item->product->name }}
                                            <div class="text-muted small">Stok tersedia: {{ $stock }}</div>
                                            @if($exceed)
                                                <div class="text-danger small">Jumlah di keranjang melebihi stok, sesuaikan.</div>
                                            @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('cart.update', $item) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="qty" value="{{ min($item->qty, $stock) }}" min="0" max="{{ $stock }}" class="form-control form-control-sm" style="width:80px;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('cart.destroy', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini dari keranjang?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Ringkasan</h5>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Subtotal</span>
                            <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Total berat</span>
                            <span>{{ $totalWeight }} gram</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Lanjut ke Checkout</a>
            </div>
        </div>
    @endif
@endsection
