<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user']);

        if ($request->filled('search')) {
            $query->where('no_faktur', 'like', "%{$request->search}%");
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->orderBy('tanggal_faktur', 'desc')->paginate(20);
        $suppliers = Supplier::where('is_active', true)->orderBy('nama')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('nama')->get();
        $products = Product::where('is_active', true)->orderBy('nama_barang')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|string|unique:purchases',
            'tanggal_faktur' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
            'items.*.expired_date' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $supplier = Supplier::find($request->supplier_id);

            $purchase = Purchase::create([
                'no_faktur' => $request->no_faktur,
                'tanggal_faktur' => $request->tanggal_faktur,
                'tanggal_jatuh_tempo' => now()->addDays($supplier->jatuh_tempo),
                'supplier_id' => $request->supplier_id,
                'status' => 'completed',
                'status_bayar' => 'belum_bayar',
                'catatan' => $request->catatan,
                'user_id' => auth()->id(),
            ]);

            $subtotal = 0;
            $diskonTotal = 0;

            foreach ($request->items as $item) {
                $hargaBeli = $item['harga_beli'];
                $jumlah = $item['jumlah'];
                $diskonPersen = $item['diskon_persen'] ?? 0;
                $diskonNominal = ($hargaBeli * $jumlah) * ($diskonPersen / 100);
                $itemSubtotal = ($hargaBeli * $jumlah) - $diskonNominal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'subtotal' => $itemSubtotal,
                    'expired_date' => $item['expired_date'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                ]);

                // Update stok produk
                $product = Product::find($item['product_id']);
                $stokSebelum = $product->stok;
                $product->increment('stok', $jumlah);

                // Update harga beli & hitung ulang harga jual
                $hargaBeliLama = $product->harga_beli;
                $hargaJualLama = $product->harga_jual;
                $harga = Product::hitungHarga($hargaBeli);
                $product->update(array_merge(['harga_beli' => $hargaBeli], $harga));

                // Catat riwayat harga jika berubah
                if ($hargaBeliLama != $hargaBeli) {
                    \App\Models\PriceHistory::create([
                        'product_id' => $product->id,
                        'harga_beli_lama' => $hargaBeliLama,
                        'harga_beli_baru' => $hargaBeli,
                        'harga_jual_lama' => $hargaJualLama,
                        'harga_jual_baru' => $harga['harga_jual'],
                        'referensi' => $request->no_faktur,
                        'user_id' => auth()->id(),
                    ]);
                }

                // Catat kartu stok
                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'masuk',
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum + $jumlah,
                    'referensi' => $purchase->no_faktur,
                    'keterangan' => "Pembelian dari {$supplier->nama}",
                    'user_id' => auth()->id(),
                ]);

                $subtotal += ($hargaBeli * $jumlah);
                $diskonTotal += $diskonNominal;
            }

            $ppn = ($subtotal - $diskonTotal) * 0.10;
            $grandTotal = $subtotal - $diskonTotal + $ppn;

            $purchase->update([
                'subtotal' => $subtotal,
                'diskon_total' => $diskonTotal,
                'ppn' => $ppn,
                'grand_total' => $grandTotal,
            ]);

            // Catat cash flow
            CashFlow::create([
                'tanggal' => $request->tanggal_faktur,
                'tipe' => 'kredit',
                'nominal' => $grandTotal,
                'keterangan' => "Pembelian {$purchase->no_faktur} - {$supplier->nama}",
                'referensi' => $purchase->no_faktur,
                'user_id' => auth()->id(),
            ]);

            DB::commit();
            \App\Models\AuditLog::log('create', "Pembelian {$purchase->no_faktur} - {$supplier->nama} - Rp " . number_format($grandTotal, 0, ',', '.'), 'Purchase', $purchase->id);
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'details.product', 'user', 'returns']);
        return view('purchases.show', compact('purchase'));
    }

    // Retur Pembelian
    public function createReturn(Purchase $purchase)
    {
        $purchase->load(['supplier', 'details.product']);
        return view('purchases.return', compact('purchase'));
    }

    public function storeReturn(Request $request, Purchase $purchase)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $noRetur = 'RTR-' . now()->format('YmdHis');

            $return = PurchaseReturn::create([
                'no_retur' => $noRetur,
                'tanggal' => now(),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'alasan' => $request->alasan,
                'user_id' => auth()->id(),
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $harga = $product->harga_beli;
                $subtotal = $harga * $item['jumlah'];

                PurchaseReturnDetail::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok
                $stokSebelum = $product->stok;
                $product->decrement('stok', $item['jumlah']);

                // Catat kartu stok
                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'retur',
                    'jumlah' => $item['jumlah'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum - $item['jumlah'],
                    'referensi' => $noRetur,
                    'keterangan' => "Retur pembelian ke {$purchase->supplier->nama}",
                    'user_id' => auth()->id(),
                ]);

                $total += $subtotal;
            }

            $return->update(['total' => $total]);

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Retur pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur: ' . $e->getMessage());
        }
    }
}
