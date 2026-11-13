<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class Reports extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');
        $category  = trim((string) $this->request->getGet('category'));
        $cashierId = trim((string) $this->request->getGet('created_by'));
        $method    = trim((string) $this->request->getGet('payment_method'));

        // Query transaksi pendapatan (status = 'diambil')
        $builder = $db->table('transactions')
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.created_by', 'left')
            ->where('transactions.status', 'diambil')
            ->where('DATE(transactions.created_at) >=', $startDate)
            ->where('DATE(transactions.created_at) <=', $endDate);

        if (! empty($cashierId)) {
            $builder->where('transactions.created_by', (int) $cashierId);
        }

        if (! empty($method)) {
            $builder->where('transactions.payment_method', $method);
        }

        if (! empty($category)) {
            $builder->whereIn('transactions.id', function ($sub) use ($category) {
                $sub->select('transaction_id')
                    ->from('transaction_items')
                    ->join('services', 'services.id = transaction_items.service_id')
                    ->where('services.category', $category);
            });
        }

        $transactions = $builder->orderBy('transactions.created_at', 'DESC')->get()->getResultArray();

        // Hitung total ringkasan
        $totalRevenue = 0.0;
        $totalTransactions = count($transactions);
        foreach ($transactions as $t) {
            $totalRevenue += (float) $t['total'];
        }

        $avgTransaction = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0.0;

        // Laporan Layanan Terlaris (FR-37)
        $bestSellerBuilder = $db->table('transaction_items')
            ->select('services.name as service_name, services.category, SUM(transaction_items.qty) as total_qty, SUM(transaction_items.subtotal) as total_subtotal')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('services', 'services.id = transaction_items.service_id')
            ->where('transactions.status', 'diambil')
            ->where('DATE(transactions.created_at) >=', $startDate)
            ->where('DATE(transactions.created_at) <=', $endDate);

        if (! empty($category)) {
            $bestSellerBuilder->where('services.category', $category);
        }

        $bestSellers = $bestSellerBuilder->groupBy('services.id')
            ->orderBy('total_qty', 'DESC')
            ->get()
            ->getResultArray();

        $userModel = new UserModel();
        $cashiers  = $userModel->findAll();

        return view('reports/index', [
            'title'             => 'Laporan Pendapatan & Layanan',
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'category'          => $category,
            'cashierId'         => $cashierId,
            'method'            => $method,
            'transactions'      => $transactions,
            'totalRevenue'      => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'avgTransaction'    => $avgTransaction,
            'bestSellers'       => $bestSellers,
            'cashiers'          => $cashiers,
        ]);
    }

    public function export()
    {
        $db = Database::connect();

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');
        $category  = trim((string) $this->request->getGet('category'));
        $cashierId = trim((string) $this->request->getGet('created_by'));
        $method    = trim((string) $this->request->getGet('payment_method'));

        $builder = $db->table('transactions')
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.created_by', 'left')
            ->where('transactions.status', 'diambil')
            ->where('DATE(transactions.created_at) >=', $startDate)
            ->where('DATE(transactions.created_at) <=', $endDate);

        if (! empty($cashierId)) {
            $builder->where('transactions.created_by', (int) $cashierId);
        }

        if (! empty($method)) {
            $builder->where('transactions.payment_method', $method);
        }

        if (! empty($category)) {
            $builder->whereIn('transactions.id', function ($sub) use ($category) {
                $sub->select('transaction_id')
                    ->from('transaction_items')
                    ->join('services', 'services.id = transaction_items.service_id')
                    ->where('services.category', $category);
            });
        }

        $transactions = $builder->orderBy('transactions.created_at', 'ASC')->get()->getResultArray();

        $filename = 'Laporan_Pendapatan_' . $startDate . '_sd_' . $endDate . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8 support
        fputs($output, "\xEF\xBB\xBF");

        // Header CSV
        fputcsv($output, [
            'No. Nota',
            'Tanggal Transaksi',
            'Nama Pelanggan',
            'Plat/Item',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Total (Rp)',
            'Metode Pembayaran',
            'Status Bayar',
            'Kasir / Operator',
        ]);

        $totalRev = 0;
        foreach ($transactions as $tx) {
            $totalRev += (float) $tx['total'];
            fputcsv($output, [
                $tx['invoice_no'],
                $tx['created_at'],
                $tx['customer_name'],
                $tx['item_note'] ?: '-',
                (int) $tx['subtotal'],
                (int) $tx['discount'],
                (int) $tx['total'],
                strtoupper($tx['payment_method'] ?? '-'),
                strtoupper($tx['payment_status']),
                $tx['cashier_name'] ?? '-',
            ]);
        }

        // Baris Total
        fputcsv($output, []);
        fputcsv($output, ['TOTAL PENDAPATAN', '', '', '', '', '', (int) $totalRev, '', '', '']);

        fclose($output);
        exit;
    }
}
