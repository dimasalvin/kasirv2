<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 2px; }
        h2 { text-align: center; font-size: 13px; margin-bottom: 5px; color: #555; }
        .meta { text-align: center; font-size: 9px; color: #888; margin-bottom: 15px; }
        .summary { display: table; width: 100%; margin-bottom: 15px; }
        .summary-item { display: table-cell; text-align: center; padding: 8px; border: 1px solid #ddd; }
        .summary-item .label { font-size: 9px; color: #666; }
        .summary-item .value { font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f3f4f6; padding: 5px 4px; text-align: left; border-bottom: 2px solid #ddd; font-size: 9px; }
        td { padding: 4px; border-bottom: 1px solid #eee; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <h1>APOTEK POS</h1>
    <h2>Laporan Penjualan</h2>
    <div class="meta">
        Periode: <?php echo e(\Carbon\Carbon::parse($tanggalDari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y')); ?>

        | Dicetak: <?php echo e(now()->format('d/m/Y H:i')); ?>

    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Penjualan</div>
            <div class="value">Rp <?php echo e(number_format($totalPenjualan, 0, ',', '.')); ?></div>
        </div>
        <div class="summary-item">
            <div class="label">Tunai</div>
            <div class="value">Rp <?php echo e(number_format($totalTunai, 0, ',', '.')); ?></div>
        </div>
        <div class="summary-item">
            <div class="label">Non Tunai</div>
            <div class="value">Rp <?php echo e(number_format($totalNonTunai, 0, ',', '.')); ?></div>
        </div>
        <div class="summary-item">
            <div class="label">Jumlah Transaksi</div>
            <div class="value"><?php echo e($jumlahTransaksi); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>No. Nota</th>
                <th>Tanggal</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Metode</th>
                <th>Kasir</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($i + 1); ?></td>
                <td><?php echo e($sale->no_nota); ?></td>
                <td><?php echo e($sale->tanggal->format('d/m/Y')); ?> <?php echo e($sale->jam); ?></td>
                <td class="text-center"><?php echo e(ucfirst($sale->shift)); ?></td>
                <td class="text-center"><?php echo e(ucfirst($sale->tipe_penjualan)); ?></td>
                <td class="text-center"><?php echo e($sale->metode_bayar == 'tunai' ? 'Tunai' : 'Non Tunai'); ?></td>
                <td><?php echo e($sale->user->name ?? '-'); ?></td>
                <td class="text-right font-bold">Rp <?php echo e(number_format($sale->grand_total, 0, ',', '.')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem Apotek POS
    </div>
</body>
</html>
<?php /**PATH C:\WORK\other\kasir_test\resources\views/exports/sales-pdf.blade.php ENDPATH**/ ?>