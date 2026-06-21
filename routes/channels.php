<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Channel "order.{orderId}" dan "kitchen-dashboard" bersifat publik
| (tidak butuh login) karena customer men-tracking pesanan tanpa akun.
| Jika ingin lebih aman, bisa diubah jadi private channel dengan
| menambahkan token unik per order.
|
*/

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    // Channel publik: siapapun yang tahu order ID bisa subscribe
    // (order ID didapat dari URL tracking yang di-share ke customer)
    return true;
});

Broadcast::channel('kitchen-dashboard', function ($user) {
    // Private channel: hanya admin/operator yang login boleh subscribe ke dashboard dapur
    return $user && in_array($user->role, ['admin_dapur', 'operator'], true)
        ? ['id' => $user->id, 'name' => $user->name, 'role' => $user->role]
        : false;
});
