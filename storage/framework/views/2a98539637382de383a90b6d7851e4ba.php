<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Apotek POS'); ?> - <?php echo e(config('app.name')); ?></title>
    
    <!-- Tailwind CSS (Vite Build) -->
    <?php if(file_exists(public_path('build/manifest.json'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    <?php endif; ?>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Theme: Apply sebelum render untuk menghindari flash -->
    <script>
        (function() {
            const theme = localStorage.getItem('apotek_theme') || 'light';
            const accent = localStorage.getItem('apotek_accent') || 'indigo';
            if (theme === 'dark') document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-accent', accent);
        })();
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            transform: translateX(4px);
        }

        /* ===== AKSEN WARNA ===== */
        :root { --accent: 99, 102, 241; } /* indigo default */
        [data-accent="indigo"] { --accent: 99, 102, 241; }
        [data-accent="blue"] { --accent: 59, 130, 246; }
        [data-accent="emerald"] { --accent: 16, 185, 129; }
        [data-accent="rose"] { --accent: 244, 63, 94; }
        [data-accent="amber"] { --accent: 245, 158, 11; }
        [data-accent="violet"] { --accent: 139, 92, 246; }

        .accent-bg { background-color: rgb(var(--accent)) !important; }
        .accent-text { color: rgb(var(--accent)) !important; }
        .accent-border { border-color: rgb(var(--accent)) !important; }
        .accent-bg-light { background-color: rgba(var(--accent), 0.1) !important; }
        .accent-ring:focus { --tw-ring-color: rgba(var(--accent), 0.5) !important; }

        /* Hover item — menggunakan warna aksen */
        .item-hover {
            transition: background-color 0.15s ease;
        }
        .item-hover:hover {
            background-color: rgba(var(--accent), 0.12) !important;
        }
        .item-hover:hover p,
        .item-hover:hover span:not([class*="bg-"]) {
            color: rgb(var(--accent)) !important;
        }

        /* ===== DARK MODE ===== */
        /* Base */
        .dark body, .dark .bg-gray-50 { background-color: #111827 !important; }
        .dark .bg-white { background-color: #1f2937 !important; color: #f3f4f6 !important; }
        .dark .bg-gray-50 { background-color: #1a2332 !important; }
        .dark .bg-gray-100 { background-color: #374151 !important; color: #e5e7eb !important; }
        .dark .bg-gray-200 { background-color: #4b5563 !important; color: #f3f4f6 !important; }

        /* Borders */
        .dark .border, .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-50 { border-color: #374151 !important; }
        .dark .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }

        /* Text */
        .dark .text-gray-800, .dark .text-gray-900 { color: #f3f4f6 !important; }
        .dark .text-gray-700 { color: #d1d5db !important; }
        .dark .text-gray-600, .dark .text-gray-500 { color: #9ca3af !important; }
        .dark .text-gray-400 { color: #6b7280 !important; }
        .dark .text-gray-300 { color: #9ca3af !important; }
        .dark h1, .dark h2, .dark h3, .dark h4, .dark label, .dark th { color: #e5e7eb !important; }

        /* Tables */
        .dark table thead { background-color: #1a2332 !important; }
        .dark table tbody tr { border-color: #374151 !important; }
        .dark table tbody tr:hover { background-color: #263040 !important; }
        .dark table td { color: #d1d5db !important; }

        /* Inputs */
        .dark input, .dark select, .dark textarea {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        .dark input::placeholder, .dark textarea::placeholder { color: #6b7280 !important; }

        /* Buttons */
        .dark button.bg-gray-100, .dark button.bg-gray-200 {
            background-color: #374151 !important;
            color: #e5e7eb !important;
        }
        .dark button.bg-gray-100:hover, .dark button.bg-gray-200:hover {
            background-color: #4b5563 !important;
        }

        /* ===== COLORED BADGES & BOXES — tetap terbaca di dark ===== */
        .dark .bg-green-100 { background-color: rgba(22, 163, 74, 0.2) !important; }
        .dark .bg-red-100 { background-color: rgba(220, 38, 38, 0.2) !important; }
        .dark .bg-blue-100 { background-color: rgba(59, 130, 246, 0.2) !important; }
        .dark .bg-yellow-100 { background-color: rgba(234, 179, 8, 0.2) !important; }
        .dark .bg-purple-100 { background-color: rgba(147, 51, 234, 0.2) !important; }
        .dark .bg-indigo-100 { background-color: rgba(99, 102, 241, 0.2) !important; }
        .dark .bg-orange-100 { background-color: rgba(249, 115, 22, 0.2) !important; }

        .dark .bg-green-50 { background-color: rgba(22, 163, 74, 0.1) !important; }
        .dark .bg-red-50 { background-color: rgba(220, 38, 38, 0.1) !important; }
        .dark .bg-blue-50 { background-color: rgba(59, 130, 246, 0.1) !important; }
        .dark .bg-yellow-50 { background-color: rgba(234, 179, 8, 0.1) !important; }
        .dark .bg-purple-50 { background-color: rgba(147, 51, 234, 0.1) !important; }
        .dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.15) !important; }
        .dark .bg-orange-50 { background-color: rgba(249, 115, 22, 0.1) !important; }

        /* Badge text tetap terang */
        .dark .text-green-700 { color: #4ade80 !important; }
        .dark .text-red-700 { color: #f87171 !important; }
        .dark .text-blue-700 { color: #60a5fa !important; }
        .dark .text-yellow-700 { color: #fbbf24 !important; }
        .dark .text-purple-700, .dark .text-purple-800 { color: #c084fc !important; }
        .dark .text-indigo-700, .dark .text-indigo-800 { color: #a5b4fc !important; }
        .dark .text-orange-700 { color: #fb923c !important; }

        /* Colored text preserve */
        .dark .text-indigo-600 { color: #818cf8 !important; }
        .dark .text-green-600 { color: #4ade80 !important; }
        .dark .text-red-600 { color: #f87171 !important; }
        .dark .text-blue-600 { color: #60a5fa !important; }
        .dark .text-purple-600 { color: #c084fc !important; }
        .dark .text-yellow-600 { color: #fbbf24 !important; }

        /* ===== PRINT STYLES ===== */
        @media print {
            /* FORCE LIGHT MODE saat print — override semua dark mode */
            .dark body, .dark .bg-gray-50 { background-color: white !important; }
            .dark .bg-white { background-color: white !important; color: #1f2937 !important; }
            .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background-color: #f9fafb !important; color: #1f2937 !important; }
            .dark .text-gray-800, .dark .text-gray-900, .dark .text-gray-700,
            .dark .text-gray-600, .dark .text-gray-500, .dark h1, .dark h2, 
            .dark h3, .dark h4, .dark label, .dark th, .dark table td,
            .dark p, .dark span { color: #1f2937 !important; }
            .dark .border, .dark .border-gray-100, .dark .border-gray-200 { border-color: #e5e7eb !important; }
            .dark input, .dark select, .dark textarea { background-color: white !important; color: #1f2937 !important; border-color: #d1d5db !important; }
            .dark table thead { background-color: #f3f4f6 !important; }
            .dark .bg-green-100, .dark .bg-red-100, .dark .bg-blue-100,
            .dark .bg-yellow-100, .dark .bg-purple-100, .dark .bg-indigo-100 {
                background-color: initial !important;
            }
            .dark .text-green-700, .dark .text-red-700, .dark .text-blue-700,
            .dark .text-indigo-700, .dark .text-purple-700 { color: initial !important; }
            .dark .text-indigo-600 { color: #4f46e5 !important; }
            .dark .text-green-600 { color: #16a34a !important; }
            .dark .text-red-600 { color: #dc2626 !important; }

            @page {
                margin: 10mm;
                size: A4 portrait;
            }

            /* Sembunyikan sidebar, navbar, filter form, flash messages */
            .no-print,
            aside[class],
            .flash-message { 
                display: none !important; 
            }

            /* Tampilkan print header */
            .print-header,
            .hidden.print\\:block {
                display: block !important;
            }

            /* Reset layout */
            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 12px !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .min-h-full {
                display: block !important;
            }

            /* Main content full width tanpa margin sidebar */
            .flex-1.flex.flex-col,
            [class*="ml-64"],
            [class*="ml-16"] {
                margin-left: 0 !important;
                width: 100% !important;
            }

            main {
                padding: 5px !important;
            }

            /* Card styling */
            .bg-white {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            /* Preserve warna badge */
            .bg-green-100, .bg-red-100, .bg-blue-100, .bg-yellow-100, 
            .bg-purple-100, .bg-indigo-100, .bg-orange-100, .bg-gray-100,
            .bg-green-50, .bg-red-50, .bg-blue-50, .bg-indigo-50, .bg-purple-50 {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* ===== TABEL RAPI ===== */
            table { 
                page-break-inside: auto;
                width: 100% !important;
                font-size: 10px !important;
                border-collapse: collapse !important;
            }
            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
            th, td {
                padding: 3px 4px !important;
                border-bottom: 1px solid #e5e7eb !important;
            }
            /* Pastikan angka tidak wrap */
            .whitespace-nowrap {
                white-space: nowrap !important;
            }

            /* Summary cards grid tetap rapi */
            .grid {
                display: grid !important;
            }

            /* Overflow visible saat print */
            .overflow-x-auto, .overflow-hidden {
                overflow: visible !important;
            }

            /* Rounded corners kecilkan saat print */
            .rounded-xl, .rounded-lg {
                border-radius: 4px !important;
            }

        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-200"
      x-data="appShell()" x-init="initApp()">
    <div class="min-h-full flex">
        <!-- Sidebar -->
        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'ml-64' : 'ml-16'">
            <!-- Top Navbar -->
            <?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!-- Page Content -->
            <main class="flex-1 p-6" id="main-content">
                <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
                <nav class="mb-4 text-sm no-print" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-gray-500">
                        <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-gray-700"><i class="fas fa-home"></i></a></li>
                        <?php echo $__env->yieldContent('breadcrumb'); ?>
                    </ol>
                </nav>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Toast Notifications (Fixed, tidak menggeser konten) -->
    <div class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm w-full pointer-events-none">
        <?php if(session('success')): ?>
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100); setTimeout(() => show = false, 4000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border border-green-200 shadow-lg rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <p class="text-sm text-gray-800"><?php echo e(session('success')); ?></p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 ml-3 flex-shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100); setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border border-red-200 shadow-lg rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <i class="fas fa-exclamation text-red-600 text-sm"></i>
                </div>
                <p class="text-sm text-gray-800"><?php echo e(session('error')); ?></p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 ml-3 flex-shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100); setTimeout(() => show = false, 6000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border border-red-200 shadow-lg rounded-lg px-4 py-3">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800 mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside text-xs text-gray-600">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 ml-2 flex-shrink-0">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Global: App Shell (Theme, Scroll, Accent) -->
    <script>
        function appShell() {
            return {
                sidebarOpen: true,
                darkMode: localStorage.getItem('apotek_theme') === 'dark',
                accent: localStorage.getItem('apotek_accent') || 'indigo',
                showThemePanel: false,

                accents: [
                    { id: 'indigo', name: 'Indigo', color: '#6366f1' },
                    { id: 'blue', name: 'Biru', color: '#3b82f6' },
                    { id: 'emerald', name: 'Hijau', color: '#10b981' },
                    { id: 'rose', name: 'Merah', color: '#f43f5e' },
                    { id: 'amber', name: 'Kuning', color: '#f59e0b' },
                    { id: 'violet', name: 'Ungu', color: '#8b5cf6' },
                ],

                initApp() {
                    // Restore main scroll position
                    const savedScroll = sessionStorage.getItem('apotek_scroll_' + window.location.pathname);
                    if (savedScroll) {
                        setTimeout(() => window.scrollTo(0, parseInt(savedScroll)), 50);
                    }

                    // Restore sidebar scroll position
                    const sidebarNav = document.querySelector('aside nav');
                    if (sidebarNav) {
                        const savedSidebarScroll = sessionStorage.getItem('apotek_sidebar_scroll');
                        if (savedSidebarScroll) {
                            sidebarNav.scrollTop = parseInt(savedSidebarScroll);
                        }
                    }

                    // Save scroll sebelum navigasi
                    document.querySelectorAll('a[href]').forEach(link => {
                        if (link.hostname === window.location.hostname) {
                            link.addEventListener('click', () => {
                                sessionStorage.setItem('apotek_scroll_' + window.location.pathname, window.scrollY);
                                // Save sidebar scroll
                                const nav = document.querySelector('aside nav');
                                if (nav) {
                                    sessionStorage.setItem('apotek_sidebar_scroll', nav.scrollTop);
                                }
                            });
                        }
                    });

                    // Apply theme
                    this.applyTheme();
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('apotek_theme', this.darkMode ? 'dark' : 'light');
                    this.applyTheme();
                },

                setAccent(accentId) {
                    this.accent = accentId;
                    localStorage.setItem('apotek_accent', accentId);
                    document.documentElement.setAttribute('data-accent', accentId);
                },

                applyTheme() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    document.documentElement.setAttribute('data-accent', this.accent);
                }
            }
        }
    </script>

    <!-- Global: Format Ribuan Helper -->
    <script>
        // Format angka ke ribuan: 1000 -> 1,000
        function formatRibuan(angka) {
            if (!angka && angka !== 0) return '';
            return angka.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Parse dari format ribuan ke angka: 1,000 -> 1000
        function parseRibuan(str) {
            if (!str) return 0;
            return parseInt(str.toString().replace(/,/g, ''), 10) || 0;
        }

        // Alpine.js magic untuk money input
        document.addEventListener('alpine:init', () => {
            // Directive x-money: otomatis format ribuan
            Alpine.directive('money', (el, { expression }, { evaluate, effect }) => {
                // Set input ke text agar bisa format
                el.setAttribute('type', 'text');
                el.setAttribute('inputmode', 'numeric');

                const getVal = () => evaluate(expression);
                const setVal = (v) => evaluate(`${expression} = ${v}`);

                // Format saat init
                effect(() => {
                    const val = getVal();
                    const formatted = formatRibuan(val);
                    if (el !== document.activeElement) {
                        el.value = formatted;
                    }
                });

                // Format saat input
                el.addEventListener('input', (e) => {
                    const raw = parseRibuan(e.target.value);
                    setVal(raw);
                    // Simpan posisi cursor
                    const pos = e.target.selectionStart;
                    const oldLen = e.target.value.length;
                    e.target.value = formatRibuan(raw);
                    const newLen = e.target.value.length;
                    const newPos = pos + (newLen - oldLen);
                    e.target.setSelectionRange(newPos, newPos);
                });

                // Format saat blur
                el.addEventListener('blur', () => {
                    el.value = formatRibuan(getVal());
                });

                // Clear saat focus jika 0
                el.addEventListener('focus', () => {
                    if (getVal() === 0) el.value = '';
                });

                // Strip koma saat form submit (agar server terima angka asli)
                const form = el.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        el.value = getVal().toString();
                    });
                }
            });
        });

        // Auto-format semua input dengan class "money-format" (untuk form non-Alpine)
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.money-format').forEach(el => {
                el.setAttribute('type', 'text');
                el.setAttribute('inputmode', 'numeric');

                // Format initial value
                if (el.value) {
                    el.value = formatRibuan(el.value);
                }

                el.addEventListener('input', (e) => {
                    const raw = parseRibuan(e.target.value);
                    const pos = e.target.selectionStart;
                    const oldLen = e.target.value.length;
                    e.target.value = formatRibuan(raw);
                    const newLen = e.target.value.length;
                    e.target.setSelectionRange(pos + (newLen - oldLen), pos + (newLen - oldLen));
                });

                el.addEventListener('blur', () => {
                    el.value = formatRibuan(parseRibuan(el.value));
                });

                // Saat submit form, kembalikan ke angka asli
                el.closest('form')?.addEventListener('submit', () => {
                    el.value = parseRibuan(el.value);
                });
            });
        });
    </script>

    <!-- Global: Unsaved Changes Warning -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Track form changes untuk form dengan class "track-changes"
            document.querySelectorAll('form.track-changes').forEach(form => {
                let formChanged = false;
                
                form.addEventListener('input', () => { formChanged = true; });
                form.addEventListener('change', () => { formChanged = true; });
                form.addEventListener('submit', () => { formChanged = false; });

                window.addEventListener('beforeunload', (e) => {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                    }
                });
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\WORK\other\kasir_test\resources\views/layouts/app.blade.php ENDPATH**/ ?>