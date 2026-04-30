@extends('layouts.app')

@section('title', 'Laporan Kas')
@section('page-title', 'Laporan Arus Kas')

@section('content')
<div class="space-y-6">
    <!-- Print Header -->
    <div class="hidden print:block print-header mb-4">
        <div class="text-center border-b-2 border-gray-800 pb-3 mb-3">
            <h1 class="text-xl font-bold">APOTEK POS</h1>
            <h2 class="text-lg font-semibold">Laporan Arus Kas</h2>
            <p class="text-sm text-gray-600">Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">Dicetak: {{ now()->format('d/m/Y H:i:s') }} | Total: {{ $cashFlows->count() }} transaksi</p>
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
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm"><i class="fas fa-print mr-1"></i> Print</button>
        </form>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Total Debit (Masuk)</p>
            <p class="text-sm font-semibold text-green-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($totalDebit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Total Kredit (Keluar)</p>
            <p class="text-sm font-semibold text-red-600 mt-1">Rp</p>
            <p class="text-xl font-bold text-red-600">{{ number_format($totalKredit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Saldo</p>
            <p class="text-sm font-semibold {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">Rp</p>
            <p class="text-xl font-bold {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($saldo, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-800">Detail Arus Kas ({{ $cashFlows->count() }} transaksi)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="cashflow-table">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-center py-2 px-2" style="width:35px">#</th>
                        <th class="text-left py-2 px-2" style="width:85px">Tanggal</th>
                        <th class="text-center py-2 px-2" style="width:55px">Tipe</th>
                        <th class="text-left py-2 px-2">Keterangan</th>
                        <th class="text-right py-2 px-2" style="width:110px">Debit (Masuk)</th>
                        <th class="text-right py-2 px-2" style="width:110px">Kredit (Keluar)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashFlows as $index => $cf)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-2 text-center text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $cf->tanggal->format('d/m/Y') }}</td>
                        <td class="py-2 px-2 text-center">
                            @if($cf->tipe == 'debit')
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Masuk</span>
                            @else
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">Keluar</span>
                            @endif
                        </td>
                        <td class="py-2 px-2">
                            <div class="truncate" title="{{ $cf->keterangan }}">{{ $cf->keterangan }}</div>
                            @if($cf->referensi)
                                <div class="text-xs text-gray-400 font-mono">{{ $cf->referensi }}</div>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-right whitespace-nowrap {{ $cf->tipe == 'debit' ? 'text-green-600 font-medium' : 'text-gray-300' }}">
                            {{ $cf->tipe == 'debit' ? 'Rp ' . number_format($cf->nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2 px-2 text-right whitespace-nowrap {{ $cf->tipe == 'kredit' ? 'text-red-600 font-medium' : 'text-gray-300' }}">
                            {{ $cf->tipe == 'kredit' ? 'Rp ' . number_format($cf->nominal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada data</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                    <tr class="font-bold">
                        <td colspan="4" class="py-3 px-2 text-right">TOTAL:</td>
                        <td class="py-3 px-2 text-right text-green-700 whitespace-nowrap">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-right text-red-700 whitespace-nowrap">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="font-bold text-base">
                        <td colspan="4" class="py-2 px-2 text-right">SALDO:</td>
                        <td colspan="2" class="py-2 px-2 text-right whitespace-nowrap {{ $saldo >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
