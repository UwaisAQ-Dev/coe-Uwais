<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // 1. Cipta Test User (jika belum ada)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // 2. Cipta Categories dan simpan ke dalam variable
        $catLaptop = Category::create([
            'name' => 'Laptop & Komputer',
            'slug' => 'laptop-komputer',
            'description' => 'Peranti komputer riba dan sistem desktop berprestasi tinggi.',
        ]);
        $catAksesori = Category::create([
            'name' => 'Aksesori Komputer',
            'slug' => 'aksesori-komputer',
            'description' => 'Peralatan sokongan seperti tetikus, papan kekunci, dan fon kepala.',
        ]);
        // 3. Cipta Products dan link-kan category_id
        Product::create([
            'name' => 'Laptop Gaming Pro 15',
            'price' => 4500.00,
            'description' => 'Laptop berprestasi tinggi untuk pembangunan perisian dan permainan video.',
            'stock' => 10,
            'image_url' => 'https://picsum.photos/seed/laptop/400/300',
            'category_id' => $catLaptop->id, // <-- Link ke kategori Laptop
        ]);
        Product::create([
            'name' => 'Tetikus Tanpa Wayar Ergonomik',
            'price' => 129.90,
            'description' => 'Tetikus tanpa wayar ergonomik yang menawarkan keselesaan penggunaan harian.',
            'stock' => 50,
            'image_url' => 'https://picsum.photos/seed/mouse/400/300',
            'category_id' => $catAksesori->id, // <-- Link ke kategori Aksesori
        ]);
        Product::create([
            'name' => 'Papan Kekunci Mekanikal RGB',
            'price' => 299.99,
            'description' => 'Papan kekunci mekanikal dengan pencahayaan RGB responsif.',
            'stock' => 30,
            'image_url' => 'https://picsum.photos/seed/keyboard/400/300',
            'category_id' => $catAksesori->id, // <-- Link ke kategori Aksesori
        ]);
    }
}
