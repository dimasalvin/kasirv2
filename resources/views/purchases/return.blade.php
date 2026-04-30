@extends('layouts.app')

@section('title', 'Retur Pembelian')
@section('page-title', 'Retur Pembelian: ' . $purchase->no_faktur)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-4 bg-orange-50 rounded-lg p-3">
            <p class="text-sm"><strong>Supplier:</strong> {{ $purchase->supplier->nama }}</p>
            <p class="text-sm"><strong>No. Faktur:</strong> {{ $purchase->no_faktur }}</p>
        </div>

        <form method="POST" action="{{ route('purchases.store-return', $purchase) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Retur</label>
                <textarea name="alasan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>

            <h4 class="font-medium text-gray-800 mb-2">Pilih Item untuk Diretur:</h4>
            <table class="w-full text-sm mb-4">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="py-2 px-3 text-center w-10">
                            <label class="sr-only" for="checkAll">Pilih Semua</label>
                            <input type="checkbox" id="checkAll" class="rounded">
                        </th>
                        <th class="py-2 px-3 text-left">Barang</th>
                        <th class="py-2 px-3 text-center">Qty Beli</th>
                        <th class="py-2 px-3 text-center">Qty Retur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->details as $i => $detail)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 px-3 text-center">
                            <label class="sr-only" for="item-check-{{ $i }}">Pilih {{ $detail->product->nama_barang }}</label>
                            <input type="checkbox" id="item-check-{{ $i }}" name="items[{{ $i }}][product_id]" value="{{ $detail->product_id }}" class="item-check rounded">
                        </td>
                        <td class="py-2 px-3">{{ $detail->product->nama_barang }}</td>
                        <td class="py-2 px-3 text-center">{{ $detail->jumlah }}</td>
                        <td class="py-2 px-3 text-center">
                            <label class="sr-only" for="qty-retur-{{ $i }}">Qty retur {{ $detail->product->nama_barang }}</label>
                            <input type="number" id="qty-retur-{{ $i }}" name="items[{{ $i }}][jumlah]" min="1" max="{{ $detail->jumlah }}" value="1"
                                   class="w-16 border border-gray-300 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('purchases.show', $purchase) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-undo mr-1"></i> Proses Retur
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.item-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection
