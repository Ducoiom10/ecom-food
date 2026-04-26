<?php

use Illuminate\Support\Facades\Route;

// ===================== AUTH ROUTES =====================
Route::get('/login',           fn() => view('auth.login'))->name('login');
Route::post('/login',          fn() => redirect()->route('client.home'))->name('login.post');
Route::get('/register',        fn() => view('auth.register'))->name('register');
Route::post('/register',       fn() => redirect()->route('client.home'))->name('register.post');
Route::post('/logout',         fn() => redirect()->route('login'))->name('logout');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('auth.forgot');
Route::post('/forgot-password',fn() => back()->with('sent', true))->name('auth.forgot.post');

// ===================== CLIENT ROUTES =====================
Route::prefix('')->name('client.')->group(function () {
    Route::get('/',                         fn() => view('client.home', [
        'menuItems' => array_values(array_filter(\App\Data\MockData::menuItems(), fn($i) => true)),
        'combos'    => \App\Data\MockData::combos(),
        'reviews'   => \App\Data\MockData::reviews(),
    ]))->name('home');
    Route::get('/menu',                     fn() => view('client.menu', [
        'menuItems' => \App\Data\MockData::menuItems(),
    ]))->name('menu');
    Route::get('/cart',                     fn() => view('client.cart', [
        'cartItems' => \App\Data\MockData::cartItems(),
        'subtotal'  => 125000,
    ]))->name('cart');
    Route::post('/cart/add',                fn() => response()->json(['ok' => true]))->name('cart.add');
    Route::get('/profile',                  fn() => view('client.profile', [
        'user'         => ['name' => 'Minh Tuấn', 'email' => 'minhtuan@email.com'],
        'snackPoints'  => 342,
        'orderHistory' => \App\Data\MockData::orderHistory(),
    ]))->name('profile');
    Route::get('/profile/edit',             fn() => view('auth.profile-edit', [
        'user' => ['name' => 'Minh Tuấn', 'email' => 'minhtuan@email.com', 'phone' => '0901234567'],
    ]))->name('profile.edit');
    Route::post('/profile/edit',            fn() => redirect()->route('client.profile'))->name('profile.update');
    Route::get('/profile/password',         fn() => view('auth.change-password'))->name('profile.password');
    Route::post('/profile/password',        fn() => redirect()->route('client.profile'))->name('profile.password.update');
    Route::get('/product/{id}',             fn($id) => view('client.product', [
        'product' => \App\Data\MockData::product($id),
    ]))->name('product');
    Route::get('/checkout',                 fn() => view('client.cart', [
        'cartItems' => \App\Data\MockData::cartItems(),
        'subtotal'  => 125000,
    ]))->name('checkout');
    Route::post('/checkout',                fn() => redirect()->route('client.home'))->name('checkout.post');

    // Group Order
    Route::get('/group-order',              fn() => view('client.group-order', ['roomCode' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6) . rand(100, 999))]))->name('group-order');
    Route::post('/group-order',             fn() => redirect()->route('client.group-order.room', request('room_code')))->name('group-order.create');
    Route::get('/group-order/join',         fn() => redirect()->route('client.group-order.room', strtoupper(request('code'))))->name('group-order.join');
    Route::get('/group-order/{code}',       fn($code) => view('client.group-order-room', ['room' => ['code' => $code, 'participants' => [], 'orders' => [], 'activities' => [], 'isLocked' => false], 'menuItems' => [], 'myItems' => [], 'myItemCount' => 0, 'myTotal' => 0, 'grandTotal' => 0, 'isHost' => true, 'menuPrices' => [], 'menuNames' => []]))->name('group-order.room');
    Route::post('/group-order/{code}/item', fn($code) => back())->name('group-order.item');
    Route::post('/group-order/{code}/lock', fn($code) => redirect()->route('client.split-bill', $code))->name('group-order.lock');

    // Split Bill
    Route::get('/split-bill/{code}',        fn($code) => view('client.split-bill', ['room' => ['code' => $code], 'bills' => [], 'grandTotal' => 0]))->name('split-bill');
});

