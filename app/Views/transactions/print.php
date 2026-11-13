<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota - <?= esc($transaction['invoice_no']) ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            font-size: 13px;
            line-height: 1.4;
        }

        .receipt-container {
            max-width: 380px;
            /* Cocok untuk printer thermal 80mm / 58mm atau kertas kecil */
            margin: 0 auto;
            border: 1px dashed #ccc;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            font-size: 12px;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
            font-size: 12px;
        }

        .items-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 12px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-print {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .receipt-container {
                border: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Cetak Nota Sekarang</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">Tutup</button>
        <?php if ($printCount > 1): ?>
            <div style="font-size: 11px; color: #666; margin-top: 5px;">
                (Salinan cetak ke-<?= $printCount ?>)
            </div>
        <?php endif; ?>
    </div>

    <div class="receipt-container">
        <!-- Header Usaha -->
        <div class="header">
            <h1><?= esc(get_setting('business_name', 'AutoClean Car & Helmet Wash')) ?></h1>
            <p><?= esc(get_setting('address', 'Jl. Raya Utama No. 123, Jakarta')) ?></p>
            <p>Telp: <?= esc(get_setting('phone', '0812-3456-7890')) ?></p>
        </div>

        <div class="divider"></div>

        <!-- Info Transaksi -->
        <table class="info-table">
            <tr>
                <td style="width: 35%;">No. Nota</td>
                <td style="width: 5%;">:</td>
                <td class="fw-bold"><?= esc($transaction['invoice_no']) ?></td>
            </tr>
            <tr>
                <td>Tanggal/Jam</td>
                <td>:</td>
                <td><?= format_datetime($transaction['created_at']) ?></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td><?= esc($transaction['cashier_name'] ?? 'Kasir') ?></td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td><?= esc($transaction['customer_name']) ?></td>
            </tr>
            <?php if ($transaction['item_note']): ?>
                <tr>
                    <td>Plat/Item</td>
                    <td>:</td>
                    <td><?= esc($transaction['item_note']) ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Status Bayar</td>
                <td>:</td>
                <td class="fw-bold"><?= strtoupper($transaction['payment_status']) ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Tabel Item Layanan -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th class="text-center" style="width: 35px;">Qty</th>
                    <th class="text-end" style="width: 80px;">Tarif</th>
                    <th class="text-end" style="width: 90px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?= esc($item['service_name']) ?>
                            <br><small style="color: #555;">[<?= ucfirst($item['category'] ?? 'mobil') ?>]</small>
                        </td>
                        <td class="text-center"><?= $item['qty'] ?></td>
                        <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Rincian Biaya -->
        <table class="info-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-end"><?= format_rupiah($transaction['subtotal']) ?></td>
            </tr>
            <?php if ($transaction['discount'] > 0): ?>
                <tr>
                    <td>Diskon</td>
                    <td class="text-end">- <?= format_rupiah($transaction['discount']) ?></td>
                </tr>
            <?php endif; ?>
            <tr class="fw-bold" style="font-size: 14px;">
                <td>TOTAL</td>
                <td class="text-end"><?= format_rupiah($transaction['total']) ?></td>
            </tr>
            <?php if ($transaction['payment_status'] === 'lunas'): ?>
                <tr>
                    <td>Metode Bayar</td>
                    <td class="text-end"><?= payment_method_label($transaction['payment_method']) ?></td>
                </tr>
                <tr>
                    <td>Dibayar</td>
                    <td class="text-end"><?= format_rupiah($transaction['paid_amount']) ?></td>
                </tr>
                <?php if ($transaction['payment_method'] === 'tunai'): ?>
                    <tr>
                        <td>Kembalian</td>
                        <td class="text-end"><?= format_rupiah($transaction['change_amount']) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer">
            <p><?= esc(get_setting('receipt_footer', 'Terima kasih atas kunjungan Anda! Bersih, Cepat, dan Mengkilap.')) ?></p>
            <p style="font-size: 9px; color: #777;">Dicetak: <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>

    <script>
        // Cetak otomatis saat jendela nota dibuka
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>

</html>