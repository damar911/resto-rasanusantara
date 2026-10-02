<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuManagementController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', [MenuController::class, 'index'])->name('customer.menu');
Route::get('/tables/availability', [MenuController::class, 'tables'])->name('customer.tables');

Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/track', [OrderController::class, 'trackSearch'])
    ->name('order.track-search');

Route::post('/track', [OrderController::class, 'trackSearchSubmit'])
    ->name('order.track-search.submit');
Route::get('/order/{order}/track', [OrderController::class, 'track'])->name('order.track');
Route::get('/order/{order}/status', [OrderController::class, 'status'])->name('order.status');

// Pembayaran Midtrans
Route::post('/order/{order}/payment-sync', [PaymentController::class, 'sync'])->name('order.payment-sync');
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])->name('midtrans.notification');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Butuh Login)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen antrean - bisa diakses Admin Dapur & Operator
    Route::middleware('role:admin_dapur,operator')->group(function () {
        Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
        Route::patch('/queue/{order}/status', [QueueController::class, 'updateStatus'])->name('queue.update-status');

        // Manajemen meja (kosong / terisi) + QR per meja
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
        Route::get('/tables/status', [TableController::class, 'status'])->name('tables.status');
        Route::get('/tables/qr', [TableController::class, 'qr'])->name('tables.qr');
        Route::patch('/tables/{table}/occupy', [TableController::class, 'occupy'])->name('tables.occupy');
        Route::patch('/tables/{table}/free', [TableController::class, 'free'])->name('tables.free');
    });

    // Manajemen menu & laporan - HANYA Admin Dapur
    Route::middleware('role:admin_dapur')->group(function () {
        Route::get('/menus', [MenuManagementController::class, 'index'])->name('menus.index');
        Route::post('/menus', [MenuManagementController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuManagementController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuManagementController::class, 'destroy'])->name('menus.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});

// Rating menu
Route::post('/menu/{menu}/rating', [\App\Http\Controllers\RatingController::class, 'store'])->name('menu.rating');
