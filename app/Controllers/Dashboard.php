<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use Config\Database;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = Database::connect();
        $today = date('Y-m-d');

        // 1. Total transaksi hari ini
        $todayTransactionsCount = $db->table('transactions')
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        // 2. Pendapatan hari ini (transaksi berstatus 'diambil' pada tanggal ini)
        $todayRevenueRow = $db->table('transactions')
            ->selectSum('total', 'revenue')
            ->where('status', 'diambil')
            ->where('DATE(updated_at)', $today)
            ->get()
            ->getRow();
        $todayRevenue = (float) ($todayRevenueRow->revenue ?? 0);

        // 3. Jumlah per status saat ini
        $statusCountsRaw = $db->table('transactions')
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $statusCounts = [
            'antri'      => 0,
            'dikerjakan' => 0,
            'selesai'    => 0,
            'diambil'    => 0,
            'dibatalkan' => 0,
        ];
        foreach ($statusCountsRaw as $row) {
            $statusCounts[$row['status']] = (int) $row['count'];
        }

        // 4. Pendapatan per kategori (transaksi 'diambil')
        $categoryRevenue = [
            'mobil' => 0.0,
            'helm'  => 0.0,
        ];
        $catRows = $db->table('transaction_items')
            ->select('services.category, SUM(transaction_items.subtotal) as cat_total')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('services', 'services.id = transaction_items.service_id')
            ->where('transactions.status', 'diambil')
            ->groupBy('services.category')
            ->get()
            ->getResultArray();

        foreach ($catRows as $row) {
            if (isset($categoryRevenue[$row['category']])) {
                $categoryRevenue[$row['category']] = (float) $row['cat_total'];
            }
        }

        // 5. 5 Transaksi terbaru
        $recentTransactions = $db->table('transactions')
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.created_by', 'left')
            ->orderBy('transactions.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('dashboard/index', [
            'title'                  => 'Dashboard',
            'todayTransactionsCount' => $todayTransactionsCount,
            'todayRevenue'           => $todayRevenue,
            'statusCounts'           => $statusCounts,
            'categoryRevenue'        => $categoryRevenue,
            'recentTransactions'     => $recentTransactions,
        ]);
    }
}
