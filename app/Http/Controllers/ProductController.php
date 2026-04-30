<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ;
            });
        }

        if ($request->filled('grup')) {
            $query->where('grup', $request->grup);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->stok_menipis) {
            $query->stokMenipis();
        }

        $products = $query->orderBy('nama_barang')->paginate(20);
        $categories = Category::orderBy('nama')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:products',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'pabrik' => 'nullable|string|max:255',
            'grup' => 'required|in:hijau,merah,biru',
            'kelas_terapi' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'lokasi_rak' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        // Hitung harga otomatis
        $harga = Product::hitungHarga($validated['harga_beli']);
        $validated = array_merge($validated, $harga);

        $product = Product::create($validated);

        // Catat kartu stok awal jika stok > 0
        if ($product->stok > 0) {
            StockCard::create([
                'product_id' => $product->id,
                'tipe' => 'masuk',
                'jumlah' => $product->stok,
                'stok_sebelum' => 0,
                'stok_sesudah' => $product->stok,
                'referensi' => 'STOK-AWAL',
                'keterangan' => 'Stok awal input barang',
                'user_id' => auth()->id(),
            ]);
        }

        \App\Models\AuditLog::log('create', "Tambah barang: {$product->nama_barang} ({$product->kode_barang})", 'Product', $product->id);

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category');
        $stockCards = $product->stockCards()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $priceHistories = $product->priceHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('products.show', compact('product', 'stockCards', 'priceHistories'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'pabrik' => 'nullable|string|max:255',
            'grup' => 'required|in:hijau,merah,biru',
            'kelas_terapi' => 'nullable|string|max:255',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'lokasi_rak' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'keterangan' => 'nullable|string',
        ]);

        // Hitung ulang harga jika HNA berubah
        if ($validated['harga_beli'] != $product->harga_beli) {
            $harga = Product::hitungHarga($validated['harga_beli']);
            $validated = array_merge($validated, $harga);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $product->update($validated);
        \App\Models\AuditLog::log('update', "Edit barang: {$product->nama_barang} ({$product->kode_barang})", 'Product', $product->id);

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        \App\Models\AuditLog::log('delete', "Hapus barang: {$product->nama_barang} ({$product->kode_barang})", 'Product', $product->id);
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    // API: Search product by kode
    public function searchByCode(Request $request)
    {
        $code = $request->get('code');
        $product = Product::where('kode_barang', $code)->first();

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan']);
    }

    // Hitung harga API
    public function hitungHarga(Request $request)
    {
        $hna = $request->get('hna', 0);
        $harga = Product::hitungHarga($hna);
        return response()->json($harga);
    }
}
