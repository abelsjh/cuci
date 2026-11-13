<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_no',
        'customer_id',
        'customer_name',
        'item_note',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'paid_at',
        'status',
        'payment_status',
        'cancel_reason',
        'print_count',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate nomor invoice unik dan berurutan per hari.
     * Format: {prefix}-YYYYMMDD-0001
     */
    public function generateInvoiceNo(): string
    {
        $settingModel = new SettingModel();
        $prefix = $settingModel->getSetting('invoice_prefix', 'CW');
        $dateStr = date('Ymd');
        $searchPrefix = "{$prefix}-{$dateStr}-";

        $lastRow = $this->like('invoice_no', $searchPrefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $nextSeq = 1;
        if ($lastRow && ! empty($lastRow['invoice_no'])) {
            $parts = explode('-', $lastRow['invoice_no']);
            $lastNum = (int) end($parts);
            $nextSeq = $lastNum + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $dateStr, $nextSeq);
    }

    /**
     * Ambil transaksi lengkap dengan data pembuat (kasir) dan pelanggan.
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->select('transactions.*, users.name as cashier_name, customers.phone as customer_phone')
            ->join('users', 'users.id = transactions.created_by', 'left')
            ->join('customers', 'customers.id = transactions.customer_id', 'left')
            ->where('transactions.id', $id)
            ->first();
    }

    /**
     * Query builder dengan filter untuk halaman daftar transaksi.
     */
    public function buildFilterQuery(array $filters)
    {
        $builder = $this->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.created_by', 'left');

        if (! empty($filters['q'])) {
            $q = trim($filters['q']);
            $builder->groupStart()
                ->like('transactions.invoice_no', $q)
                ->orLike('transactions.customer_name', $q)
                ->orLike('transactions.item_note', $q)
                ->groupEnd();
        }

        if (! empty($filters['status'])) {
            $builder->where('transactions.status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $builder->where('transactions.payment_status', $filters['payment_status']);
        }

        if (! empty($filters['payment_method'])) {
            $builder->where('transactions.payment_method', $filters['payment_method']);
        }

        if (! empty($filters['created_by'])) {
            $builder->where('transactions.created_by', (int) $filters['created_by']);
        }

        if (! empty($filters['start_date'])) {
            $builder->where('DATE(transactions.created_at) >=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $builder->where('DATE(transactions.created_at) <=', $filters['end_date']);
        }

        if (! empty($filters['category'])) {
            $builder->whereIn('transactions.id', function ($subQuery) use ($filters) {
                $subQuery->select('transaction_id')
                    ->from('transaction_items')
                    ->join('services', 'services.id = transaction_items.service_id')
                    ->where('services.category', $filters['category']);
            });
        }

        return $builder;
    }
}
