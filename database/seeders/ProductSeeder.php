<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load category IDs by their slug to ensure correctness
        $categories = Category::all()->pluck('id', 'slug')->toArray();

        $products = [
            // Breaking Bad
            [
                'name' => 'Breaking Bad - Heisenberg Poster',
                'category_slug' => 'posterler',
                'price' => 299.00,
                'stock' => 25,
                'franchise' => 'Breaking Bad',
                'tmdb_id' => 1396,
                'tmdb_type' => 'tv',
                'description' => 'Efsanevi Heisenberg posteri, yüksek kaliteli kuşe kağıda baskı.',
            ],
            [
                'name' => 'Breaking Bad - Walter White Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 950.00,
                'stock' => 15,
                'franchise' => 'Breaking Bad',
                'tmdb_id' => 1396,
                'tmdb_type' => 'tv',
                'description' => 'Detaylı işçiliğe sahip 15cm boyunda Walter White koleksiyon figürü.',
            ],
            [
                'name' => 'Breaking Bad - I Am The Danger Kupa',
                'category_slug' => 'mutfak-aksesuar',
                'price' => 280.00,
                'stock' => 30,
                'franchise' => 'Breaking Bad',
                'tmdb_id' => 1396,
                'tmdb_type' => 'tv',
                'description' => '"I am the one who knocks" repliği yazılı seramik kupa.',
            ],
            // Game of Thrones
            [
                'name' => 'Game of Thrones - Iron Throne Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 1200.00,
                'stock' => 10,
                'franchise' => 'Game of Thrones',
                'tmdb_id' => 1399,
                'tmdb_type' => 'tv',
                'description' => '1:12 ölçekli mini Demir Taht koleksiyon figürü.',
            ],
            [
                'name' => 'Game of Thrones - Stark Arması Poster',
                'category_slug' => 'posterler',
                'price' => 275.00,
                'stock' => 30,
                'franchise' => 'Game of Thrones',
                'tmdb_id' => 1399,
                'tmdb_type' => 'tv',
                'description' => '"Winter is Coming" yazılı Stark kurdu armalı poster.',
            ],
            [
                'name' => 'Game of Thrones - Lannister Hoodie',
                'category_slug' => 'giyim',
                'price' => 780.00,
                'stock' => 20,
                'franchise' => 'Game of Thrones',
                'tmdb_id' => 1399,
                'tmdb_type' => 'tv',
                'description' => 'Lannister Aslanı işlemeli içi polarlı, %100 pamuklu sweatshirt.',
            ],
            // Vikings
            [
                'name' => 'Vikings - Ragnar Lothbrok Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 1100.00,
                'stock' => 12,
                'franchise' => 'Vikings',
                'tmdb_id' => 44217,
                'tmdb_type' => 'tv',
                'description' => 'Ragnar Lothbrok balta ve kalkanlı detaylı koleksiyon figürü.',
            ],
            [
                'name' => 'Vikings - Runik Sembol Poster',
                'category_slug' => 'posterler',
                'price' => 260.00,
                'stock' => 35,
                'franchise' => 'Vikings',
                'tmdb_id' => 44217,
                'tmdb_type' => 'tv',
                'description' => 'Viking runik alfabeleri ve sembolleri detaylı eskitme poster.',
            ],
            [
                'name' => 'Vikings - Kattegat Baskılı Tişört',
                'category_slug' => 'giyim',
                'price' => 450.00,
                'stock' => 25,
                'franchise' => 'Vikings',
                'tmdb_id' => 44217,
                'tmdb_type' => 'tv',
                'description' => 'Kattegat limanı temalı pamuklu bisiklet yaka tişört.',
            ],
            // The Godfather
            [
                'name' => 'The Godfather - Corleone Ailesi Poster',
                'category_slug' => 'posterler',
                'price' => 349.00,
                'stock' => 20,
                'franchise' => 'The Godfather',
                'tmdb_id' => 238,
                'tmdb_type' => 'movie',
                'description' => 'Don Vito Corleone ve ailesinin ikonik monokrom afişi.',
            ],
            [
                'name' => 'The Godfather - Make An Offer Kupa',
                'category_slug' => 'mutfak-aksesuar',
                'price' => 290.00,
                'stock' => 30,
                'franchise' => 'The Godfather',
                'tmdb_id' => 238,
                'tmdb_type' => 'movie',
                'description' => '"I\'m gonna make him an offer he can\'t refuse" yazılı kupa.',
            ],
            // The Dark Knight
            [
                'name' => 'The Dark Knight - Joker Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 1350.00,
                'stock' => 10,
                'franchise' => 'The Dark Knight',
                'tmdb_id' => 155,
                'tmdb_type' => 'movie',
                'description' => 'Heath Ledger Joker canlandırması, 18cm hareketli figür.',
            ],
            [
                'name' => 'The Dark Knight - Batman Maskesi Poster',
                'category_slug' => 'posterler',
                'price' => 325.00,
                'stock' => 25,
                'franchise' => 'The Dark Knight',
                'tmdb_id' => 155,
                'tmdb_type' => 'movie',
                'description' => 'Batman maskesi ve Gotham şehir silüeti temalı poster.',
            ],
            [
                'name' => 'The Dark Knight - Why So Serious Sırt Çantası',
                'category_slug' => 'canta-cuzdanlar',
                'price' => 1100.00,
                'stock' => 15,
                'franchise' => 'The Dark Knight',
                'tmdb_id' => 155,
                'tmdb_type' => 'movie',
                'description' => 'Joker temalı, çok bölmeli, kaliteli sırt çantası.',
            ],
            // Scarface
            [
                'name' => 'Scarface - Tony Montana Poster',
                'category_slug' => 'posterler',
                'price' => 299.00,
                'stock' => 25,
                'franchise' => 'Scarface',
                'tmdb_id' => 111,
                'tmdb_type' => 'movie',
                'description' => 'Tony Montana tahtında otururken tasvir edilen efsanevi afiş.',
            ],
            [
                'name' => 'Scarface - The World Is Yours Kupa',
                'category_slug' => 'mutfak-aksesuar',
                'price' => 270.00,
                'stock' => 30,
                'franchise' => 'Scarface',
                'tmdb_id' => 111,
                'tmdb_type' => 'movie',
                'description' => 'İkonik "The World Is Yours" heykeli tasarımlı seramik kupa.',
            ],
            // The Punisher
            [
                'name' => 'The Punisher - Skull Logo Tişört',
                'category_slug' => 'giyim',
                'price' => 480.00,
                'stock' => 25,
                'franchise' => 'The Punisher',
                'tmdb_id' => 67178,
                'tmdb_type' => 'tv',
                'description' => 'Karanlıkta parlayan Punisher kuru kafa logolu tişört.',
            ],
            [
                'name' => 'The Punisher - Frank Castle Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 950.00,
                'stock' => 15,
                'franchise' => 'The Punisher',
                'tmdb_id' => 67178,
                'tmdb_type' => 'tv',
                'description' => 'Frank Castle savaş yelekli detaylı figürü.',
            ],
            // The Boys
            [
                'name' => 'The Boys - Homelander Figürü',
                'category_slug' => 'figurler-koleksiyon',
                'price' => 1250.00,
                'stock' => 12,
                'franchise' => 'The Boys',
                'tmdb_id' => 76479,
                'tmdb_type' => 'tv',
                'description' => 'Homelander pelerinli, detaylı stantlı figür.',
            ],
            [
                'name' => 'The Boys - Vought International Hoodie',
                'category_slug' => 'giyim',
                'price' => 820.00,
                'stock' => 20,
                'franchise' => 'The Boys',
                'tmdb_id' => 76479,
                'tmdb_type' => 'tv',
                'description' => 'Vought International logolu gri kapüşonlu hoodie.',
            ],
        ];

        foreach ($products as $prodData) {
            $categorySlug = $prodData['category_slug'];
            unset($prodData['category_slug']);
            
            $prodData['category_id'] = $categories[$categorySlug];
            $prodData['slug'] = Str::slug($prodData['name']);
            $prodData['is_active'] = true;

            $product = Product::updateOrCreate(
                ['slug' => $prodData['slug']],
                $prodData
            );

            // Seed a primary image path for this product
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'is_primary' => true
                ],
                [
                    'image_path' => 'images/products/' . $product->slug . '.webp'
                ]
            );
        }
    }
}
