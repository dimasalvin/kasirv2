

<?php $__env->startSection('title', 'Produk Terlaris'); ?>
<?php $__env->startSection('page-title', 'Produk Terlaris'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Print Header -->
    <div class="hidden print:block print-header mb-4">
        <div class="text-center border-b-2 border-gray-800 pb-3 mb-3">
            <h1 class="text-xl font-bold">APOTEK POS</h1>
            <h2 class="text-lg font-semibold">Laporan Produk Terlaris</h2>
            <p class="text-sm text-gray-600">Periode: <?php echo e(\Carbon\Carbon::parse($tanggalDari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y')); ?></p>
            <p class="text-xs text-gray-500 mt-1">Dicetak: <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
        </div>
    </div>

    <!-- Filter (hidden saat print) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="<?php echo e($tanggalDari); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="<?php echo e($tanggalSampai); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm no-print"><i class="fas fa-print mr-1"></i> Print</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-center py-3 px-4 text-gray-600 w-12">#</th>
                        <th class="text-left py-3 px-4 text-gray-600">Kode</th>
                        <th class="text-left py-3 px-4 text-gray-600">Nama Barang</th>
                        <th class="text-right py-3 px-4 text-gray-600">Total Qty Terjual</th>
                        <th class="text-right py-3 px-4 text-gray-600">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 text-center">
                            <?php if($i < 3): ?>
                                <span class="w-6 h-6 inline-flex items-center justify-center rounded-full text-xs font-bold <?php echo e($i == 0 ? 'bg-yellow-100 text-yellow-700' : ($i == 1 ? 'bg-gray-100 text-gray-700' : 'bg-orange-100 text-orange-700')); ?>">
                                    <?php echo e($i + 1); ?>

                                </span>
                            <?php else: ?>
                                <?php echo e($i + 1); ?>

                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 font-mono text-xs"><?php echo e($product->kode_barang); ?></td>
                        <td class="py-3 px-4 font-medium"><?php echo e($product->nama_barang); ?></td>
                        <td class="py-3 px-4 text-right font-medium"><?php echo e(number_format($product->total_qty)); ?></td>
                        <td class="py-3 px-4 text-right font-medium text-indigo-600">Rp <?php echo e(number_format($product->total_penjualan, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/reports/top-products.blade.php ENDPATH**/ ?>