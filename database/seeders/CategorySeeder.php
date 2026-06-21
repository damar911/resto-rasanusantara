<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'slug' => 'makanan', 'sort_order' => 1],
            ['name' => 'Minuman', 'slug' => 'minuman', 'sort_order' => 2],
            ['name' => 'Cemilan', 'slug' => 'cemilan', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
