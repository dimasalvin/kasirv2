<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 bg-gradient-to-b from-gray-900 to-gray-800 text-white transition-all duration-300 shadow-xl"
       :class="sidebarOpen ? 'w-64' : 'w-16'">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700">
        <div class="flex items-center space-x-3" x-show="sidebarOpen">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <i class="fas fa-prescription-bottle-medical accent-text"></i>
            </div>
            <span class="font-bold text-lg">Apotek POS</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white p-1">
            <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="px-2 space-y-1 overflow-y-auto pt-4 pb-24" style="height: calc(100vh - 4rem);">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-tachometer-alt w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
        </a>

        <!-- POS Kasir -->
        <a href="{{ route('pos') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pos') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Kasir / POS</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-3" x-show="sidebarOpen"></div>
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" x-show="sidebarOpen">Inventori</p>

        <!-- Stock / Produk -->
        <a href="{{ route('products.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('products.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-pills w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Data Barang</span>
        </a>

        <!-- Kategori -->
        <a href="{{ route('categories.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('categories.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-tags w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Kategori</span>
        </a>

        <!-- Stock Opname -->
        <a href="{{ route('stock-opname.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('stock-opname.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-clipboard-check w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Stock Opname</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-3" x-show="sidebarOpen"></div>
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" x-show="sidebarOpen">Transaksi</p>

        <!-- Supplier -->
        <a href="{{ route('suppliers.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-truck w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Supplier (PBF)</span>
        </a>

        <!-- Pembelian -->
        <a href="{{ route('purchases.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('purchases.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-shopping-cart w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Pembelian</span>
        </a>

        <!-- Penjualan -->
        <a href="{{ route('sales.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('sales.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-receipt w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Penjualan</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-3" x-show="sidebarOpen"></div>
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" x-show="sidebarOpen">Laporan</p>

        <!-- Laporan Penjualan -->
        <a href="{{ route('reports.sales') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.sales') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Lap. Penjualan</span>
        </a>

        <!-- Closing Kasir -->
        <a href="{{ route('reports.closing-kasir') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.closing-kasir') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-calculator w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Closing Kasir</span>
        </a>

        <!-- Laporan Kas -->
        <a href="{{ route('reports.cash-flow') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.cash-flow') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-money-bill-wave w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Laporan Kas</span>
        </a>

        <!-- Produk Terlaris -->
        <a href="{{ route('reports.top-products') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.top-products') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-trophy w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Produk Terlaris</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-3" x-show="sidebarOpen"></div>
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" x-show="sidebarOpen">Lainnya</p>

        <!-- Absensi -->
        <a href="{{ route('attendance.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('attendance.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-fingerprint w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Absensi</span>
        </a>

        <!-- User Management (Admin Only) -->
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-users-cog w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Manajemen User</span>
        </a>

        <!-- Audit Log (Admin Only) -->
        <a href="{{ route('audit-log.index') }}" 
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('audit-log.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Audit Log</span>
        </a>

        <!-- Pengaturan (Admin Only) -->
        <a href="{{ route('settings.index') }}"
           class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'accent-bg text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-cog w-5 text-center"></i>
            <span class="ml-3" x-show="sidebarOpen">Pengaturan</span>
        </a>
        @endif
    </nav>
</aside>
