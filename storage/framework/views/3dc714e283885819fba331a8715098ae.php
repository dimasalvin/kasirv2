

<?php $__env->startSection('title', 'Detail Barang'); ?>
<?php $__env->startSection('page-title', 'Detail: ' . $product->nama_barang); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li><span class="mx-1">/</span></li>
<li><a href="<?php echo e(route('products.index')); ?>" class="hover:text-gray-700">Data Barang</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium"><?php echo e($product->nama_barang); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Product Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800"><?php echo e($product->nama_barang); ?></h2>
                <p class="text-sm text-gray-500"><?php echo e($product->kode_barang); ?> | Barcode: <?php echo e($product->barcode ?? '-'); ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('products.edit', $product)); ?>" class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-sm hover:bg-yellow-200">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Grup</p>
                <p class="font-semibold text-sm"><?php echo e($product->grup_label); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Satuan</p>
                <p class="font-semibold text-sm"><?php echo e(ucfirst($product->satuan)); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Pabrik</p>
                <p class="font-semibold text-sm"><?php echo e($product->pabrik ?? '-'); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Kategori</p>
                <p class="font-semibold text-sm"><?php echo e($product->category->nama ?? '-'); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Stok</p>
                <p class="font-semibold text-sm <?php echo e($product->stok <= $product->stok_minimum ? 'text-red-600' : 'text-green-600'); ?>">
                    <?php echo e($product->stok); ?> <?php echo e($product->satuan); ?>

                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Stok Minimum</p>
                <p class="font-semibold text-sm"><?php echo e($product->stok_minimum); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Expired</p>
                <p class="font-semibold text-sm"><?php echo e($product->expired_date ? $product->expired_date->format('d/m/Y') : '-'); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Lokasi Rak</p>
                <p class="font-semibold text-sm"><?php echo e($product->lokasi_rak ?? '-'); ?></p>
            </div>
        </div>

        <!-- Harga -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xs text-blue-600">Harga Beli (HNA)</p>
                <p class="font-bold text-blue-800">Rp <?php echo e(number_format($product->harga_beli, 0, ',', '.')); ?></p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-xs text-green-600">Harga Jual</p>
                <p class="font-bold text-green-800">Rp <?php echo e(number_format($product->harga_jual, 0, ',', '.')); ?></p>
            </div>
            <div class="bg-indigo-50 rounded-lg p-3">
                <p class="text-xs text-indigo-600">Harga HV</p>
                <p class="font-bold text-indigo-800">Rp <?php echo e(number_format($product->harga_hv, 0, ',', '.')); ?></p>
            </div>
            <div class="bg-purple-50 rounded-lg p-3">
                <p class="text-xs text-purple-600">Harga Resep</p>
                <p class="font-bold text-purple-800">Rp <?php echo e(number_format($product->harga_resep, 0, ',', '.')); ?></p>
            </div>
        </div>
    </div>

    <!-- Riwayat Harga -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-green-500 mr-2"></i> Riwayat Perubahan Harga
        </h3>
        <?php if($priceHistories->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3 text-gray-600">Tanggal</th>
                        <th class="text-right py-2 px-3 text-gray-600">HNA Lama</th>
                        <th class="text-right py-2 px-3 text-gray-600">HNA Baru</th>
                        <th class="text-center py-2 px-3 text-gray-600">Perubahan</th>
                        <th class="text-left py-2 px-3 text-gray-600">Referensi</th>
                        <th class="text-left py-2 px-3 text-gray-600">User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $priceHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3"><?php echo e($ph->created_at->format('d/m/Y H:i')); ?></td>
                        <td class="py-2 px-3 text-right">Rp <?php echo e(number_format($ph->harga_beli_lama, 0, ',', '.')); ?></td>
                        <td class="py-2 px-3 text-right font-medium">Rp <?php echo e(number_format($ph->harga_beli_baru, 0, ',', '.')); ?></td>
                        <td class="py-2 px-3 text-center">
                            <?php $diff = $ph->harga_beli_baru - $ph->harga_beli_lama; ?>
                            <span class="px-2 py-0.5 rounded text-xs <?php echo e($diff > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                                <?php echo e($diff > 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($diff), 0, ',', '.')); ?>

                            </span>
                        </td>
                        <td class="py-2 px-3 font-mono text-xs"><?php echo e($ph->referensi ?? '-'); ?></td>
                        <td class="py-2 px-3 text-gray-600"><?php echo e($ph->user->name ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-sm text-gray-500 text-center py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada perubahan harga. Riwayat akan tercatat otomatis saat ada pembelian dengan harga berbeda.</p>
        <?php endif; ?>
    </div>

    <!-- Kartu Stok -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-history text-indigo-500 mr-2"></i> Kartu Stok
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3 text-gray-600">Tanggal</th>
                        <th class="text-center py-2 px-3 text-gray-600">Tipe</th>
                        <th class="text-center py-2 px-3 text-gray-600">Jumlah</th>
                        <th class="text-center py-2 px-3 text-gray-600">Stok Sebelum</th>
                        <th class="text-center py-2 px-3 text-gray-600">Stok Sesudah</th>
                        <th class="text-left py-2 px-3 text-gray-600">Referensi</th>
                        <th class="text-left py-2 px-3 text-gray-600">Keterangan</th>
                        <th class="text-left py-2 px-3 text-gray-600">User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $stockCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3"><?php echo e($card->created_at->format('d/m/Y H:i')); ?></td>
                        <td class="py-2 px-3 text-center">
                            <?php if($card->tipe == 'masuk'): ?>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Masuk</span>
                            <?php elseif($card->tipe == 'keluar'): ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Keluar</span>
                            <?php elseif($card->tipe == 'opname'): ?>
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Opname</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs">Retur</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-3 text-center font-medium"><?php echo e($card->jumlah); ?></td>
                        <td class="py-2 px-3 text-center"><?php echo e($card->stok_sebelum); ?></td>
                        <td class="py-2 px-3 text-center font-medium"><?php echo e($card->stok_sesudah); ?></td>
                        <td class="py-2 px-3 font-mono text-xs"><?php echo e($card->referensi ?? '-'); ?></td>
                        <td class="py-2 px-3 text-gray-600"><?php echo e($card->keterangan ?? '-'); ?></td>
                        <td class="py-2 px-3 text-gray-600"><?php echo e($card->user->name ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">Belum ada riwayat stok</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <?php echo e($stockCards->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/products/show.blade.php ENDPATH**/ ?>