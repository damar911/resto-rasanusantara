<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::where('slug', 'makanan')->first();
        $minuman = Category::where('slug', 'minuman')->first();
        $cemilan = Category::where('slug', 'cemilan')->first();

        $menus = [
            // Makanan (10)
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Spesial', 'price' => 25000, 'description' => 'Nasi goreng dengan telur, ayam, dan sosis.'],
            ['category_id' => $makanan->id, 'name' => 'Ayam Bakar Madu', 'price' => 28000, 'description' => 'Ayam bakar bumbu madu khas dapur kami.'],
            ['category_id' => $makanan->id, 'name' => 'Soto Ayam Lamongan', 'price' => 22000, 'description' => 'Soto ayam kuah bening dengan koya.'],
            ['category_id' => $makanan->id, 'name' => 'Rendang Daging Sapi', 'price' => 35000, 'description' => 'Rendang khas Padang, empuk dan gurih.'],
            ['category_id' => $makanan->id, 'name' => 'Mie Goreng Jawa', 'price' => 23000, 'description' => 'Mie goreng dengan bumbu rempah khas Jawa.'],
            ['category_id' => $makanan->id, 'name' => 'Gudeg Jogja', 'price' => 27000, 'description' => 'Gudeg nangka khas Yogyakarta dengan ayam kampung.'],
            ['category_id' => $makanan->id, 'name' => 'Sate Ayam Madura', 'price' => 24000, 'description' => '10 tusuk sate ayam dengan bumbu kacang.'],
            ['category_id' => $makanan->id, 'name' => 'Ikan Bakar Sambal Dabu', 'price' => 32000, 'description' => 'Ikan bakar dengan sambal dabu-dabu segar.'],
            ['category_id' => $makanan->id, 'name' => 'Bebek Goreng Kremes', 'price' => 33000, 'description' => 'Bebek goreng renyah dengan kremesan gurih.'],
            ['category_id' => $makanan->id, 'name' => 'Nasi Liwet Komplit', 'price' => 26000, 'description' => 'Nasi liwet dengan ayam suwir dan sambal goreng.'],

            // Minuman (15)
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis', 'price' => 5000, 'description' => 'Teh manis dingin segar.'],
            ['category_id' => $minuman->id, 'name' => 'Es Jeruk', 'price' => 7000, 'description' => 'Perasan jeruk segar dengan es.'],
            ['category_id' => $minuman->id, 'name' => 'Kopi Hitam', 'price' => 8000, 'description' => 'Kopi robusta khas nusantara.'],
            ['category_id' => $minuman->id, 'name' => 'Es Kopi Susu Gula Aren', 'price' => 15000, 'description' => 'Kopi susu kekinian dengan gula aren asli.'],
            ['category_id' => $minuman->id, 'name' => 'Wedang Jahe', 'price' => 9000, 'description' => 'Wedang jahe hangat untuk menghangatkan badan.'],
            ['category_id' => $minuman->id, 'name' => 'Es Cincau Susu', 'price' => 12000, 'description' => 'Cincau hitam dengan susu segar.'],
            ['category_id' => $minuman->id, 'name' => 'Air Mineral', 'price' => 4000, 'description' => 'Air mineral kemasan botol 600ml.'],
            ['category_id' => $minuman->id, 'name' => 'Es Teh Tarik', 'price' => 10000, 'description' => 'Teh tarik creamy ala kedai.'],
            ['category_id' => $minuman->id, 'name' => 'Jus Alpukat', 'price' => 14000, 'description' => 'Jus alpukat kental dengan susu coklat.'],
            ['category_id' => $minuman->id, 'name' => 'Es Buah Segar', 'price' => 13000, 'description' => 'Campuran buah segar dengan sirup dan susu.'],
            ['category_id' => $minuman->id, 'name' => 'Teh Tawar Hangat', 'price' => 4000, 'description' => 'Teh tawar hangat tanpa gula.'],
            ['category_id' => $minuman->id, 'name' => 'Susu Coklat Dingin', 'price' => 11000, 'description' => 'Susu coklat segar dengan es batu.'],
            ['category_id' => $minuman->id, 'name' => 'Lemon Tea', 'price' => 9000, 'description' => 'Teh dengan perasan lemon segar.'],
            ['category_id' => $minuman->id, 'name' => 'Es Kelapa Muda', 'price' => 12000, 'description' => 'Kelapa muda segar dengan sirup gula merah.'],
            ['category_id' => $minuman->id, 'name' => 'Kopi Susu Original', 'price' => 13000, 'description' => 'Kopi susu klasik dengan rasa seimbang.'],

            // Cemilan (20)
            ['category_id' => $cemilan->id, 'name' => 'Kerupuk Udang', 'price' => 5000, 'description' => 'Kerupuk udang renyah.'],
            ['category_id' => $cemilan->id, 'name' => 'Tahu Isi', 'price' => 8000, 'description' => 'Tahu isi sayuran, 5 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Bakwan Jagung', 'price' => 7000, 'description' => 'Bakwan jagung renyah, 5 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Pisang Goreng Crispy', 'price' => 10000, 'description' => 'Pisang goreng tepung crispy dengan topping coklat.'],
            ['category_id' => $cemilan->id, 'name' => 'Cireng Bumbu Rujak', 'price' => 9000, 'description' => 'Cireng kriuk dengan bumbu rujak pedas manis.'],
            ['category_id' => $cemilan->id, 'name' => 'Tempe Mendoan', 'price' => 8000, 'description' => 'Tempe mendoan khas Banyumas, 5 lembar.'],
            ['category_id' => $cemilan->id, 'name' => 'Risoles Mayo', 'price' => 12000, 'description' => 'Risoles isi smoked beef dan mayo, 5 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Sosis Bakar', 'price' => 11000, 'description' => 'Sosis bakar saus barbeque, 5 tusuk.'],
            ['category_id' => $cemilan->id, 'name' => 'Kentang Goreng', 'price' => 13000, 'description' => 'Kentang goreng renyah dengan saus sambal/mayo.'],
            ['category_id' => $cemilan->id, 'name' => 'Roti Bakar Coklat Keju', 'price' => 14000, 'description' => 'Roti bakar dengan topping coklat dan keju leleh.'],
            ['category_id' => $cemilan->id, 'name' => 'Siomay Bandung', 'price' => 15000, 'description' => 'Siomay ikan dengan bumbu kacang, 6 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Batagor', 'price' => 15000, 'description' => 'Batagor goreng dengan saus kacang khas Bandung.'],
            ['category_id' => $cemilan->id, 'name' => 'Martabak Mini Manis', 'price' => 12000, 'description' => 'Martabak mini topping coklat keju kacang.'],
            ['category_id' => $cemilan->id, 'name' => 'Pukis Coklat', 'price' => 10000, 'description' => 'Pukis lembut dengan taburan coklat, 5 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Lumpia Semarang', 'price' => 11000, 'description' => 'Lumpia isi rebung dan ayam, 3 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Otak-Otak Bakar', 'price' => 13000, 'description' => 'Otak-otak ikan bakar dengan saus kacang.'],
            ['category_id' => $cemilan->id, 'name' => 'Klepon', 'price' => 8000, 'description' => 'Klepon isi gula merah dengan kelapa parut, 8 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Combro', 'price' => 7000, 'description' => 'Combro isi oncom pedas, 3 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Misro', 'price' => 7000, 'description' => 'Misro isi gula merah manis, 3 buah.'],
            ['category_id' => $cemilan->id, 'name' => 'Keripik Singkong Balado', 'price' => 9000, 'description' => 'Keripik singkong dengan bumbu balado pedas.'],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name']],
                array_merge($menu, ['availability' => 'available'])
            );
        }
    }
}
