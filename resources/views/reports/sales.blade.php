@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Print Header (hanya muncul saat cetak) -->
    <div class="hidden print:block print-header mb-4">
        <div class="text-center border-b-2 border-gray-800 pb-3 mb-3">
            <h1 class="text-xl font-bold">APOTEK POS</h1>
            <h2 class="text-lg font-semibold">Laporan Penjualan</h2>
            <p class="text-sm text-gray-600">Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</p>
            @if($shift)<p class="text-sm">Shift: {{ ucfirst($shift) }}</p>@endif
            @if($metodeBayar)<p class="text-sm">Metode: {{ $metodeBayar == 'tunai' ? 'Tunai' : 'Non Tunai' }}</p>@endif
            <p class="text-xs text-gray-500 mt-1">Dicetak: {{ now()->format('d/m/Y H:i:s') }} | Total: {{ $jumlahTransaksi }} transaksi</p>
        </div>
    </div>

    <!-- Filter (hidden saat print) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-32">
                <label class="block text-xs text-gray-500 mb-1">Shift</label>
                <select name="shift" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="pagi" {{ $shift == 'pagi' ? 'selected' : '' }}>Pagi (07:00 - 13:59)</option>
                    <option value="siang" {{ $shift == 'siang' ? 'selected' : '' }}>Siang (14:00 - 21:00)</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Metode Bayar</label>
                <select name="metode_bayar" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="tunai" {{ $metodeBayar == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="non_tunai" {{ $metodeBayar == 'non_tunai' ? 'selected' : '' }}>Non Tunai</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm"><i class="fas fa-print mr-1"></i> Print</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 print:grid-cols-5">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Total Penjualan</p>
            <p class="text-sm font-semibold text-indigo-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-indigo-600">{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Tunai</p>
            <p class="text-sm font-semibold text-green-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($totalTunai, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Non Tunai</p>
            <p class="text-sm font-semibold text-blue-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($totalNonTunai, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">HV (Reguler)</p>
            <p class="text-sm font-semibold text-gray-800 mt-1">Rp</p>
            <p class="text-xl font-bold text-gray-800">{{ number_format($totalHV, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Resep</p>
            <p class="text-sm font-semibold text-purple-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-purple-600">{{ number_format($totalResep, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="font-semibold">Detail Transaksi ({{ $jumlahTransaksi }} transaksi)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-center py-2 px-3 w-10">#</th>
                        <th class="text-left py-2 px-3">No. Nota</th>
                        <th class="text-left py-2 px-3">Tanggal</th>
                        <th class="text-center py-2 px-3">Shift</th>
                        <th class="text-center py-2 px-3">Tipe</th>
                        <th class="text-center py-2 px-3">Metode</th>
                        <th class="text-left py-2 px-3">Kasir</th>
                        <th class="text-right py-2 px-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $index => $sale)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 px-3 text-center text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 font-mono text-xs">{{ $sale->no_nota }}</td>
                        <td class="py-2 px-3">{{ $sale->tanggal->format('d/m/Y') }} {{ $sale->jam }}</td>
                        <td class="py-2 px-3 text-center capitalize">{{ $sale->shift }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($sale->details->where('tipe_harga', 'resep')->count() > 0 && $sale->details->where('tipe_harga', 'hv')->count() > 0)
                                <span class="text-xs text-indigo-600">HV+Resep</span>
                            @elseif($sale->details->where('tipe_harga', 'resep')->count() > 0)
                                <span class="text-xs text-purple-600">Resep</span>
                            @else
                                <span class="text-xs text-blue-600">HV</span>
                            @endif
                            @if($sale->has_stok_minus)<span class="text-red-500 ml-1" title="Stok minus">⚠</span>@endif
                        </td>
                        <td class="py-2 px-3 text-center">{{ $sale->metode_bayar == 'tunai' ? 'Tunai' : 'Non Tunai' }}</td>
                        <td class="py-2 px-3">{{ $sale->user->name ?? '-' }}</td>
                        <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
