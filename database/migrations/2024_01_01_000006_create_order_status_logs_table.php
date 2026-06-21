<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status');                       // diterima, dimasak, disajikan, dibatalkan
            $table->foreignId('changed_by')->nullable()->constrained('users'); // admin/operator yg ubah
            $table->boolean('whatsapp_sent')->default(false);
            $table->text('whatsapp_response')->nullable();   // log response dari Fonnte API
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
