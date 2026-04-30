@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori Obat')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null, editNama: '', editKelas: '', editKet: '' }">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola kategori / kelas terapi obat</p>
        <button @click="showForm = !showForm; editId = null" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori
        </button>
    </div>

    <!-- Add/Edit Form -->
    <div x-show="showForm" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form :action="editId ? '/categories/' + editId : '{{ route('categories.store') }}'" method="POST">
            @csrf
            <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                    <input type="text" name="nama" x-model="editNama" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas Terapi</label>
                    <input type="text" name="kelas_terapi" x-model="editKelas" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" x-model="editKet" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-3">
                <button type="button" @click="showForm = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm">Simpan</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-4">Nama</th>
                    <th class="text-left py-3 px-4">Kelas Terapi</th>
                    <th class="text-left py-3 px-4">Keterangan</th>
                    <th class="text-center py-3 px-4">Jumlah Produk</th>
                    <th class="text-center py-3 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium">{{ $cat->nama }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $cat->kelas_terapi ?? '-' }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $cat->keterangan ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">{{ $cat->products_count }}</td>
                    <td class="py-3 px-4 text-center">
                        <button @click="showForm = true; editId = {{ $cat->id }}; editNama = '{{ $cat->nama }}'; editKelas = '{{ $cat->kelas_terapi }}'; editKet = '{{ $cat->keterangan }}'"
                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada kategori</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
