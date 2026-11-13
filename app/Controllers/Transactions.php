<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Libraries\PriceCalculator;
use App\Models\CustomerModel;
use App\Models\ServiceModel;
use App\Models\StatusLogModel;
use App\Models\TransactionItemModel;
use App\Models\TransactionModel;
use App\Models\UserModel;
use Config\Database;

class Transactions extends BaseController
{
    protected TransactionModel $transactionModel;
    protected TransactionItemModel $itemModel;
    protected StatusLogModel $statusLogModel;
    protected ServiceModel $serviceModel;
    protected CustomerModel $customerModel;
    protected UserModel $userModel;
    protected PriceCalculator $calculator;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->itemModel        = new TransactionItemModel();
        $this->statusLogModel   = new StatusLogModel();
        $this->serviceModel     = new ServiceModel();
        $this->customerModel    = new CustomerModel();
        $this->userModel        = new UserModel();
        $this->calculator       = new PriceCalculator();
    }

    public function index()
    {
        $filters = [
            'q'              => trim((string) $this->request->getGet('q')),
            'status'         => trim((string) $this->request->getGet('status')),
            'payment_status' => trim((string) $this->request->getGet('payment_status')),
            'payment_method' => trim((string) $this->request->getGet('payment_method')),
            'created_by'     => trim((string) $this->request->getGet('created_by')),
            'category'       => trim((string) $this->request->getGet('category')),
            'start_date'     => trim((string) $this->request->getGet('start_date')),
            'end_date'       => trim((string) $this->request->getGet('end_date')),
        ];

        $builder = $this->transactionModel->buildFilterQuery($filters);
        $transactions = $builder->orderBy('transactions.id', 'DESC')->paginate(15);
        $pager = $this->transactionModel->pager;

        $cashiers = $this->userModel->findAll();

        return view('transactions/index', [
            'title'        => 'Daftar Transaksi',
            'transactions' => $transactions,
            'pager'        => $pager,
            'filters'      => $filters,
            'cashiers'     => $cashiers,
        ]);
    }

    public function new()
    {
        $services = $this->serviceModel->getActiveServices();
        $customers = $this->customerModel->orderBy('name', 'ASC')->findAll();

        $selectedCustomerId = (int) $this->request->getGet('customer_id');
        $selectedCustomer = $selectedCustomerId ? $this->customerModel->find($selectedCustomerId) : null;

        return view('transactions/new', [
            'title'            => 'Transaksi Pencucian Baru',
            'services'         => $services,
            'customers'        => $customers,
            'selectedCustomer' => $selectedCustomer,
        ]);
    }

    public function create()
    {
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[100]',
            'item_note'     => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $serviceIds = (array) $this->request->getPost('service_ids');
        $quantities = (array) $this->request->getPost('quantities');

        // Filter item yang valid
        $validList = [];
        foreach ($serviceIds as $idx => $sId) {
            $sId = (int) $sId;
            $qty = isset($quantities[$idx]) ? (int) $quantities[$idx] : 1;
            if ($sId > 0 && $qty >= 1) {
                $validList[] = ['id' => $sId, 'qty' => $qty];
            }
        }

        if (empty($validList)) {
            return redirect()->back()->withInput()->with('error', 'Minimal pilih satu layanan pencucian.');
        }

        // Ambil data layanan master untuk snapshot harga & nama
        $itemsData = [];
        foreach ($validList as $entry) {
            $sId = $entry['id'];
            $qty = $entry['qty'];

            $service = $this->serviceModel->find($sId);
            if (! $service) {
                return redirect()->back()->withInput()->with('error', 'Layanan yang dipilih tidak valid.');
            }

            $price = (float) $service['price'];
            $subtotal = $this->calculator->calculateItemSubtotal($price, $qty);

            $itemsData[] = [
                'service_id'   => $sId,
                'service_name' => $service['name'],
                'price'        => $price,
                'qty'          => $qty,
                'subtotal'     => $subtotal,
            ];
        }

        // Hitung total kotor
        $grossTotal = $this->calculator->calculateGrossTotal($itemsData);

        // Hitung diskon
        $discountType = $this->request->getPost('discount_type') === 'percent' ? 'percent' : 'nominal';
        $discountVal  = (float) $this->request->getPost('discount_value');
        $discount     = $this->calculator->calculateDiscount($grossTotal, $discountVal, $discountType);

        // Hitung total bersih
        $total = $this->calculator->calculateTotal($grossTotal, $discount);

        // Pembayaran (opsional saat awal)
        $isPaying = (bool) $this->request->getPost('is_pay_now');
        $paymentMethod = null;
        $paidAmount    = 0.0;
        $changeAmount  = 0.0;
        $paidAt        = null;
        $paymentStatus = 'belum';

        if ($isPaying) {
            $paymentMethod = (string) $this->request->getPost('payment_method');
            $rawPaid       = (float) $this->request->getPost('paid_amount');

            $valRes = $this->calculator->validatePayment($total, $paymentMethod, $rawPaid);
            if (! $valRes['valid']) {
                return redirect()->back()->withInput()->with('error', $valRes['error']);
            }

            $paidAmount    = $valRes['paid_amount'];
            $changeAmount  = $valRes['change_amount'];
            $paidAt        = date('Y-m-d H:i:s');
            $paymentStatus = 'lunas';
        }

        $customerId = $this->request->getPost('customer_id');
        $customerId = (! empty($customerId) && (int) $customerId > 0) ? (int) $customerId : null;

        $db = Database::connect();
        $db->transStart();

        $invoiceNo = $this->transactionModel->generateInvoiceNo();
        $now = date('Y-m-d H:i:s');
        $userId = (int) session()->get('user_id');

        $txData = [
            'invoice_no'     => $invoiceNo,
            'customer_id'    => $customerId,
            'customer_name'  => trim((string) $this->request->getPost('customer_name')),
            'item_note'      => trim((string) $this->request->getPost('item_note')),
            'subtotal'       => $grossTotal,
            'discount'       => $discount,
            'total'          => $total,
            'payment_method' => $paymentMethod,
            'paid_amount'    => $paidAmount,
            'change_amount'  => $changeAmount,
            'paid_at'        => $paidAt,
            'status'         => 'antri',
            'payment_status' => $paymentStatus,
            'cancel_reason'  => null,
            'print_count'    => 0,
            'created_by'     => $userId,
            'created_at'     => $now,
            'updated_at'     => $now,
        ];

        $txId = $this->transactionModel->insert($txData);

        // Simpan transaction items snapshot
        foreach ($itemsData as &$item) {
            $item['transaction_id'] = $txId;
            $this->itemModel->insert($item);
        }

        // Catat log status awal: antri
        $this->statusLogModel->insert([
            'transaction_id' => $txId,
            'status'         => 'antri',
            'changed_by'     => $userId,
            'created_at'     => $now,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi ke database.');
        }

        return redirect()->to('/transactions/' . $txId)->with('success', "Transaksi {$invoiceNo} berhasil dibuat dengan status Antri.");
    }

    public function show(int $id)
    {
        $transaction = $this->transactionModel->getWithDetails($id);
        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan.');
        }

        $items = $this->itemModel->getItems($id);
        $logs  = $this->statusLogModel->getLogs($id);

        return view('transactions/show', [
            'title'       => 'Detail Transaksi ' . $transaction['invoice_no'],
            'transaction' => $transaction,
            'items'       => $items,
            'logs'        => $logs,
        ]);
    }

    public function updateStatus(int $id)
    {
        $transaction = $this->transactionModel->find($id);
        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan.');
        }

        $newStatus = strtolower(trim((string) $this->request->getPost('status')));
        $validStatuses = ['antri', 'dikerjakan', 'selesai', 'diambil', 'dibatalkan'];
        if (! in_array($newStatus, $validStatuses, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $currentStatus = $transaction['status'];
        $userRole = session()->get('user_role');
        $userId   = (int) session()->get('user_id');

        // Jika transaksi sudah final (diambil / dibatalkan)
        if (in_array($currentStatus, ['diambil', 'dibatalkan'], true)) {
            if ($userRole !== 'admin') {
                return redirect()->back()->with('error', 'Transaksi yang sudah selesai/dibatalkan tidak dapat diubah lagi statusnya.');
            }
            // Admin membuka kembali transaksi final
            AuditLogger::log(
                $userId,
                'REOPEN_TRANSACTION',
                'transaction',
                $id,
                "Admin membuka kembali transaksi {$transaction['invoice_no']} dari status '{$currentStatus}' ke '{$newStatus}'"
            );
        }

        // Aturan transisi maju normal: antri -> dikerjakan -> selesai -> diambil
        $statusRank = [
            'antri'      => 1,
            'dikerjakan' => 2,
            'selesai'    => 3,
            'diambil'    => 4,
            'dibatalkan' => 99,
        ];

        // Status mundur hanya boleh oleh Admin
        if (isset($statusRank[$newStatus], $statusRank[$currentStatus])) {
            if ($statusRank[$newStatus] < $statusRank[$currentStatus] && $userRole !== 'admin') {
                return redirect()->back()->with('error', 'Status mundur hanya dapat dilakukan oleh Administrator.');
            }
        }

        // Syarat status Diambil: payment_status WAJIB lunas!
        if ($newStatus === 'diambil' && $transaction['payment_status'] !== 'lunas') {
            return redirect()->back()->with('error', 'Status Diambil hanya diperbolehkan jika pembayaran sudah Lunas. Silakan lakukan pembayaran terlebih dahulu.');
        }

        $now = date('Y-m-d H:i:s');
        $this->transactionModel->update($id, [
            'status'     => $newStatus,
            'updated_at' => $now,
        ]);

        $this->statusLogModel->insert([
            'transaction_id' => $id,
            'status'         => $newStatus,
            'changed_by'     => $userId,
            'created_at'     => $now,
        ]);

        return redirect()->to('/transactions/' . $id)->with('success', "Status pengerjaan berhasil diperbarui menjadi: " . ucfirst($newStatus));
    }

    public function pay(int $id)
    {
        $transaction = $this->transactionModel->find($id);
        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan.');
        }

        if ($transaction['payment_status'] === 'lunas') {
            return redirect()->back()->with('info', 'Transaksi ini sudah lunas.');
        }

        $method = (string) $this->request->getPost('payment_method');
        $paidAmount = (float) $this->request->getPost('paid_amount');
        $total = (float) $transaction['total'];

        $res = $this->calculator->validatePayment($total, $method, $paidAmount);
        if (! $res['valid']) {
            return redirect()->back()->with('error', $res['error']);
        }

        $now = date('Y-m-d H:i:s');
        $this->transactionModel->update($id, [
            'payment_method' => $method,
            'paid_amount'    => $res['paid_amount'],
            'change_amount'  => $res['change_amount'],
            'payment_status' => 'lunas',
            'paid_at'        => $now,
            'updated_at'     => $now,
        ]);

        return redirect()->to('/transactions/' . $id)->with('success', 'Pembayaran berhasil dicatat. Status transaksi kini LUNAS.');
    }

    public function cancel(int $id)
    {
        $transaction = $this->transactionModel->find($id);
        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan.');
        }

        $userRole = session()->get('user_role');
        $userId   = (int) session()->get('user_id');

        // Aturan: Pembatalan hanya dari 'antri' atau 'dikerjakan' (kecuali admin)
        if (! in_array($transaction['status'], ['antri', 'dikerjakan'], true) && $userRole !== 'admin') {
            return redirect()->back()->with('error', 'Hanya transaksi berstatus Antri atau Dikerjakan yang dapat dibatalkan.');
        }

        // Aturan: Membatalkan transaksi yang sudah LUNAS hanya oleh Admin
        if ($transaction['payment_status'] === 'lunas' && $userRole !== 'admin') {
            return redirect()->back()->with('error', 'Transaksi yang sudah LUNAS hanya dapat dibatalkan oleh Administrator.');
        }

        $reason = trim((string) $this->request->getPost('cancel_reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan pembatalan wajib diisi.');
        }

        $now = date('Y-m-d H:i:s');
        $this->transactionModel->update($id, [
            'status'        => 'dibatalkan',
            'cancel_reason' => $reason,
            'updated_at'    => $now,
        ]);

        $this->statusLogModel->insert([
            'transaction_id' => $id,
            'status'         => 'dibatalkan',
            'changed_by'     => $userId,
            'created_at'     => $now,
        ]);

        AuditLogger::log(
            $userId,
            'CANCEL_TRANSACTION',
            'transaction',
            $id,
            "Membatalkan transaksi {$transaction['invoice_no']} (Status bayar: {$transaction['payment_status']}). Alasan: {$reason}"
        );

        return redirect()->to('/transactions/' . $id)->with('warning', 'Transaksi telah dibatalkan.');
    }

    public function printReceipt(int $id)
    {
        $transaction = $this->transactionModel->getWithDetails($id);
        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Catat print count
        $newCount = ((int) $transaction['print_count']) + 1;
        $this->transactionModel->update($id, ['print_count' => $newCount]);

        $items = $this->itemModel->getItems($id);

        return view('transactions/print', [
            'title'       => 'Cetak Nota ' . $transaction['invoice_no'],
            'transaction' => $transaction,
            'items'       => $items,
            'printCount'  => $newCount,
        ]);
    }
}
