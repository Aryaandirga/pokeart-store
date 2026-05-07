<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Laravel\Fortify\Features;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\PokewalletController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

Route::post('/checkout/sync-sale', [CheckoutController::class, 'syncSale'])->name('checkout.sync-sale')->middleware('auth');
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
Route::get('/api/pokewallet/search', [PokewalletController::class, 'search'])->name('pokewallet.search');
Route::get('/api/pokewallet/images/{id}', [PokewalletController::class, 'image'])->name('pokewallet.image');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Midtrans webhook (tidak perlu auth)
Route::post('/midtrans/callback', [CheckoutController::class, 'callback'])
    ->name('midtrans.callback');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    Route::post('/products/{product}/images', [ProductController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('my.profile');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('my.profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('my.profile.avatar');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');        // ← tambah ini
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/shipping/areas', [ShippingController::class, 'searchArea'])->name('shipping.areas');
    Route::post('/shipping/rates', [ShippingController::class, 'getRates'])->name('shipping.rates');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/orders', [MyOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [MyOrderController::class, 'show'])->name('orders.show');
});


require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';