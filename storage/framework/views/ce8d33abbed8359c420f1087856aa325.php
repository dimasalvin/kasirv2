

<?php $__env->startSection('title', 'Supplier'); ?>
<?php $__env->startSection('page-title', 'Data Supplier (PBF)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola data supplier / Pedagang Besar Farmasi</p>
        <a href="<?php echo e(route('suppliers.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah Supplier
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Cari Supplier</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama, kode, kota..."
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
                    <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono text-xs"><?php echo e($supplier->kode); ?></td>
                        <td class="py-3 px-4 font-medium"><?php echo e($supplier->nama); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($supplier->kota ?? '-'); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($supplier->no_telp ?? '-'); ?></td>
                        <td class="py-3 px-4 text-center"><?php echo e($supplier->jatuh_tempo); ?> hari</td>
                        <td class="py-3 px-4 text-center">
                            <?php if($supplier->is_active): ?>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="<?php echo e(route('suppliers.show', $supplier)); ?>" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" class="inline" onsubmit="return confirm('Hapus supplier ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Belum ada data supplier</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t"><?php echo e($suppliers->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/suppliers/index.blade.php ENDPATH**/ ?>