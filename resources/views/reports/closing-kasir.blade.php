@extends('layouts.app')

@section('title', 'Closing Kasir')
@section('page-title', 'Closing Kasir')

@section('content')
<div class="space-y-6" x-data="{ showOpenModal: false, showExpenseModal: false, expenseShiftId: null }">

    <!-- Info Box: Penjelasan -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
        <h4 class="font-semibold text-indigo-800 text-sm mb-1"><i class="fas fa-info-circle mr-1"></i> Cara Kerja Closing Kasir</h4>
        <p class="text-xs text-indigo-700">
            <strong>1. Buka Shift</strong> → Input saldo awal di laci kasir &nbsp;→&nbsp;
            <strong>2. Transaksi & Pengeluaran</strong> → Catat penjualan di POS & pengeluaran operasional &nbsp;→&nbsp;
            <strong>3. Tutup Shift</strong> → Sistem otomatis rekap total penjualan & hitung saldo akhir
        </p>
        <p class="text-xs text-indigo-600 mt-1">
            <strong>Rumus:</strong> Saldo Akhir = Saldo Awal + Penjualan Tunai − Pengeluaran
        </p>
    </div>

    <!-- Open Shift Button -->
    <div class="flex justify-end">
        <button @click="showOpenModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
            <i class="fas fa-play mr-2"></i> Buka Shift
        </button>
    </div>

    <!-- Open Shift Modal -->
    <div x-show="showOpenModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-semibold mb-4"><i class="fas fa-play text-green-500 mr-2"></i>Buka Shift Baru</h3>
            <form method="POST" action="{{ route('reports.open-shift') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                        <select name="shift" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="pagi">Pagi (07:00 - 13:59)</option>
                            <option value="siang">Siang (14:00 - 21:00)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Awal (Rp)</label>
                        <input type="text" name="saldo_awal" value="300,000" inputmode="numeric" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm money-format">
                        <p class="text-xs text-gray-500 mt-1">Uang tunai yang ada di laci kasir saat mulai shift</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showOpenModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Buka Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div x-show="showExpenseModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4"><i class="fas fa-money-bill-wave text-orange-500 mr-2"></i>Catat Pengeluaran</h3>
            <form method="POST" :action="'/reports/add-expense/' + expenseShiftId">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" name="nominal" inputmode="numeric" required placeholder="Contoh: 25,000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 money-format">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                        <input type="text" name="keterangan" required placeholder="Contoh: Beli plastik kresek, ongkir, konsumsi..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Pengeluaran adalah uang kas yang <strong>keluar dari laci kasir</strong> untuk keperluan operasional 
                            (beli perlengkapan, konsumsi, ongkir, dll). Ini akan mengurangi saldo akhir shift.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showExpenseModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Registers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-3">Tanggal</th>
                        <th class="text-center py-3 px-3">Shift</th>
                        <th class="text-left py-3 px-3">Kasir</th>
                        <th class="text-right py-3 px-3">Saldo Awal</th>
                        <th class="text-right py-3 px-3">Tunai</th>
                        <th class="text-right py-3 px-3">Non Tunai</th>
                        <th class="text-right py-3 px-3">HV</th>
                        <th class="text-right py-3 px-3">Resep</th>
                        <th class="text-right py-3 px-3">Pengeluaran</th>
                        <th class="text-right py-3 px-3">Saldo Akhir</th>
                        <th class="text-center py-3 px-3">Status</th>
                        <th class="text-center py-3 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registers as $reg)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-3">{{ $reg->tanggal->format('d/m/Y') }}</td>
                        <td class="py-3 px-3 text-center capitalize">{{ $reg->shift }}</td>
                        <td class="py-3 px-3">{{ $reg->user->name }}</td>
                        <td class="py-3 px-3 text-right">Rp {{ number_format($reg->saldo_awal, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-green-700">Rp {{ number_format($reg->total_penjualan_tunai, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-blue-700">Rp {{ number_format($reg->total_penjualan_non_tunai, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">Rp {{ number_format($reg->total_penjualan_hv, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">Rp {{ number_format($reg->total_penjualan_resep, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right {{ $reg->pengeluaran > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                            {{ $reg->pengeluaran > 0 ? '- Rp ' . number_format($reg->pengeluaran, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-3 text-right font-bold text-indigo-700">Rp {{ number_format($reg->saldo_akhir, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-center">
                            @if($reg->status == 'open')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Open</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">Closed</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($reg->status == 'open')
                            <div class="flex items-center justify-center space-x-1">
                                <button @click="expenseShiftId = {{ $reg->id }}; showExpenseModal = true"
                                        class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs hover:bg-orange-200" title="Catat Pengeluaran">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Pengeluaran
                                </button>
                                <form method="POST" action="{{ route('reports.close-shift', $reg) }}" class="inline" onsubmit="return confirm('Tutup shift ini? Pastikan semua pengeluaran sudah dicatat.')">
                                    @csrf
                                    <button class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                        <i class="fas fa-stop mr-1"></i> Tutup
                                    </button>
                                </form>
                            </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-8 text-center text-gray-500">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $registers->links() }}</div>
    </div>
</div>
@endsection
