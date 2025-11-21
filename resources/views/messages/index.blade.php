@extends('layouts.store')

@section('title', 'Pesan')

@section('content')
    <div class="mb-3">
        <h1 class="h4 mb-1">Chat</h1>
        <p class="text-muted small mb-0">Pisah per pelanggan, dengan indikator pesan belum dibaca.</p>
    </div>

    <div class="row g-4">
        @if(auth()->user()->isAdmin())
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Pelanggan</h6>
                        <div class="list-group list-group-flush">
                            @foreach($customers as $customer)
                                @php
                                    $unread = $unreadPerCustomer[$customer->id] ?? 0;
                                    $active = request('customer_id', $customers->first()->id ?? null) == $customer->id;
                                @endphp
                                <a href="{{ route('messages.index', ['customer_id' => $customer->id]) }}"
                                   class="list-group-item d-flex justify-content-between align-items-center @if($active) active @endif">
                                    <span>{{ $customer->name }}</span>
                                    @if($unread > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $unread }}</span>
                                    @endif
                                </a>
                            @endforeach
                            @if($customers->isEmpty())
                                <div class="text-muted small">Belum ada pelanggan.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
        @else
            <div class="col-lg-12">
        @endif
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Kirim Pesan</h6>
                                <form method="POST" action="{{ route('messages.store') }}" class="row g-3">
                                    @csrf

                                    @if(auth()->user()->isAdmin())
                                        <div class="col-12">
                                            <label class="form-label">Pilih Customer</label>
                                            <select name="to_user_id" class="form-select" required>
                                                <option value="">-- pilih customer --</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }} ({{ $customer->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <label class="form-label">Terkait Order (opsional)</label>
                                        <select name="order_id" class="form-select">
                                            <option value="">Tidak ada</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->id }}">{{ $order->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Pesan</label>
                                        <textarea name="body" rows="3" class="form-control" required>{{ old('body') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Riwayat Chat</h6>
                                <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                                    @foreach($messages as $msg)
                                        @php
                                            $isMine = $msg->user_id === auth()->id();
                                            $unreadBadge = (!$isMine && !$msg->is_read) ? true : false;
                                        @endphp
                                        <div class="mb-3">
                                            <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                                                <div class="p-2 rounded {{ $isMine ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                                    <div class="small fw-semibold d-flex align-items-center gap-2">
                                                        {{ $isMine ? 'Saya' : ($msg->user->name ?? 'User') }}
                                                        @if($unreadBadge)
                                                            <span class="badge bg-danger">Baru</span>
                                                        @endif
                                                        @if($msg->order)
                                                            <span class="badge bg-warning text-dark">Order: {{ $msg->order->code }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="small">{!! nl2br(e($msg->body)) !!}</div>
                                                    <div class="text-muted small mt-1">{{ $msg->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    {{ $messages->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @if(auth()->user()->isAdmin())
            </div>
        @else
            </div>
        @endif
    </div>
@endsection
