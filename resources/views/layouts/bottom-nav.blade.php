<div class="d-block d-lg-none bg-light fixed-bottom">
    <div class="container d-flex justify-content-around py-1">
        <a href="{{ route('home.index') }}"
            class="text-center text-decoration-none {{ Route::currentRouteName() == 'home.index' ? 'text-custom' : 'text-secondary' }}">
            <i class="bi bi-house fs-5"></i>
            <div class="small">Home</div>
        </a>
        @auth
            <a href="{{ route('wishlist.index') }}"
                class="text-center text-decoration-none position-relative {{ Route::currentRouteName() == 'wishlist.index' ? 'text-custom' : 'text-secondary' }}">
                <i class="bi bi-heart fs-5"></i>
                <div class="small">Favorit</div>
                @if ($wishlistCount > 0)
                    <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                        style="top: 0; right: 10px;">
                        {{ $wishlistCount }}
                    </span>
                @endif
            </a>
        @else
            <a href="#" onclick="showLoginAlert()"
                class="text-center text-decoration-none position-relative text-secondary">
                <i class="bi bi-heart fs-5"></i>
                <div class="small">Favorit</div>
            </a>
        @endauth

        @auth
            <a href="{{ route('payment.index') }}"
                class="text-center text-decoration-none {{ Route::currentRouteName() == 'payment.index' ? 'text-custom' : 'text-secondary' }}">
                <i class="bi bi-credit-card fs-5"></i>
                <div class="small">Pembayaran</div>
            </a>
        @else
            <a href="#" onclick="showLoginAlert()" class="text-center text-decoration-none text-secondary">
                <i class="bi bi-credit-card fs-5"></i>
                <div class="small">Pembayaran</div>
            </a>
        @endauth

        @auth
            <a href="{{ route('home.riwayat-pesanan') }}"
                class="text-center text-decoration-none {{ Route::currentRouteName() == 'home.riwayat-pesanan' ? 'text-custom' : 'text-secondary' }}">
                <i class="bi bi-receipt fs-5"></i>
                <div class="small">Pesanan</div>
            </a>
        @else
            <a href="#" onclick="showLoginAlert()" class="text-center text-decoration-none text-secondary">
                <i class="bi bi-receipt fs-5"></i>
                <div class="small">Pesanan</div>
            </a>
        @endauth

        @auth
            <a href="{{ route('pelanggan.profile') }}"
                class="text-center text-decoration-none {{ Route::currentRouteName() == 'pelanggan.profile' ? 'text-custom' : 'text-secondary' }}">
                <i class="bi bi-person fs-5"></i>
                <div class="small">Akun</div>
            </a>
        @else
            <a href="{{ route('login') }}"
                class="text-center text-decoration-none {{ Route::currentRouteName() == 'login' ? 'text-custom' : 'text-secondary' }}">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <div class="small">Login</div>
            </a>
        @endauth
    </div>
</div>

<script>
    function showLoginAlert() {
        Swal.fire({
            title: 'Login Diperlukan',
            text: 'Silakan login terlebih dahulu untuk mengakses fitur ini',
            icon: 'warning',
            confirmButtonText: 'Login',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            }
        });
    }
</script>
