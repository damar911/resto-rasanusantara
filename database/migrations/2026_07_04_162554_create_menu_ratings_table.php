<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1-5 bintang
            $table->string('reviewer_name')->nullable(); // nama pemesan (opsional)
            $table->string('session_id')->nullable();    // cegah rating berulang dari device sama
            $table->timestamps();

            $table->index(['menu_id', 'rating']);
        });

        // Tambah kolom rata-rata rating ke tabel menus untuk performa query
        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('avg_rating', 3, 2)->default(0)->after('availability');
            $table->unsignedInteger('rating_count')->default(0)->after('avg_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_ratings');
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['avg_rating', 'rating_count']);
        });
    }
};
