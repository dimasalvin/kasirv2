@extends('layouts.app')

@section('title', 'Detail Stock Opname')
@section('page-title', 'Stock Opname: ' . $stockOpname->kode_opname)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">{{ $stockOpname->kode_opname }}</h2>
                <p class="text-sm text-gray-500">{{ $stockOpname->tanggal->format('d F Y') }} | Petugas: {{ $stockOpname->user->name ?? '-' }}</p>
            </div>
            @if($stockOpname->status == 'draft')
                <form method="POST" action="{{ route('stock-opname.approve', $stockOpname) }}" onsubmit="return confirm('Setujui stock opname ini? Stok akan diperbarui.')">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                        <i class="fas fa-check mr-1"></i> Setujui & Update Stok
                    </button>
                </form>
            @else
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Completed</span>
            @endif
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-2 px-3">#</th>
                    <th class="text-left py-2 px-3">Barang</th>
                    <th class="text-center py-2 px-3">Stok Sistem</th>
                    <th class="text-center py-2 px-3">Stok Fisik</th>
                    <th class="text-center py-2 px-3">Selisih</th>
                    <th class="text-left py-2 px-3">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOpname->details as $i => $detail)
                <tr class="border-b border-gray-50">
                    <td class="py-2 px-3">{{ $i + 1 }}</td>
                    <td class="py-2 px-3 font-medium">{{ $detail->product->nama_barang }}</td>
                    <td class="py-2 px-3 text-center">{{ $detail->stok_sistem }}</td>
                    <td class="py-2 px-3 text-center">{{ $detail->stok_fisik }}</td>
                    <td class="py-2 px-3 text-center font-medium {{ $detail->selisih < 0 ? 'text-red-600' : ($detail->selisih > 0 ? 'text-green-600' : '') }}">
                        {{ $detail->selisih > 0 ? '+' : '' }}{{ $detail->selisih }}
                    </td>
                    <td class="py-2 px-3 text-gray-600">{{ $detail->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
