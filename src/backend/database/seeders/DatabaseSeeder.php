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

        // 2. Cipta 5 Kategori
        $catLaptops = Category::create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops-computers',
            'description' => 'High-performance laptops, workstations, and desktop systems.',
        ]);
        $catPeripherals = Category::create([
            'name' => 'Peripherals & Accessories',
            'slug' => 'peripherals-accessories',
            'description' => 'Keyboards, mice, webcams, and daily computing gear.',
        ]);
        $catAudioVisual = Category::create([
            'name' => 'Audio & Displays',
            'slug' => 'audio-displays',
            'description' => 'Ultra-crisp monitors, studio headphones, and speakers.',
        ]);
        $catStorage = Category::create([
            'name' => 'Storage & Components',
            'slug' => 'storage-components',
            'description' => 'High-speed NVMe SSDs, RAM kits, PC components, and cooling.',
        ]);
        $catOffice = Category::create([
            'name' => 'Networking & Office',
            'slug' => 'networking-office',
            'description' => 'WiFi routers, ergonomic chairs, standing desks, and office essentials.',
        ]);

        // 3. Senarai 20 Produk Pilihan Berkualiti Tinggi
        $products = [
            // Laptops & Computers (4 items)
            [
                'name' => 'Pro Gaming Laptop 15',
                'price' => 4500.00,
                'description' => 'High-performance laptop engineered for software development and gaming.',
                'stock' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&q=80',
                'category_id' => $catLaptops->id,
            ],
            [
                'name' => 'UltraBook Thin & Light 14',
                'price' => 3899.00,
                'description' => 'Ultra-portable aluminum laptop with all-day battery life and OLED screen.',
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&q=80',
                'category_id' => $catLaptops->id,
            ],
            [
                'name' => 'Creator Workstation 16 Pro',
                'price' => 7299.00,
                'description' => 'Studio-grade mobile workstation optimized for 3D rendering and video editing.',
                'stock' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=500&q=80',
                'category_id' => $catLaptops->id,
            ],
            [
                'name' => 'Compact Mini PC Ryzen 7',
                'price' => 2199.00,
                'description' => 'Powerful palm-sized desktop computer with 8 cores and triple 4K output support.',
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=500&q=80',
                'category_id' => $catLaptops->id,
            ],

            // Peripherals & Accessories (4 items)
            [
                'name' => 'Wireless Ergonomic Mouse',
                'price' => 129.90,
                'description' => 'Ergonomic wireless mouse designed for all-day productivity and precision.',
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500&q=80',
                'category_id' => $catPeripherals->id,
            ],
            [
                'name' => 'RGB Mechanical Keyboard',
                'price' => 299.99,
                'description' => 'Tactile mechanical keyboard with customizable per-key RGB backlighting.',
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&q=80',
                'category_id' => $catPeripherals->id,
            ],
            [
                'name' => 'Precision Optical Gaming Mouse',
                'price' => 189.00,
                'description' => 'Ultra-lightweight gaming mouse with 26K DPI optical sensor and PTFE feet.',
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=500&q=80',
                'category_id' => $catPeripherals->id,
            ],
            [
                'name' => '4K Ultra HD Webcam',
                'price' => 349.00,
                'description' => 'Crystal-clear 4K HDR webcam with AI autofocus and noise-reducing stereo mic.',
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=500&q=80',
                'category_id' => $catPeripherals->id,
            ],

            // Audio & Displays (4 items)
            [
                'name' => '27-inch 4K UHD Monitor',
                'price' => 1299.00,
                'description' => 'Ultra-crisp 4K UHD display with HDR and 99% sRGB color accuracy.',
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&q=80',
                'category_id' => $catAudioVisual->id,
            ],
            [
                'name' => '34-inch Ultrawide Curved Monitor',
                'price' => 2499.00,
                'description' => 'Immersive 1000R curved gaming monitor with 165Hz refresh rate and FreeSync.',
                'stock' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&q=80',
                'category_id' => $catAudioVisual->id,
            ],
            [
                'name' => 'Wireless Noise-Cancelling Headphones',
                'price' => 899.00,
                'description' => 'Premium active noise cancellation headphones with 40-hour battery life.',
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'category_id' => $catAudioVisual->id,
            ],
            [
                'name' => 'Spatial Audio Gaming Headset',
                'price' => 299.00,
                'description' => '7.1 surround sound gaming headset with breathable memory foam ear cushions.',
                'stock' => 35,
                'image_url' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&q=80',
                'category_id' => $catAudioVisual->id,
            ],

            // Storage & Components (4 items)
            [
                'name' => '1TB NVMe PCIe 4.0 M.2 SSD',
                'price' => 389.00,
                'description' => 'Blazing fast solid-state drive delivering up to 7400 MB/s read speeds.',
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500&q=80',
                'category_id' => $catStorage->id,
            ],
            [
                'name' => '2TB Rugged Portable External SSD',
                'price' => 649.00,
                'description' => 'Drop-resistant, water-resistant portable SSD with USB 3.2 Gen 2x2 connectivity.',
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1544652478-6653e09f18a2?w=500&q=80',
                'category_id' => $catStorage->id,
            ],
            [
                'name' => '32GB DDR5 6000MHz RAM Kit',
                'price' => 499.00,
                'description' => 'Dual-channel desktop RAM kit (2x16GB) with aluminum heatspreaders.',
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500&q=80',
                'category_id' => $catStorage->id,
            ],
            [
                'name' => '4TB External Backup Hard Drive',
                'price' => 429.00,
                'description' => 'High-capacity external hard drive with automatic backup utility included.',
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500&q=80',
                'category_id' => $catStorage->id,
            ],

            // Networking & Office (4 items)
            [
                'name' => 'WiFi 6 Dual-Band Gigabit Router',
                'price' => 299.00,
                'description' => 'Next-gen AX3000 router delivering ultra-fast streaming and low gaming latency.',
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500&q=80',
                'category_id' => $catOffice->id,
            ],
            [
                'name' => 'Ergonomic Mesh High-Back Chair',
                'price' => 799.00,
                'description' => 'Breathable mesh chair with 3D armrests and dynamic lumbar spinal support.',
                'stock' => 12,
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=500&q=80',
                'category_id' => $catOffice->id,
            ],
            [
                'name' => 'Motorized Dual-Motor Standing Desk',
                'price' => 1299.00,
                'description' => 'Electric height-adjustable desk with anti-collision sensor and memory presets.',
                'stock' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=500&q=80',
                'category_id' => $catOffice->id,
            ],
            [
                'name' => 'Smart LED Monitor ScreenBar Light',
                'price' => 149.00,
                'description' => 'Monitor-mounted reading lamp with zero screen glare and auto-dimming sensor.',
                'stock' => 35,
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&q=80',
                'category_id' => $catOffice->id,
            ],
        ];

        // 4. Masukkan semua produk ke dalam database
        foreach ($products as $prod) {
            Product::create($prod);
        }
    }
}
