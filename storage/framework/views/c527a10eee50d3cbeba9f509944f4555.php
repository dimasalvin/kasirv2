

<?php $__env->startSection('title', 'Manajemen User'); ?>
<?php $__env->startSection('page-title', 'Manajemen User'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola akun pengguna sistem</p>
        <a href="<?php echo e(route('users.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah User
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-4">Nama</th>
                    <th class="text-left py-3 px-4">Username</th>
                    <th class="text-left py-3 px-4">Email</th>
                    <th class="text-center py-3 px-4">Role</th>
                    <th class="text-center py-3 px-4">Status</th>
                    <th class="text-center py-3 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium"><?php echo e($user->name); ?></td>
                    <td class="py-3 px-4 font-mono text-xs"><?php echo e($user->username); ?></td>
                    <td class="py-3 px-4 text-gray-600"><?php echo e($user->email); ?></td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            <?php echo e($user->role == 'admin' ? 'bg-red-100 text-red-700' : ($user->role == 'apoteker' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700')); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $user->role))); ?>

                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <?php if($user->is_active): ?>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="<?php echo e(route('users.edit', $user)); ?>" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><i class="fas fa-edit"></i></a>
                        <?php if($user->id !== auth()->id()): ?>
                        <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" class="inline" onsubmit="return confirm('Hapus user ini?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="px-4 py-3 border-t"><?php echo e($users->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/users/index.blade.php ENDPATH**/ ?>