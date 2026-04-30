

<?php $__env->startSection('title', 'Pembelian'); ?>
<?php $__env->startSection('page-title', 'Data Pembelian'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola pembelian barang dari supplier</p>
        <a href="<?php echo e(route('purchases.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Input Pembelian
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs text-gray-500 mb-1">No. Faktur</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php echo e(request('supplier_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-search mr-1"></i> Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600">No. Faktur</th>
                        <th class="text-left py-3 px-4 text-gray-600">Tanggal</th>
                        <th class="text-left py-3 px-4 text-gray-600">Supplier</th>
                        <th class="text-right py-3 px-4 text-gray-600">Total</th>
                        <th class="text-center py-3 px-4 text-gray-600">Status Bayar</th>
                        <th class="text-left py-3 px-4 text-gray-600">Jatuh Tempo</th>
                        <th class="text-center py-3 px-4 text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono font-medium">
                            <a href="<?php echo e(route('purchases.show', $purchase)); ?>" class="text-indigo-600 hover:text-indigo-800"><?php echo e($purchase->no_faktur); ?></a>
                        </td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($purchase->tanggal_faktur->format('d/m/Y')); ?></td>
                        <td class="py-3 px-4"><?php echo e($purchase->supplier->nama); ?></td>
                        <td class="py-3 px-4 text-right font-medium">Rp <?php echo e(number_format($purchase->grand_total, 0, ',', '.')); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if($purchase->status_bayar == 'lunas'): ?>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Lunas</span>
                            <?php elseif($purchase->status_bayar == 'sebagian'): ?>
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Sebagian</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($purchase->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-'); ?></td>
                        <td class="py-3 px-4 text-center">
                            <a href="<?php echo e(route('purchases.show', $purchase)); ?>" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo e(route('purchases.return', $purchase)); ?>" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded" title="Retur"><i class="fas fa-undo"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Belum ada data pembelian</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t"><?php echo e($purchases->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/purchases/index.blade.php ENDPATH**/ ?>