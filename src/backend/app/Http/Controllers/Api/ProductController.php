<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Mulakan pembina pertanyaan (Query Builder)
        $query = Product::with('category');

        // 2. Carian (Search): Jika pengguna hantar parameter ?search=...
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'ilike', "%{$keyword}%")
                  ->orWhere('description', 'ilike', "%{$keyword}%");
            });
        }
        // 3. Susunan (Sorting): Jika ada pilihan susun harga / nama / tarikh
        $sortBy = $request->get('sort_by', 'created_at'); // default: tarikh dicipta
        $order  = $request->get('order', 'desc');         // default: terkini dahulu
        $query->orderBy($sortBy, $order)->orderBy('id', $order);
        
        // 4. Penomboran Halaman (Pagination): Papar mengikut saiz halaman (cth: 5 produk)
        $perPage  = $request->get('per_page', 5);
        $products = $query->paginate($perPage);

        // 5. Pulangkan respon JSON berserta metadata halaman
        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data'    => $products->items(), // Senarai rekod untuk halaman ini
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            // 1. Validasi Input (Semak syarat keselamatan data dari borang)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'stock' => 'required|integer',
            'image_url' => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);
       
            // 2. Simpan produk baharu ke dalam pangkalan data
        $product = Product::create($validated);

            // 3. Pulangkan respon JSON dengan status HTTP 201 Created!
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Cari rekod mengikut ID (Sama macam fnSELECT... dalam C)
        $product = Product::find($id);
        
        // 2. Jika rekod tiada, pulangkan ralat 404
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        // 3. Semak syarat validasi data baharu
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'stock'       => 'required|integer',
            'image_url'   => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
            
        ]);
        // 4. Kemaskini rekod dalam database (EXEC SQL UPDATE...)
        $product->update($validated);
        // 5. Pulangkan respon JSON status berjaya
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data'    => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        // 1. Cari produk (Eloquent ORM automatik buat SELECT mengikut Primary Key)
        $product = Product::find($id);

        // 2. Semak jika rekod tiada (jika null, hantar HTTP Status Code 404)
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // 3. Padam dari database (Eloquent automatik buat DELETE SQL)
        $product->delete();
        
        // 4. Hantar respon JSON status berjaya (HTTP 200 OK secara default)
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
