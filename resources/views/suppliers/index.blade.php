@extends('layouts.app')

@section('title', 'Supplier')
@section('page-title', 'Data Supplier (PBF)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola data supplier / Pedagang Besar Farmasi</p>
        <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah Supplier
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Cari Supplier</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, kota..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-search mr-1"></i> Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600">Kode</th>
                        <th class="text-left py-3 px-4 text-gray-600">Nama</th>
                        <th class="text-left py-3 px-4 text-gray-600">Kota</th>
                        <th class="text-left py-3 px-4 text-gray-600">No. Telp</th>
                        <th class="text-center py-3 px-4 text-gray-600">Jatuh Tempo</th>
                        <th class="text-center py-3 px-4 text-gray-600">Status</th>
                        <th class="text-center py-3 px-4 text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono text-xs">{{ $supplier->kode }}</td>
                        <td class="py-3 px-4 font-medium">{{ $supplier->nama }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $supplier->kota ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $supplier->no_telp ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">{{ $supplier->jatuh_tempo }} hari</td>
                        <td class="py-3 px-4 text-center">
                            @if($supplier->is_active)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Hapus supplier ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Belum ada data supplier</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $suppliers->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
