<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            RestaurantTable::firstOrCreate(
                ['number' => $i],
                ['label' => "Meja {$i}", 'is_active' => true]
            );
        }
    }
}
