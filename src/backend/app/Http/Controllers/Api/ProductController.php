<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * 1. Papar Senarai Produk Berserta Caching Redis (TTL: 60 Saat)
     */
    public function index(Request $request)
    {
        // 1. Cipta kunci cache unik berdasarkan parameter URL (carian, susun, muka surat)
        $cacheKey = 'products_index_' . md5(json_encode($request->query()));

        // Semak adakah data ini sudah ada dalam RAM Redis sebelum diambil
        $isCached = Cache::has($cacheKey);

        // 2. Gunakan Cache::remember: Jika ada dalam Redis, ambil terus; jika tiada, tanya PostgreSQL dan simpan ke Redis selama 60 saat
        $cachedData = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Product::with('category');

            // Carian (Search) mengabaikan huruf besar/kecil (ilike)
            if ($request->has('search') && !empty($request->search)) {
                $keyword = $request->search;
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'ilike', "%{$keyword}%")
                      ->orWhere('description', 'ilike', "%{$keyword}%");
                });
            }

            // Susunan (Sorting)
            $sortBy = $request->get('sort_by', 'created_at');
            $order  = $request->get('order', 'desc');
            $query->orderBy($sortBy, $order)->orderBy('id', $order);

            // Penomboran Halaman (Pagination)
            $perPage  = $request->get('per_page', 5);
            $products = $query->paginate($perPage);

            return [
                'items' => $products->toArray()['data'],
                'meta'  => [
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                ]
            ];
        });

        // 3. Pulangkan respon JSON berserta label sumber data
        return response()->json([
            'success' => true,
            'message' => 'Senarai produk berjaya diambil',
            'source'  => $isCached ? 'REDIS_CACHE (Pantas/RAM)' : 'POSTGRESQL_DB (Pertanyaan Asal)',
            'data'    => $cachedData['items'],
            'meta'    => $cachedData['meta']
        ]);
    }

    /**
     * 2. Tambah Produk Baharu & Padam Cache (Cache Invalidation)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'required|string',
            'stock'       => 'required|integer',
            'image_url'   => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product = Product::create($validated);

        // Invalidation: Padam semua cache produk dalam Redis supaya data baharu kelihatan serta-merta!
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya ditambah',
            'data'    => $product
        ], 201);
    }

    /**
     * 3. Papar Butiran 1 Produk
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak dijumpai'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product
        ]);
    }

    /**
     * 4. Kemas Kini Produk & Padam Cache (Cache Invalidation)
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak dijumpai'
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'stock'       => 'required|integer',
            'image_url'   => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($validated);

        // Invalidation: Padam cache lama dalam Redis
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya dikemaskini',
            'data'    => $product
        ]);
    }

    /**
     * 5. Padam Produk & Padam Cache (Cache Invalidation)
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak dijumpai'
            ], 404);
        }

        $product->delete();

        // Invalidation: Padam cache lama dalam Redis
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya dipadam'
        ]);
    }
}