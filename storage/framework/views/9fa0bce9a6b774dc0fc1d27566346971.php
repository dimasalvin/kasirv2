

<?php $__env->startSection('title', 'Pengaturan'); ?>
<?php $__env->startSection('page-title', 'Pengaturan Sistem'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Pengaturan Harga -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">
            <i class="fas fa-calculator text-indigo-500 mr-2"></i>Pengaturan Harga Otomatis
        </h3>
        <p class="text-sm text-gray-500 mb-4">Atur persentase markup untuk kalkulasi harga jual otomatis dari HNA (Harga Netto Apotek).</p>

        <form method="POST" action="<?php echo e(route('settings.update-pricing')); ?>" x-data="pricingSettings()">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- PPN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPN (%)</label>
                    <input type="number" name="ppn_persen" x-model.number="ppn" @input="recalculate()" step="0.1" min="0" max="100"
                           value="<?php echo e($pricing['ppn_persen']); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Harga Jual = HNA + PPN%</p>
                </div>

                <!-- Markup HV -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Markup HV (%)</label>
                    <input type="number" name="markup_hv_persen" x-model.number="markupHv" @input="recalculate()" step="0.1" min="0" max="100"
                           value="<?php echo e($pricing['markup_hv_persen']); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Harga HV = Harga Jual + Markup%</p>
                </div>

                <!-- Markup Resep -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Markup Resep (%)</label>
                    <input type="number" name="markup_resep_persen" x-model.number="markupResep" @input="recalculate()" step="0.1" min="0" max="100"
                           value="<?php echo e($pricing['markup_resep_persen']); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Harga Resep = Harga HV + Markup%</p>
                </div>
            </div>

            <!-- Preview Simulasi -->
            <div class="mt-6 bg-indigo-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-indigo-800 mb-3"><i class="fas fa-eye mr-1"></i> Preview Simulasi (HNA = Rp 10,000)</h4>
                <div class="grid grid-cols-4 gap-3 text-center">
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500">HNA</p>
                        <p class="text-sm font-bold text-gray-800">Rp 10,000</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500">Harga Jual</p>
                        <p class="text-sm font-bold text-green-600" x-text="'Rp ' + formatNumber(hargaJual)"></p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500">Harga HV</p>
                        <p class="text-sm font-bold text-indigo-600" x-text="'Rp ' + formatNumber(hargaHV)"></p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500">Harga Resep</p>
                        <p class="text-sm font-bold text-purple-600" x-text="'Rp ' + formatNumber(hargaResep)"></p>
                    </div>
                </div>
                <p class="text-xs text-indigo-600 mt-2 text-center">
                    Formula: HNA → ×<span x-text="(1 + ppn/100).toFixed(3)"></span> → ×<span x-text="(1 + markupHv/100).toFixed(3)"></span> → ×<span x-text="(1 + markupResep/100).toFixed(3)"></span>
                </p>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function pricingSettings() {
    return {
        ppn: <?php echo e($pricing['ppn_persen']); ?>,
        markupHv: <?php echo e($pricing['markup_hv_persen']); ?>,
        markupResep: <?php echo e($pricing['markup_resep_persen']); ?>,
        hargaJual: 0,
        hargaHV: 0,
        hargaResep: 0,
        init() { this.recalculate(); },
        recalculate() {
            const hna = 10000;
            this.hargaJual = Math.round(hna * (1 + this.ppn / 100));
            this.hargaHV = Math.round(this.hargaJual * (1 + this.markupHv / 100));
            this.hargaResep = Math.round(this.hargaHV * (1 + this.markupResep / 100));
        },
        formatNumber(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/settings/index.blade.php ENDPATH**/ ?>