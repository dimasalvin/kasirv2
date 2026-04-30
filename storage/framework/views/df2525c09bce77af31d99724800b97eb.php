

<?php $__env->startSection('title', 'Absensi'); ?>
<?php $__env->startSection('page-title', 'Absensi Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Clock In/Out Buttons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Absensi Hari Ini</h3>
                <p class="text-sm text-gray-500"><?php echo e(now()->format('l, d F Y')); ?></p>
            </div>
            <div class="flex space-x-3">
                <form method="POST" action="<?php echo e(route('attendance.clock-in')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i> Absen Masuk
                    </button>
                </form>
                <form method="POST" action="<?php echo e(route('attendance.clock-out')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex gap-3 items-end">
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo e(request('tanggal', date('Y-m-d'))); ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-search mr-1"></i> Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                        <th class="text-center py-3 px-4">Jam Masuk</th>
                        <th class="text-center py-3 px-4">Jam Pulang</th>
                        <th class="text-center py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50">
                        <td class="py-3 px-4 font-medium"><?php echo e($att->user->name); ?></td>
                        <td class="py-3 px-4"><?php echo e($att->tanggal->format('d/m/Y')); ?></td>
                        <td class="py-3 px-4 text-center"><?php echo e($att->jam_masuk ?? '-'); ?></td>
                        <td class="py-3 px-4 text-center"><?php echo e($att->jam_pulang ?? '-'); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if($att->status == 'hadir'): ?>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Hadir</span>
                            <?php elseif($att->status == 'izin'): ?>
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Izin</span>
                            <?php elseif($att->status == 'sakit'): ?>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Sakit</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Alpha</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($att->keterangan ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada data absensi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t"><?php echo e($attendances->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/attendance/index.blade.php ENDPATH**/ ?>