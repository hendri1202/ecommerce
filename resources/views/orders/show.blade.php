@extends('layouts.store')

@section('title', 'Detail Pesanan '.$order->code)

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 mb-1">Pesanan {{ $order->code }}</h1>
            <p class="text-muted small mb-0">Detail pesanan dan status terkini.</p>
        </div>
        <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary btn-sm">Kembali ke riwayat</a>
    </div>

    @php
        $statusClass = [
            'pending' => 'bg-warning-subtle text-warning-emphasis border-warning',
            'paid' => 'bg-success-subtle text-success-emphasis border-success',
            'shipped' => 'bg-info-subtle text-info-emphasis border-info',
            'completed' => 'bg-success-subtle text-success-emphasis border-success',
            'cancelled' => 'bg-danger-subtle text-danger-emphasis border-danger',
        ][$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis border-secondary';
    @endphp

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Pengiriman</h5>
                    <p class="mb-1"><span class="text-muted">Nama:</span> {{ $order->recipient_name }}</p>
                    <p class="mb-1">
                        <span class="text-muted">Alamat:</span>
                        {{ $order->address }}, {{ $order->city }}, {{ $order->province }} ({{ $order->postal_code }})
                    </p>
                    <p class="mb-1"><span class="text-muted">Kurir:</span> {{ strtoupper($order->courier) }} - {{ $order->service }}</p>
                    <p class="mb-0"><span class="text-muted">Dibuat:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Item Pesanan</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Status Pesanan</span>
                        <span class="badge {{ $statusClass }} border">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span class="fw-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Ongkir</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Total</span>
                        <span class="fs-5 fw-bold text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
