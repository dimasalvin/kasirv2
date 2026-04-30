<!-- Top Navbar -->
<header class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center justify-between px-6 no-print">
    <div class="flex items-center space-x-4">
        <h2 class="text-lg font-semibold text-gray-800"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h2>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Notifikasi Stok -->
        <?php
            $stokAlert = \App\Models\Product::stokMenipis()->where('is_active', true)->count();
        ?>
        <?php if($stokAlert > 0): ?>
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="fas fa-bell text-lg"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    <?php echo e($stokAlert); ?>

                </span>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                <div class="p-3 border-b border-gray-100">
                    <h3 class="font-semibold text-sm text-gray-700">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                        Stok Menipis (<?php echo e($stokAlert); ?> item)
                    </h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <?php $__currentLoopData = \App\Models\Product::stokMenipis()->where('is_active', true)->limit(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-3 py-2 border-b border-gray-50 item-hover">
                        <p class="text-sm font-medium text-gray-800"><?php echo e($item->nama_barang); ?></p>
                        <p class="text-xs text-red-600">Stok: <?php echo e($item->stok); ?> (Min: <?php echo e($item->stok_minimum); ?>)</p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a href="<?php echo e(route('products.index', ['stok_menipis' => 1])); ?>" class="block p-2 text-center text-sm text-indigo-600 item-hover">
                    Lihat Semua
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Theme Toggle -->
        <div class="relative">
            <button @click="showThemePanel = !showThemePanel" class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none" title="Tema & Warna">
                <i class="fas fa-palette text-lg"></i>
            </button>

            <!-- Theme Panel -->
            <div x-show="showThemePanel" @click.away="showThemePanel = false" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 p-4">
                
                <!-- Dark Mode Toggle -->
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Mode Tampilan</p>
                    <div class="flex gap-2">
                        <button @click="darkMode = false; localStorage.setItem('apotek_theme', 'light'); applyTheme();" 
                                :class="!darkMode ? 'ring-2 ring-offset-1 accent-border bg-yellow-50 text-yellow-700' : 'text-gray-600 dark:text-gray-300'"
                                class="flex-1 py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 text-center text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-sun mr-1"></i> Light
                        </button>
                        <button @click="darkMode = true; localStorage.setItem('apotek_theme', 'dark'); applyTheme();"
                                :class="darkMode ? 'ring-2 ring-offset-1 accent-border bg-gray-800 text-blue-300' : 'text-gray-600 dark:text-gray-300'"
                                class="flex-1 py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 text-center text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-moon mr-1"></i> Dark
                        </button>
                    </div>
                </div>

                <!-- Accent Color -->
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Warna Aksen</p>
                    <div class="grid grid-cols-6 gap-2">
                        <template x-for="a in accents" :key="a.id">
                            <button @click="setAccent(a.id)" 
                                    :title="a.name"
                                    :style="'background-color:' + a.color"
                                    :class="accent === a.id ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : ''"
                                    class="w-8 h-8 rounded-full transition-transform hover:scale-110 shadow-sm">
                                <i x-show="accent === a.id" class="fas fa-check text-white text-xs"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dark Mode Quick Toggle -->
        <button @click="toggleDarkMode()" class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none" title="Toggle Dark Mode">
            <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon'"></i>
        </button>

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                <div class="w-8 h-8 accent-bg-light rounded-full flex items-center justify-center">
                    <i class="fas fa-user accent-text text-sm"></i>
                </div>
                <div class="text-left hidden md:block">
                    <p class="text-sm font-medium"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo e(str_replace('_', ' ', auth()->user()->role)); ?></p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                <div class="py-1">
                    <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="<?php echo e(route('manual')); ?>" target="_blank" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-book mr-2"></i> Buku Manual
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\WORK\other\kasir_test\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>