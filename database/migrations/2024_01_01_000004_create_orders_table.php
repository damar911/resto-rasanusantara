<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 10);              // contoh: "00", "01", "02" (reset harian)
            $table->string('customer_name');
            $table->foreignId('table_id')->constrained('restaurant_tables');
            $table->string('whatsapp_number');                // nomor WA aktif pemesan, format 62xxxx
            $table->enum('payment_method', ['transfer', 'qris', 'tunai'])->default('tunai');
            $table->enum('payment_status', ['pending', 'confirmed'])->default('pending');
            $table->unsignedBigInteger('total_amount');
            // Status alur dapur: diterima -> dimasak -> disajikan
            $table->enum('status', ['diterima', 'dimasak', 'disajikan', 'dibatalkan'])->default('diterima');
            $table->integer('estimated_minutes')->default(15); // estimasi waktu selesai
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cooking_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('queue_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
