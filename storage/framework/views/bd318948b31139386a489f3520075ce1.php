

<?php $__env->startSection('title', 'Data Barang'); ?>
<?php $__env->startSection('page-title', 'Data Barang / Obat'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-gray-500 text-sm">Kelola data barang dan stok obat apotek</p>
        </div>
        <a href="<?php echo e(route('products.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah Barang
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?php echo e(route('products.index')); ?>" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Cari</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Kode / Nama barang..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Grup</label>
                <select name="grup" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="hijau" <?php echo e(request('grup') == 'hijau' ? 'selected' : ''); ?>>🟢 Obat Bebas</option>
                    <option value="merah" <?php echo e(request('grup') == 'merah' ? 'selected' : ''); ?>>🔴 Obat Keras</option>
                    <option value="biru" <?php echo e(request('grup') == 'biru' ? 'selected' : ''); ?>>🔵 Konsinyasi</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="stok_menipis" value="1" <?php echo e(request('stok_menipis') ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-indigo-600 mr-1">
                    Stok Menipis
                </label>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Kode</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Nama Barang</th>
                        <th class="text-center py-3 px-4 text-gray-600 font-medium">Grup</th>
                        <th class="text-center py-3 px-4 text-gray-600 font-medium">Satuan</th>
                        <th class="text-center py-3 px-4 text-gray-600 font-medium">Stok</th>
                        <th class="text-right py-3 px-4 text-gray-600 font-medium">Harga HV</th>
                        <th class="text-right py-3 px-4 text-gray-600 font-medium">Harga Resep</th>
                        <th class="text-center py-3 px-4 text-gray-600 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded"><?php echo e($product->kode_barang); ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium text-gray-800"><?php echo e($product->nama_barang); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($product->pabrik ?? '-'); ?> | <?php echo e($product->category->nama ?? '-'); ?></p>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if($product->grup == 'hijau'): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Bebas</span>
                            <?php elseif($product->grup == 'merah'): ?>
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Keras</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Konsinyasi</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center text-gray-600"><?php echo e($product->satuan); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if($product->stok <= $product->stok_minimum): ?>
                                <span class="px-2.5 py-1 bg-red-600 text-white rounded-full text-xs font-bold"><?php echo e($product->stok); ?></span>
                            <?php else: ?>
                                <span class="text-gray-800 font-medium"><?php echo e($product->stok); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-right text-gray-800">Rp <?php echo e(number_format($product->harga_hv, 0, ',', '.')); ?></td>
                        <td class="py-3 px-4 text-right text-gray-800">Rp <?php echo e(number_format($product->harga_resep, 0, ',', '.')); ?></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="<?php echo e(route('products.show', $product)); ?>" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('products.edit', $product)); ?>" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('products.destroy', $product)); ?>" class="inline"
                                      onsubmit="return confirm('Yakin hapus barang ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                            <p>Belum ada data barang</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($products->withQueryString()->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/products/index.blade.php ENDPATH**/ ?>