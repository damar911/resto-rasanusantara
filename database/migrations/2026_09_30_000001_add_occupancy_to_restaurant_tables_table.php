<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->boolean('is_occupied')->default(false)->after('is_active');
            $table->timestamp('occupied_at')->nullable()->after('is_occupied');
            $table->string('occupant_name')->nullable()->after('occupied_at');
            $table->string('occupant_whatsapp')->nullable()->after('occupant_name');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumn(['is_occupied', 'occupied_at', 'occupant_name', 'occupant_whatsapp']);
        });
    }
};
