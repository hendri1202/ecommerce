@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Checkout</h1>
        <p class="text-muted small mb-0">Pilih alamat tersimpan lalu selesaikan pesanan.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ringkasan Keranjang</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $item->product->name }}</div>
                                    <div class="text-muted small">{{ $item->qty }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="pt-3 border-top mt-3 small text-muted">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span class="fw-semibold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total berat</span>
                            <span>{{ $totalWeight }} gram</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Alamat Pengiriman</h5>

                    @if($addresses->isEmpty())
                        <div class="alert alert-warning">
                            Belum ada alamat tersimpan. Silakan tambah di halaman <a href="{{ route('profile.edit') }}">Profil</a>.
                        </div>
                    @endif

                    <form action="{{ route('checkout.store') }}" method="POST" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label class="form-label">Pilih Alamat</label>
                            <select name="address_id" class="form-select" {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" @if($address->is_default) selected @endif>
                                        {{ $address->label ?? 'Alamat' }} - {{ $address->city ?? '' }} @if($address->is_default) (Default) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted small mt-2">Kelola alamat di halaman Profil.</div>
                        </div>

                        @php
                            $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
                        @endphp
                        @if($defaultAddress)
                            <div class="col-12">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold">{{ $defaultAddress->recipient_name }}</div>
                                    <div class="text-muted small">{{ $defaultAddress->phone }}</div>
                                    <div class="text-muted small">{{ $defaultAddress->address }}, {{ $defaultAddress->city }}, {{ $defaultAddress->province }} ({{ $defaultAddress->postal_code }})</div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Kurir</label>
                            <select name="courier" class="form-select">
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Layanan & Ongkir (dummy)</label>
                            <select name="service" id="service" class="form-select">
                                <option value="REG" data-cost="20000">REG - Rp 20.000</option>
                                <option value="YES" data-cost="35000">YES - Rp 35.000</option>
                            </select>
                            <input type="hidden" name="shipping_cost" id="shipping_cost" value="20000">
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <div class="fw-semibold">Total (estimasi)</div>
                            <div class="fs-5 fw-bold text-primary">
                                Rp <span id="total_display">{{ number_format($subtotal + 20000, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100" {{ $addresses->isEmpty() ? 'disabled' : '' }}>Buat Pesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const serviceSelect = document.getElementById('service');
    const shippingCostInput = document.getElementById('shipping_cost');
    const totalDisplay = document.getElementById('total_display');
    const subtotal = {{ $subtotal }};

    function updateTotal() {
        const selected = serviceSelect.options[serviceSelect.selectedIndex];
        const cost = parseInt(selected.dataset.cost);
        shippingCostInput.value = cost;
        totalDisplay.innerText = (subtotal + cost).toLocaleString('id-ID');
    }

    serviceSelect?.addEventListener('change', updateTotal);
</script>
@endpush
