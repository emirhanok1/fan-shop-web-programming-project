<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Posterler',
                'slug' => 'posterler',
                'description' => 'Film ve dizilere ait en şık duvar posterleri.',
            ],
            [
                'name' => 'Figürler & Koleksiyon',
                'slug' => 'figurler-koleksiyon',
                'description' => 'Özel üretim lisanslı karakter figürleri.',
            ],
            [
                'name' => 'Giyim',
                'slug' => 'giyim',
                'description' => 'Tişörtler, hoodie\'ler ve lisanslı giyim ürünleri.',
            ],
            [
                'name' => 'Mutfak & Aksesuar',
                'slug' => 'mutfak-aksesuar',
                'description' => 'Kupalar, bardaklar ve mutfak aksesuarları.',
            ],
            [
                'name' => 'Çanta & Cüzdan',
                'slug' => 'canta-cuzdanlar',
                'description' => 'Lisanslı sırt çantaları ve şık cüzdan modelleri.',
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }
    }
}
