<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $sale->no_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 5mm; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .double-line { border-top: 2px solid #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .header { margin-bottom: 10px; }
        .footer { margin-top: 10px; }
        @media print {
            body { width: 80mm; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header center">
        <div class="bold" style="font-size: 14px;">APOTEK POS</div>
        <div>Jl. Contoh No. 123</div>
        <div>Telp: (021) 1234567</div>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No. Nota</td>
            <td class="right">{{ $sale->no_nota }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="right">{{ $sale->tanggal->format('d/m/Y') }} {{ $sale->jam }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="right">{{ $sale->user->name ?? '-' }}</td>
        </tr>
        @if($sale->customer)
        <tr>
            <td>Pelanggan</td>
            <td class="right">{{ $sale->customer->nama }}</td>
        </tr>
        @endif
        @if($sale->pasien_nama)
        <tr>
            <td>Pasien</td>
            <td class="right">{{ $sale->pasien_nama }}</td>
        </tr>
        @if($sale->pasien_no_hp)
        <tr>
            <td>No. HP</td>
            <td class="right">{{ $sale->pasien_no_hp }}</td>
        </tr>
        @endif
        @if($sale->pasien_alamat)
        <tr>
            <td>Alamat</td>
            <td class="right">{{ $sale->pasien_alamat }}</td>
        </tr>
        @endif
        @endif
    </table>

    <div class="double-line"></div>

    @if($sale->tipe_penjualan === 'resep')
    {{-- Resep: TIDAK tampilkan daftar obat, hanya total --}}
    <div class="center" style="margin: 8px 0;">
        <div class="bold">PENJUALAN RESEP</div>
        <div style="font-size: 11px;">{{ $sale->details->count() }} item obat</div>
    </div>

    <div class="double-line"></div>

    <table>
        <tr class="bold">
            <td style="font-size: 14px;">TOTAL</td>
            <td class="right" style="font-size: 14px;">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>
    @else
    {{-- Reguler: tampilkan daftar obat lengkap --}}
    @foreach($sale->details as $detail)
    <div style="margin-bottom: 3px;">
        <div>{{ $detail->product->nama_barang }}</div>
        <table>
            <tr>
                <td>{{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                @if($detail->diskon_persen > 0)
                    <td class="right">Disc {{ $detail->diskon_persen }}%</td>
                @endif
                <td class="right bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endforeach

    <div class="double-line"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($sale->diskon_total > 0)
        <tr>
            <td>Diskon</td>
            <td class="right">- Rp {{ number_format($sale->diskon_total, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td style="font-size: 14px;">TOTAL</td>
            <td class="right" style="font-size: 14px;">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>
    @endif

    <div class="line"></div>

    <table>
        <tr>
            <td>{{ $sale->metode_bayar == 'tunai' ? 'Tunai' : 'Non Tunai' }}</td>
            <td class="right">Rp {{ number_format($sale->bayar, 0, ',', '.') }}</td>
        </tr>
        @if($sale->metode_bayar == 'tunai')
        <tr>
            <td>Kembalian</td>
            <td class="right">Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <div class="line"></div>

    <div class="footer center">
        <div>Terima kasih atas kunjungan Anda</div>
        <div>Semoga lekas sembuh</div>
        <div style="margin-top: 5px; font-size: 10px;">{{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>
