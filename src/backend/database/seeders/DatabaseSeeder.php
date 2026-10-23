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
        // ====================================================================
        // 1. PENGGUNA (ADMIN & CUSTOMER)
        // ====================================================================
        User::create([
            'name'     => 'Admin COE',
            'email'    => 'admin@coe.com',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Customer Tester',
            'email'    => 'customer@coe.com',
            'password' => 'password123',
            'role'     => 'customer',
        ]);

        // ====================================================================
        // 2. KATEGORI PRODUK
        // ====================================================================
        $catLaptop = Category::create([
            'name'        => 'Laptop & Komputer',
            'slug'        => 'laptop-komputer',
            'description' => 'Peranti komputer riba, ultrabook, dan PC desktop berkuasa tinggi.',
        ]);

        $catAksesori = Category::create([
            'name'        => 'Aksesori Komputer',
            'slug'        => 'aksesori-komputer',
            'description' => 'Tetikus, papan kekunci, pad tetikus, dan kelengkapan meja kerja.',
        ]);

        $catAudio = Category::create([
            'name'        => 'Audio & Visual',
            'slug'        => 'audio-visual',
            'description' => 'Fon kepala, pembesar suara wayarles, monitor, dan kamera web.',
        ]);

        $catGajet = Category::create([
            'name'        => 'Telefon & Gajet Pintar',
            'slug'        => 'telefon-gajet',
            'description' => 'Telefon pintar, tablet, jam pintar, dan pengecas tanpa wayar.',
        ]);

        // ====================================================================
        // 3. SENARAI 20 PRODUK E-DAGANG REALISTIK
        // ====================================================================
        $products = [
            // Kategori: Laptop & Komputer
            [
                'name'        => 'Laptop Gaming Pro 15',
                'price'       => 4599.00,
                'description' => 'Komputer riba berkuasa tinggi dengan Intel i7 dan kad grafik RTX 4060.',
                'stock'       => 12,
                'image_url'   => 'https://picsum.photos/seed/laptop1/400/300',
                'category_id' => $catLaptop->id,
            ],
            [
                'name'        => 'Ultrabook Air Slim 13',
                'price'       => 3299.00,
                'description' => 'Komputer riba nipis dan ringan dengan ketahanan bateri sehingga 18 jam.',
                'stock'       => 20,
                'image_url'   => 'https://picsum.photos/seed/laptop2/400/300',
                'category_id' => $catLaptop->id,
            ],
            [
                'name'        => 'PC Desktop Workstation Z8',
                'price'       => 6899.00,
                'description' => 'Stesen kerja berprestasi ekstrim untuk suntingan video 4K dan pemodelan 3D.',
                'stock'       => 5,
                'image_url'   => 'https://picsum.photos/seed/pcworkstation/400/300',
                'category_id' => $catLaptop->id,
            ],
            [
                'name'        => 'Mini PC Kompak Intel N100',
                'price'       => 899.00,
                'description' => 'Komputer saiz poket jimat tenaga untuk kerja pejabat dan tontonan media.',
                'stock'       => 25,
                'image_url'   => 'https://picsum.photos/seed/minipc/400/300',
                'category_id' => $catLaptop->id,
            ],
            [
                'name'        => 'Laptop Pelajar Celeron 14',
                'price'       => 1199.00,
                'description' => 'Pilihan mampu milik untuk tugasan sekolah, kelas dalam talian, dan pelayaran web.',
                'stock'       => 30,
                'image_url'   => 'https://picsum.photos/seed/studentlaptop/400/300',
                'category_id' => $catLaptop->id,
            ],

            // Kategori: Aksesori Komputer
            [
                'name'        => 'Papan Kekunci Mekanikal RGB',
                'price'       => 299.90,
                'description' => 'Papan kekunci mekanikal dengan suis merah senyap dan lampu RGB dinamik.',
                'stock'       => 45,
                'image_url'   => 'https://picsum.photos/seed/keyboard1/400/300',
                'category_id' => $catAksesori->id,
            ],
            [
                'name'        => 'Tetikus Wayarles Ergonomik',
                'price'       => 129.90,
                'description' => 'Direka mengurangkan lenguh pergelangan tangan semasa kerja berjam-jam.',
                'stock'       => 60,
                'image_url'   => 'https://picsum.photos/seed/mouse1/400/300',
                'category_id' => $catAksesori->id,
            ],
            [
                'name'        => 'Tetikus Gaming Berkelajuan Tinggi 16000 DPI',
                'price'       => 199.00,
                'description' => 'Sensor optik ketepatan tinggi dengan butang boleh diatur cara.',
                'stock'       => 35,
                'image_url'   => 'https://picsum.photos/seed/gamingmouse/400/300',
                'category_id' => $catAksesori->id,
            ],
            [
                'name'        => 'Pad Meja Ekstra Besar (XXL)',
                'price'       => 49.00,
                'description' => 'Pelapik meja kalis air dengan tapak getah anti-gelincir berukuran 900x400mm.',
                'stock'       => 80,
                'image_url'   => 'https://picsum.photos/seed/mousepad/400/300',
                'category_id' => $catAksesori->id,
            ],
            [
                'name'        => 'Penyangga Komputer Riba Aluminium',
                'price'       => 79.00,
                'description' => 'Dudukan boleh laras ketinggian untuk postur leher yang lebih sihat.',
                'stock'       => 50,
                'image_url'   => 'https://picsum.photos/seed/laptopstand/400/300',
                'category_id' => $catAksesori->id,
            ],

            // Kategori: Audio & Visual
            [
                'name'        => 'Monitor Gaming 27 Inci 165Hz IPS',
                'price'       => 849.00,
                'description' => 'Paparan resolusi QHD 2K dengan kadar segar semula 165Hz dan masa tindak 1ms.',
                'stock'       => 18,
                'image_url'   => 'https://picsum.photos/seed/monitor27/400/300',
                'category_id' => $catAudio->id,
            ],
            [
                'name'        => 'Fon Kepala Wayarles Pembatalan Hingar (ANC)',
                'price'       => 499.00,
                'description' => 'Teknologi Active Noise Cancelling premium untuk fokus tanpa gangguan.',
                'stock'       => 22,
                'image_url'   => 'https://picsum.photos/seed/headphones/400/300',
                'category_id' => $catAudio->id,
            ],
            [
                'name'        => 'Fon Telinga TWS Bluetooth 5.3',
                'price'       => 159.00,
                'description' => 'Fon telinga kecil tanpa wayar dengan bass mendalam dan kalis peluh IPX5.',
                'stock'       => 70,
                'image_url'   => 'https://picsum.photos/seed/earbuds/400/300',
                'category_id' => $catAudio->id,
            ],
            [
                'name'        => 'Kamera Web HD Penuh 1080P dengan Mikrofon',
                'price'       => 139.00,
                'description' => 'Kamera berkualiti tajam untuk mesyuarat Zoom, Google Meet, dan strim langsung.',
                'stock'       => 40,
                'image_url'   => 'https://picsum.photos/seed/webcam/400/300',
                'category_id' => $catAudio->id,
            ],
            [
                'name'        => 'Pembesar Suara Bluetooth Kalis Air',
                'price'       => 219.00,
                'description' => 'Audio 360 darjah bertenaga dengan bateri tahan 12 jam untuk aktiviti luar.',
                'stock'       => 30,
                'image_url'   => 'https://picsum.photos/seed/speaker/400/300',
                'category_id' => $catAudio->id,
            ],

            // Kategori: Telefon & Gajet Pintar
            [
                'name'        => 'Telefon Pintar Flagship 5G 256GB',
                'price'       => 3499.00,
                'description' => 'Skrin AMOLED 120Hz, kamera tri-lensa 108MP, dan pengecasan pantas 67W.',
                'stock'       => 15,
                'image_url'   => 'https://picsum.photos/seed/phone1/400/300',
                'category_id' => $catGajet->id,
            ],
            [
                'name'        => 'Tablet Kreatif 11 Inci dengan Stylus Pen',
                'price'       => 1899.00,
                'description' => 'Sesuai untuk melukis ilustrasi digital, mengambil nota, dan membaca dokumen.',
                'stock'       => 16,
                'image_url'   => 'https://picsum.photos/seed/tablet/400/300',
                'category_id' => $catGajet->id,
            ],
            [
                'name'        => 'Jam Tangan Pintar Kesihatan & Sukan',
                'price'       => 389.00,
                'description' => 'Pantau degupan jantung, tahap oksigen SpO2, pola tidur, dan 100+ mod senaman.',
                'stock'       => 45,
                'image_url'   => 'https://picsum.photos/seed/smartwatch/400/300',
                'category_id' => $catGajet->id,
            ],
            [
                'name'        => 'Powerbank Pantas 20000mAh 65W PD',
                'price'       => 149.00,
                'description' => 'Kapasiti mega yang mampu mengecas telefon pintar dan laptop jenis Type-C.',
                'stock'       => 65,
                'image_url'   => 'https://picsum.photos/seed/powerbank/400/300',
                'category_id' => $catGajet->id,
            ],
            [
                'name'        => 'Pengecas Wayarles 3-dalam-1 Lipat',
                'price'       => 99.00,
                'description' => 'Kemas kini meja anda dengan mengecas telefon, jam pintar, dan fon telinga serentak.',
                'stock'       => 55,
                'image_url'   => 'https://picsum.photos/seed/wirelesscharger/400/300',
                'category_id' => $catGajet->id,
            ],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }
    }
}