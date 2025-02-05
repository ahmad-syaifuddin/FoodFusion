@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <input type="hidden" name="address_id" id="address_id_input">
            <input type="hidden" name="total_amount" id="total_amount_input" value="{{ $cartTotal }}">
            @foreach ($cartItems as $item)
                <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
            @endforeach
            <div class="row">
                <!-- Delivery Address -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Alamat Pengiriman</h5>
                                <button type="button" class="btn btn-outline-custom btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addAddressModal">
                                    <i class="bi bi-plus"></i> Tambah Alamat Baru
                                </button>
                            </div>

                            <div class="addresses-container">
                                @foreach (auth()->user()->addresses as $address)
                                    <div
                                        class="address-item mb-3 p-3 border rounded @if ($address->is_primary) border-primary @endif">
                                        <div class="form-check">
                                            <input class="form-check-input radio-custom" type="radio"
                                                name="selected_address" id="address_{{ $address->id }}"
                                                value="{{ $address->id }}"
                                                @if ($address->is_primary) checked @endif>
                                            <label class="form-check-label" for="address_{{ $address->id }}">
                                                <strong>{{ $address->label }}</strong>
                                                @if ($address->is_primary)
                                                    <span class="badge bg-custom ms-2">Utama</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="ms-4 mt-2">
                                            <p class="mb-1"><strong>{{ $address->receiver_name }}</strong></p>
                                            <p class="mb-1">{{ $address->phone_number }}</p>
                                            <p class="mb-0">{{ $address->full_address }}</p>
                                        </div>
                                        <div class="mt-2 ms-4">
                                            <button type="button" class="btn btn-link btn-sm p-0 text-primary me-3"
                                                data-bs-toggle="modal" data-bs-target="#editAddressModal"
                                                data-address-id="{{ $address->id }}">
                                                Edit
                                            </button>
                                            @if (!$address->is_primary)
                                                <button type="button" class="btn btn-link btn-sm p-0 text-danger"
                                                    onclick="deleteAddress({{ $address->id }})">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Detail Pembelian</h5>
                            <div id="checkout-items">
                                @foreach ($cartItems as $item)
                                    <div class="d-flex flex-column flex-sm-row mb-3 border-bottom pb-3">
                                        <div class="text-center text-sm-start mb-3 mb-sm-0">
                                            <img src="{{ asset('storage/' . $item->product->gambar) }}"
                                                alt="{{ $item->product->nama_produk }}" class="img-thumbnail me-sm-3"
                                                style="width: 120px; height: 120px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2">{{ $item->product->nama_produk }}</h6>
                                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                                <div>
                                                    <p class="mb-1">{{ $item->quantity }} x Rp
                                                        {{ number_format($item->product->harga, 0, ',', '.') }}</p>
                                                    @if ($item->product->diskon > 0)
                                                        <p class="mb-2 text-danger">
                                                            Diskon {{ $item->product->diskon }}%
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="mt-2 mt-sm-0">
                                                    <h6 class="mb-0 text-end">
                                                        Rp
                                                        {{ number_format($item->product->harga * $item->quantity - ($item->product->harga * $item->quantity * $item->product->diskon) / 100, 0, ',', '.') }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Catatan</h5>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Tambahkan catatan untuk penjual (opsional)"></textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Metode Pembayaran</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input radio-custom" type="radio" name="payment_method"
                                    id="transfer" value="transfer">
                                <label class="form-check-label" for="transfer">
                                    Transfer Bank
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input radio-custom" type="radio" name="payment_method"
                                    id="cod" value="Cash on Delivery">
                                <label class="form-check-label" for="cod">
                                    Bayar di Tempat (COD)
                                </label>
                            </div>

                            <!-- Transfer Info -->
                            <div id="transfer-info" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Informasi Transfer:</h6>
                                    <p class="mb-0">Setelah checkout, Anda akan diarahkan ke halaman pembayaran untuk
                                        melihat
                                        nomor rekening dan mengunggah bukti transfer.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Ringkasan Pesanan</h5>

                            <!-- Voucher Section -->
                            <div class="mb-4">
                                <label for="voucher_code" class="form-label">Kode Voucher</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="voucher_code" name="voucher_code"
                                        placeholder="Masukkan kode voucher">
                                    <button class="btn btn-outline-custom" type="button" id="apply_voucher">
                                        Terapkan
                                    </button>
                                </div>
                                <div id="voucher_message" class="small mt-2"></div>
                            </div>

                            <div class="border-top pt-3">
                                <!-- Detail harga per produk -->
                                @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="">{{ $item->product->nama_produk }} ({{ $item->quantity }})</span>
                                    <span>Rp {{ number_format($item->product->harga * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Diskon Total Produk</span>
                                    <span id="product_discount" class="text-success">-Rp
                                        {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" id="discount_row"
                                        style="display: none !important;">
                                        <span>Diskon Voucher</span>
                                        <span id="discount_amount" class="text-success">-Rp 0</span>
                                    </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ongkos Kirim</span>
                                <span id="shipping_cost">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="">Subtotal</strong>
                                <strong id="subtotal">Rp {{ number_format($cartTotal, 0, ',', '.') }}</strong>
                            </div>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <strong>Total</strong>
                                    <strong id="total_amount">Rp {{ number_format($cartTotal, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <!-- Hidden inputs for voucher -->
                            <input type="hidden" id="applied_voucher_id" name="voucher_id">
                            <input type="hidden" id="applied_voucher_code" name="voucher_code">
                            <input type="hidden" id="applied_discount_amount" name="voucher_discount" value="0">
                            
                            <!-- Add hidden input for cart items -->
                            @if (isset($directBuy) && $directBuy)
                                <input type="hidden" name="direct_buy" value="1">
                                <input type="hidden" name="produk_id" value="{{ $cartItems->first()->produk_id }}">
                                <input type="hidden" name="quantity" value="{{ $cartItems->first()->quantity }}">
                            @else
                                @foreach ($cartItems as $item)
                                    <input type="hidden" name="cart_items[]" value="{{ $item->id }}">
                                @endforeach
                            @endif

                            <button type="submit" class="btn btn-custom w-100 mt-3" id="btn-pay">
                                <span class="normal-state">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Bayar Sekarang
                                </span>
                                <span class="loading-state d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                        aria-hidden="true"></span>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Address Modals -->
    @include('components.address-modals')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/checkout.js') }}"></script>
        <script src="{{ asset('js/address.js') }}"></script>
    @endpush
@endsection
