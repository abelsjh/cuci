<?php

if (! function_exists('format_rupiah')) {
    /**
     * Format angka ke format mata uang Rupiah (contoh: Rp 50.000).
     */
    function format_rupiah($amount): string
    {
        $num = (float) ($amount ?? 0);
        return 'Rp ' . number_format($num, 0, ',', '.');
    }
}

if (! function_exists('status_badge')) {
    /**
     * Tampilkan badge status pengerjaan transaksi.
     */
    function status_badge(string $status): string
    {
        $status = strtolower(trim($status));
        return match ($status) {
            'antri'      => '<span class="badge text-bg-secondary">Antri</span>',
            'dikerjakan' => '<span class="badge text-bg-warning">Dikerjakan</span>',
            'selesai'    => '<span class="badge text-bg-info">Selesai</span>',
            'diambil'    => '<span class="badge text-bg-success">Diambil</span>',
            'dibatalkan' => '<span class="badge text-bg-danger">Dibatalkan</span>',
            default      => '<span class="badge text-bg-secondary">' . esc(ucfirst($status)) . '</span>',
        };
    }
}

if (! function_exists('category_badge')) {
    /**
     * Tampilkan badge kategori layanan (mobil / helm).
     */
    function category_badge(string $category): string
    {
        $cat = strtolower(trim($category));
        return match ($cat) {
            'mobil' => '<span class="badge text-bg-primary">Mobil</span>',
            'helm'  => '<span class="badge text-bg-dark">Helm</span>',
            default => '<span class="badge text-bg-secondary">' . esc(ucfirst($cat)) . '</span>',
        };
    }
}

if (! function_exists('payment_badge')) {
    /**
     * Tampilkan badge status pembayaran (belum / lunas).
     */
    function payment_badge(string $status): string
    {
        $status = strtolower(trim($status));
        return match ($status) {
            'lunas' => '<span class="badge text-bg-success">Lunas</span>',
            'belum' => '<span class="badge text-bg-light border text-dark">Belum</span>',
            default => '<span class="badge text-bg-light border text-dark">' . esc(ucfirst($status)) . '</span>',
        };
    }
}

if (! function_exists('payment_method_label')) {
    /**
     * Tampilkan label metode pembayaran yang rapi.
     */
    function payment_method_label(?string $method): string
    {
        if (empty($method)) {
            return '-';
        }
        $method = strtolower(trim($method));
        return match ($method) {
            'tunai'    => 'Tunai',
            'transfer' => 'Transfer',
            'qris'     => 'QRIS',
            default    => ucfirst($method),
        };
    }
}

if (! function_exists('format_datetime')) {
    /**
     * Format tanggal dan jam ke dd-mm-yyyy HH:ii.
     */
    function format_datetime(?string $datetime, string $format = 'd-m-Y H:i'): string
    {
        if (empty($datetime)) {
            return '-';
        }
        $time = strtotime($datetime);
        if (! $time) {
            return $datetime;
        }
        return date($format, $time);
    }
}

if (! function_exists('get_setting')) {
    /**
     * Ambil nilai pengaturan dari database.
     */
    function get_setting(string $key, string $default = ''): string
    {
        static $settings = null;
        if ($settings === null) {
            $db = \Config\Database::connect();
            $rows = $db->table('settings')->get()->getResultArray();
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['key']] = $row['value'];
            }
        }

        return $settings[$key] ?? $default;
    }
}
