<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');           // dalam Rupiah, tanpa desimal
            $table->string('photo')->nullable();            // path foto menu (storage)
            $table->enum('availability', ['available', 'sold_out'])->default('available');
            $table->timestamps();

            $table->index('availability');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
