@extends('layouts.app')

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <!-- Status Banner -->
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 text-success rounded-pill py-2 px-4 d-inline-block mb-3">
                                <i class="bi bi-check-circle me-2"></i>Pesanan Berhasil Dibuat
                            </div>
                            <h4 class="mb-1">Terima Kasih, {{ $order->user->name }}!</h4>
                            <p class="text-muted mb-0">Nomor Pesanan: #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <!-- Order Details -->
                        <div class="border rounded-3 p-3 mb-4">
                            <h5 class="mb-3">Detail Pesanan</h5>

                            <!-- Order Items -->
                            @foreach ($order->orderItems as $item)
                                <div class="d-flex flex-column flex-sm-row align-items-center mb-3 pb-3 border-bottom">
                                    <img src="{{ asset('storage/' . $item->produk->gambar) }}"
                                        alt="{{ $item->produk->nama_produk }}" class="rounded mb-2 mb-sm-0" width="100" height="100"
                                        style="object-fit: cover;">
                                    <div class="ms-sm-3 flex-grow-1 text-center text-sm-start">
                                        <h6 class="mb-1">{{ $item->produk->nama_produk }}</h6>
                                        <div class="text-muted small">
                                            {{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}
                                            @if ($item->discount > 0)
                                                <span class="text-danger">(Diskon {{ intval($item->discount) }}%)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Order Summary -->
                            <div class="bg-light rounded p-3">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="text-muted mb-1 mb-sm-0">Total Item</span>
                                    <span>{{ $order->qty }} barang</span>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="text-muted mb-1 mb-sm-0">Harga Awal</span>
                                    <span>Rp{{ number_format($order->total_original_price, 0, ',', '.') }}</span>
                                </div>
                                @if ($order->total_discount > 0)
                                    <div class="d-flex flex-wrap justify-content-between mb-2">
                                        <span class="text-muted mb-1 mb-sm-0">Total Diskon</span>
                                        <span class="text-danger">-Rp{{ number_format($order->total_discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <strong class="mb-1 mb-sm-0">Total Pembayaran</strong>
                                    <strong class="text-danger">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="border rounded-3 p-3 mb-4">
                            <h5 class="mb-3">Informasi Pembayaran</h5>
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                <span class="text-muted mb-1 mb-sm-0">Metode Pembayaran</span>
                                <span class="text-capitalize">{{ $order->payment_method }}</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                <span class="text-muted mb-1 mb-sm-0">Status Pembayaran</span>
                                <span class="badge {{ $order->status === 'pending' ? 'bg-warning' : 'bg-info' }}">
                                    {{ ucfirst($order->status_label) }}
                                </span>
                            </div>
                            @if ($order->payment_method === 'transfer' && $order->payment_proof)
                                <div class="mt-3">
                                    <label class="form-label">Bukti Transfer</label>
                                    <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Transfer"
                                        class="img-fluid rounded" style="max-height: 200px">
                                </div>
                            @endif
                        </div>

                        <!-- Customer Info -->
                        <div class="border rounded-3 p-3">
                            <h5 class="mb-3">Informasi Penerima</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Nama</span>
                                <span>{{ $order->address->receiver_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Alamat</span>
                                <span class="text-end" style="max-width: 60%">{{ $order->address->full_address }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4 me-3 mb-3 d-flex flex-column flex-md-row">
                            <a href="{{ route('home.index') }}" class="btn btn-outline-custom w-100 mb-2 mb-md-0 me-md-2">
                                Kembali ke Beranda
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn btn-custom w-100">
                                Lihat Pesanan Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
