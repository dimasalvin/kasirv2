

<?php $__env->startSection('title', 'Audit Log'); ?>
<?php $__env->startSection('page-title', 'Audit Log Aktivitas'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Aksi</label>
                <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="login" <?php echo e(request('action') == 'login' ? 'selected' : ''); ?>>Login</option>
                    <option value="logout" <?php echo e(request('action') == 'logout' ? 'selected' : ''); ?>>Logout</option>
                    <option value="create" <?php echo e(request('action') == 'create' ? 'selected' : ''); ?>>Create</option>
                    <option value="update" <?php echo e(request('action') == 'update' ? 'selected' : ''); ?>>Update</option>
                    <option value="delete" <?php echo e(request('action') == 'delete' ? 'selected' : ''); ?>>Delete</option>
                    <option value="void" <?php echo e(request('action') == 'void' ? 'selected' : ''); ?>>Void</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1">User</label>
                <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3">Waktu</th>
                        <th class="text-left py-2 px-3">User</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                        <th class="text-left py-2 px-3">Deskripsi</th>
                        <th class="text-left py-2 px-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3 whitespace-nowrap text-gray-500 text-xs"><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></td>
                        <td class="py-2 px-3"><?php echo e($log->user->name ?? 'System'); ?></td>
                        <td class="py-2 px-3 text-center">
                            <?php
                                $colors = ['login' => 'bg-blue-100 text-blue-700', 'logout' => 'bg-gray-100 text-gray-700', 'create' => 'bg-green-100 text-green-700', 'update' => 'bg-yellow-100 text-yellow-700', 'delete' => 'bg-red-100 text-red-700', 'void' => 'bg-red-100 text-red-700'];
                            ?>
                            <span class="px-2 py-0.5 rounded text-xs <?php echo e($colors[$log->action] ?? 'bg-gray-100 text-gray-700'); ?>"><?php echo e(ucfirst($log->action)); ?></span>
                        </td>
                        <td class="py-2 px-3"><?php echo e($log->description); ?></td>
                        <td class="py-2 px-3 font-mono text-xs text-gray-400"><?php echo e($log->ip_address); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada log</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t"><?php echo e($logs->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/audit-log/index.blade.php ENDPATH**/ ?>