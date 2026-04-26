<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\GroupOrderController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\MenuController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DispatchController;
use App\Http\Controllers\Admin\KdsController;
use App\Http\Controllers\Admin\SmartPrepController;
use App\Http\Controllers\Admin\SuperAdminController;
use Illuminate\Support\Facades\Route;

// ===================== AUTH =====================
Route::middleware('guest')->group(function () {
    Route::get('/login',            [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',           [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',         [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',        [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',  [AuthController::class, 'showForgotPassword'])->name('auth.forgot');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('auth.forgot.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===================== CLIENT =====================
Route::prefix('')->name('client.')->group(function () {

    // Public
    Route::get('/',             [HomeController::class,   'index'])->name('home');
    Route::get('/menu',         [MenuController::class,   'index'])->name('menu');
    Route::get('/product/{id}', [ProductController::class,'show'])->name('product');

    // Auth required
    Route::middleware('auth')->group(function () {

        // Cart
        Route::get('/cart',          [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add',     [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{id}',   [CartController::class, 'updateQty'])->name('cart.update');
        Route::delete('/cart/{id}',  [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/voucher', [CartController::class, 'applyVoucher'])->name('cart.voucher');
        Route::get('/checkout',      [CartController::class, 'checkout'])->name('checkout');
        Route::post('/checkout',     [CartController::class, 'placeOrder'])->name('checkout.post');

        // Profile
        Route::get('/profile',           [ProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit',      [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit',     [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/password',  [ProfileController::class, 'showChangePassword'])->name('profile.password');
        Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password.update');

        // Orders
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('order.show');

        // Group Order
        Route::get('/group-order',              [GroupOrderController::class, 'index'])->name('group-order');
        Route::post('/group-order',             [GroupOrderController::class, 'create'])->name('group-order.create');
        Route::get('/group-order/join',         [GroupOrderController::class, 'join'])->name('group-order.join');
        Route::get('/group-order/{code}',       [GroupOrderController::class, 'room'])->name('group-order.room');
        Route::post('/group-order/{code}/item', [GroupOrderController::class, 'addItem'])->name('group-order.item');
        Route::post('/group-order/{code}/lock', [GroupOrderController::class, 'lock'])->name('group-order.lock');

        // Split Bill
        Route::get('/split-bill/{code}', [GroupOrderController::class, 'splitBill'])->name('split-bill');
    });
});

// ===================== ADMIN =====================
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'role:super_admin,branch_manager,coordinator,kitchen_staff,support'])
    ->group(function () {

    Route::get('/', fn() => redirect()->route('admin.kds'));

    // KDS — kitchen_staff trở lên
    Route::get('/kds',                  [KdsController::class, 'index'])->name('kds');
    Route::post('/kds/{id}/move',       [KdsController::class, 'move'])->name('kds.move');
    Route::patch('/kds/inventory/{id}', [KdsController::class, 'updateInventory'])->name('kds.inventory');

    // Smart Prep
    Route::get('/smart-prep',           [SmartPrepController::class, 'index'])->name('smartprep');
    Route::post('/smart-prep/{id}/ack', [SmartPrepController::class, 'acknowledge'])->name('smartprep.acknowledge');

    // Dispatch — coordinator trở lên
    Route::get('/dispatch',             [DispatchController::class, 'index'])->name('dispatch');
    Route::patch('/dispatch/{id}',      [DispatchController::class, 'update'])->name('dispatch.update');
    Route::post('/dispatch/assign',     [DispatchController::class, 'assign'])->name('dispatch.assign');

    // Branch — branch_manager trở lên
    Route::get('/branch',               [BranchController::class, 'index'])->name('branch');
    Route::patch('/branch/refund/{id}', [BranchController::class, 'refund'])->name('branch.refund');

    // Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/super',        [SuperAdminController::class, 'index'])->name('super');
        Route::post('/super/push',  [SuperAdminController::class, 'sendPush'])->name('super.push');
        Route::patch('/super/perm', [SuperAdminController::class, 'updatePerm'])->name('super.perm');
    });
});
