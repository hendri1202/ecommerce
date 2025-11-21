@extends('layouts.store')

@section('title', 'Riwayat Belanja')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Riwayat Belanja</h1>
        <p class="text-muted small mb-0">Lihat status pesanan yang pernah kamu buat.</p>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info">Belum ada pesanan. Pesananmu akan muncul di sini setelah checkout.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $statusClass = [
                                    'pending' => 'bg-warning-subtle text-warning-emphasis border-warning',
                                    'paid' => 'bg-success-subtle text-success-emphasis border-success',
                                    'shipped' => 'bg-info-subtle text-info-emphasis border-info',
                                    'completed' => 'bg-success-subtle text-success-emphasis border-success',
                                    'cancelled' => 'bg-danger-subtle text-danger-emphasis border-danger',
                                ][$order->status] ?? 'bg-secondary-subtle text-secondary-emphasis border-secondary';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $order->code }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }} border">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
