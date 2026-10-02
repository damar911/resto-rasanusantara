<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum menjadi string supaya bisa menampung nilai baru
        // (payment_method: midtrans, payment_status: paid/failed/expired)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('midtrans')->change();
            $table->string('payment_status')->default('pending')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->unique()->after('payment_status');
            $table->text('snap_token')->nullable()->after('midtrans_order_id');
            $table->string('payment_type')->nullable()->after('snap_token');
            $table->timestamp('paid_at')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['midtrans_order_id']);
            $table->dropColumn(['midtrans_order_id', 'snap_token', 'payment_type', 'paid_at']);
        });
    }
};
