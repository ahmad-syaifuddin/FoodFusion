<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\VoucherController; // Tambah ini
use App\Http\Controllers\PaymentController; // Tambah ini
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PesananController;
use App\Models\KategoriProduk;
use App\Models\Produk;
use PHPUnit\Framework\Attributes\Group;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//TODO: php artisan route:clear

//! Route untuk halaman utama (market)
Route::get('/', [MarketController::class, 'index'])->middleware('cegah.admin.akses.pelanggan')->name('home.index');
Route::get('/search', [MarketController::class, 'search'])->name('home.search');

// Route::get('/', [MarketController::class, 'index'])->name('home.index');
// Route::get('/cart', [MarketController::class, 'cart'])->name('market.cart');
// Route::get('/wishlist', [MarketController::class, 'wishlist'])->name('market.wishlist');
// Route::get('/profile', [MarketController::class, 'profile'])->name('profile');
// Route::get('/orders', [MarketController::class, 'orderHistory'])->name('market.orders');

// Market routes
Route::get('/produk/{slug}', [MarketController::class, 'detailProduk'])->name('produk.detail');
Route::get('/kategori/{slug}', [MarketController::class, 'index'])->name('market.kategori');

// Wishlist routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

//! Route untuk guest (belum login) untuk pelanggan
Route::middleware('guest')->group(function () {
    Route::get('/login', [PelangganController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PelangganController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

//? Route Pelanggan
//! Route untuk pelanggan yang sudah login
Route::middleware(['auth', 'pelanggan'])->group(function () {  // Tambah middleware pelanggan
    Route::post('/logout', [PelangganController::class, 'logout'])->name('logout');

    Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/order/{id}/confirm-receipt', [OrderController::class, 'confirmReceipt'])->name('orders.confirm-receipt');
    Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cart}/quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Voucher routes
    Route::post('/vouchers/validate', [CheckoutController::class, 'validateVoucher'])->name('vouchers.validate');

    // Payment Routes
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');

    // Address Routes
    Route::get('/addresses/list', [AddressController::class, 'getList'])->name('addresses.list');
    Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('addresses.show');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::match(['PUT', 'PATCH'], '/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    Route::prefix('pelanggan')->name('pelanggan.')->group(function () {

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::middleware('auth')->group(function () {

    // Address Management Routes
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::get('/addresses/{address}', [AddressController::class, 'show']);
    Route::match(['PUT', 'PATCH'], '/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
});

Route::get('/cart/get-selected-items', [CartController::class, 'getSelectedItems'])
    ->name('cart.get-selected-items');



//! Route Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminController::class, 'login'])->name('login');
        Route::post('/login', [AdminController::class, 'authenticate'])->name('authenticate');
        // Redirect guest to admin login
        Route::get('/dashboard', function () {
            return redirect()->route('admin.login');
        });
    });

    // Admin routes
    Route::middleware(['auth', 'adminOnly'])->group(function () {
        Route::get('/beranda', [AdminController::class, 'index'])->name('dashboard'); // Ini akan menjadi admin.dashboard
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/pengguna/profile', [AdminController::class, 'profilePengguna'])->name('pengguna.profile'); // Halaman profile pengguna

        Route::prefix('')->group(function () {  // Tambahkan grup kosong untuk memastikan prefix nama benar

            Route::get('/pengguna', [PenggunaController::class, 'indexPengguna'])->name('pengguna.index'); // Halaman daftar pengguna
            Route::get('/pengguna/create', [PenggunaController::class, 'createPengguna'])->name('pengguna.create'); // Halaman form tambah pengguna
            Route::post('/pengguna', [PenggunaController::class, 'storePengguna'])->name('pengguna.store'); // Proses tambah pengguna
            Route::get('/pengguna/{id}/edit', [PenggunaController::class, 'editPengguna'])->name('pengguna.edit'); // Halaman form edit pengguna
            Route::put('/pengguna/{id}', [PenggunaController::class, 'updatePengguna'])->name('pengguna.update'); // Proses edit pengguna
            Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroyPengguna'])->name('pengguna.destroy'); // Proses hapus pengguna

            Route::get('/kategori', [KategoriProdukController::class, 'indexKategori'])->name('kategori.index'); // Halaman daftar kategori
            Route::get('/kategori/create', [KategoriProdukController::class, 'createKategori'])->name('kategori.create'); // Halaman form tambah kategori
            Route::post('/kategori', [KategoriProdukController::class, 'storeKategori'])->name('kategori.store'); // Proses tambah kategori
            Route::get('/kategori/{id}/edit', [KategoriProdukController::class, 'editKategori'])->name('kategori.edit'); // Halaman form edit kategori
            Route::put('/kategori/{id}', [KategoriProdukController::class, 'updateKategori'])->name('kategori.update'); // Proses edit kategori
            Route::delete('/kategori/{id}', [KategoriProdukController::class, 'destroyKategori'])->name('kategori.destroy'); // Proses hapus kategori

            Route::get('/produk', [ProdukController::class, 'indexProduk'])->name('produk.index'); // Halaman daftar produk
            Route::get('/produk/create', [ProdukController::class, 'createProduk'])->name('produk.create'); // Halaman form tambah produk
            Route::post('/produk', [ProdukController::class, 'storeProduk'])->name('produk.store'); // Proses tambah produk
            Route::get('/produk/{id}/edit', [ProdukController::class, 'editProduk'])->name('produk.edit'); // Halaman form edit produk
            Route::put('/produk/{id}', [ProdukController::class, 'updateProduk'])->name('produk.update'); // Proses edit produk
            Route::delete('/produk/{id}', [ProdukController::class, 'destroyProduk'])->name('produk.destroy'); // Proses hapus produk

            Route::get('/pesanan', [PesananController::class, 'indexPesanan'])->name('pesanan.index'); // Halaman daftar produk
            Route::get('/pesanan/{order_number}', [PesananController::class, 'showPesanan'])->name('pesanan.show');
            Route::put('/pesanan/{id}/update-status', [OrderController::class, 'updateStatus'])->name('pesanan.updateStatus');
            Route::delete('/pesanan/{id}', [PesananController::class, 'destroyPesanan'])->name('pesanan.destroy');

            // Voucher Management
            Route::resource('vouchers', VoucherController::class);

            Route::post('/vouchers/validate', [CheckoutController::class, 'validateVoucher'])
                ->name('api.vouchers.validate');

            // Route::post('/pesanan/{id}/confirm', [OrderController::class, 'confirm'])->name('pesanan.confirm');
            // Route::post('/pesanan/{id}/process', [OrderController::class, 'processing'])->name('pesanan.process');
            // Route::post('/pesanan/{id}/delivery', [OrderController::class, 'delivery'])->name('pesanan.delivery');
            // Route::post('/pesanan/{id}/complete', [OrderController::class, 'complete'])->name('pesanan.complete');
            Route::patch('/orders/{order}/confirm-cod', [OrderController::class, 'confirmCOD'])->name('orders.confirm-cod');
        });
    });
});