<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with('user')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->orderBy('nama_barang')
            ->get();

        $kodeOpname = 'SO-' . now()->format('YmdHis');

        return view('stock-opname.create', compact('products', 'kodeOpname'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_opname' => 'required|unique:stock_opnames',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.stok_fisik' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'kode_opname' => $request->kode_opname,
                'tanggal' => $request->tanggal,
                'status' => 'draft',
                'user_id' => auth()->id(),
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $selisih = $item['stok_fisik'] - $product->stok;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $item['product_id'],
                    'stok_sistem' => $product->stok,
                    'stok_fisik' => $item['stok_fisik'],
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('stock-opname.show', $opname)
                ->with('success', 'Stock opname berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan stock opname: ' . $e->getMessage());
        }
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['details.product', 'user']);
        return view('stock-opname.show', compact('stockOpname'));
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Stock opname sudah diproses.');
        }

        DB::beginTransaction();
        try {
            foreach ($stockOpname->details as $detail) {
                $product = $detail->product;
                $stokSebelum = $product->stok;

                // Update stok produk
                $product->update(['stok' => $detail->stok_fisik]);

                // Catat kartu stok
                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'opname',
                    'jumlah' => abs($detail->selisih),
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $detail->stok_fisik,
                    'referensi' => $stockOpname->kode_opname,
                    'keterangan' => 'Stock opname - selisih: ' . $detail->selisih,
                    'user_id' => auth()->id(),
                ]);
            }

            $stockOpname->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Stock opname berhasil disetujui dan stok diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses stock opname: ' . $e->getMessage());
        }
    }
}