// ===================== ADMIN ROUTES =====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/',         fn() => redirect()->route('admin.kds'));

    Route::get('/kds',      fn() => view('admin.kds',        ['orders' => [], 'inventory' => []]))->name('kds');
    Route::post('/kds/{id}/move',           fn($id) => back())->name('kds.move');
    Route::patch('/kds/inventory/{id}',     fn($id) => back())->name('kds.inventory');

    Route::get('/smart-prep',               fn() => view('admin.smart-prep', ['recommendations' => [], 'weather' => ['icon' => '🌧️', 'temp' => 24, 'label' => 'Mưa lớn', 'impact' => 'Mưa lớn → Đơn ship tăng 85%!', 'deliveryBoost' => 85], 'mealPeriod' => ['name' => 'Bữa trưa', 'emoji' => '☀️', 'peak' => true], 'currentWeather' => 'rainy', 'criticalCount' => 0, 'highCount' => 0, 'pendingRecs' => 0, 'acknowledgedRecs' => 0]))->name('smartprep');
    Route::post('/smart-prep/{id}/ack',     fn($id) => back())->name('smartprep.acknowledge');

    Route::get('/dispatch',                 fn() => view('admin.dispatch', ['orders' => [], 'shippers' => [], 'activeCount' => 0, 'freeShippers' => 0, 'todayCount' => 47, 'batchableCount' => 0]))->name('dispatch');
    Route::patch('/dispatch/{id}',          fn($id) => back())->name('dispatch.update');
    Route::post('/dispatch/assign',         fn() => back())->name('dispatch.assign');

    Route::get('/branch',                   fn() => view('admin.branch', ['branches' => [], 'selectedBranch' => ['id' => 'b1', 'name' => 'Chi nhánh Quận 1', 'revenue' => 15600000, 'orders' => 234, 'rating' => 4.8], 'hourlyData' => [], 'lowStock' => [], 'refunds' => []]))->name('branch');
    Route::patch('/branch/refund/{id}',     fn($id) => back())->name('branch.refund');

    Route::get('/super',                    fn() => view('admin.super', ['activeTab' => request('tab', 'analytics'), 'totalRevenue' => 115400000, 'revenueData' => [['day'=>'T2','revenue'=>12500000],['day'=>'T3','revenue'=>14200000],['day'=>'T4','revenue'=>11800000],['day'=>'T5','revenue'=>16500000],['day'=>'T6','revenue'=>18900000],['day'=>'T7','revenue'=>22000000],['day'=>'CN','revenue'=>19500000]], 'branches' => [], 'vouchers' => [], 'roles' => ['Super Admin','Branch Manager','Coordinator','Kitchen Staff','Support'], 'permissions' => [['key'=>'view_revenue','label'=>'Xem doanh thu'],['key'=>'manage_menu','label'=>'Quản lý thực đơn'],['key'=>'manage_vouchers','label'=>'Quản lý voucher'],['key'=>'view_orders','label'=>'Xem đơn hàng'],['key'=>'update_orders','label'=>'Cập nhật đơn hàng'],['key'=>'manage_staff','label'=>'Quản lý nhân viên'],['key'=>'refund_orders','label'=>'Hoàn tiền'],['key'=>'view_audit_log','label'=>'Xem audit log']], 'rolePerms' => ['Super Admin'=>['view_revenue','manage_menu','manage_vouchers','view_orders','update_orders','manage_staff','refund_orders','view_audit_log'],'Branch Manager'=>['view_revenue','manage_menu','view_orders','update_orders','refund_orders'],'Coordinator'=>['view_orders','update_orders'],'Kitchen Staff'=>['view_orders','update_orders'],'Support'=>['view_orders','refund_orders']], 'auditLogs' => [['time'=>'13:45:22','user'=>'admin@baanh.vn','action'=>'UPDATE','target'=>'orders/ORD-201','detail'=>'Status: pending → completed','ip'=>'192.168.1.10'],['time'=>'13:40:15','user'=>'manager@baanh.vn','action'=>'UPDATE','target'=>'vouchers/SALE50','detail'=>'Discount: 30% → 50%','ip'=>'192.168.1.25'],['time'=>'13:35:08','user'=>'admin@baanh.vn','action'=>'DELETE','target'=>'menu/item-99','detail'=>'Xoá sản phẩm hết hạn','ip'=>'192.168.1.10']]]))->name('super');
    Route::post('/super/push',              fn() => back())->name('super.push');
    Route::patch('/super/perm',             fn() => back())->name('super.perm');
});
