@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola stock opname per periode</p>
        <a href="{{ route('stock-opname.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Buat Stock Opname
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-4">Kode</th>
                    <th class="text-left py-3 px-4">Tanggal</th>
                    <th class="text-left py-3 px-4">Petugas</th>
                    <th class="text-center py-3 px-4">Status</th>
                    <th class="text-center py-3 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opnames as $opname)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-3 px-4 font-mono text-xs">{{ $opname->kode_opname }}</td>
                    <td class="py-3 px-4">{{ $opname->tanggal->format('d/m/Y') }}</td>
                    <td class="py-3 px-4">{{ $opname->user->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($opname->status == 'draft')
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Draft</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Completed</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('stock-opname.show', $opname) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada stock opname</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $opnames->links() }}</div>
    </div>
</div>
@endsection
