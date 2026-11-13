<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\TransactionModel;

class Customers extends BaseController
{
    protected CustomerModel $customerModel;
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->customerModel    = new CustomerModel();
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        $builder = $this->customerModel->orderBy('id', 'DESC');
        if (! empty($keyword)) {
            $builder->groupStart()
                ->like('name', $keyword)
                ->orLike('phone', $keyword)
                ->groupEnd();
        }

        $customers = $builder->paginate(15);
        $pager     = $this->customerModel->pager;

        return view('customers/index', [
            'title'     => 'Kelola Pelanggan',
            'customers' => $customers,
            'pager'     => $pager,
            'keyword'   => $keyword,
        ]);
    }

    public function new()
    {
        return view('customers/new', [
            'title' => 'Tambah Pelanggan',
        ]);
    }

    public function create()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[8]|max_length[20]|is_unique[customers.phone]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $phone = trim((string) $this->request->getPost('phone'));
        $data = [
            'name'  => trim((string) $this->request->getPost('name')),
            'phone' => $phone !== '' ? $phone : null,
            'notes' => trim((string) $this->request->getPost('notes')),
        ];

        $this->customerModel->insert($data);

        return redirect()->to('/customers')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Pelanggan tidak ditemukan.');
        }

        return view('customers/edit', [
            'title'    => 'Ubah Pelanggan',
            'customer' => $customer,
        ]);
    }

    public function update(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Pelanggan tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'phone' => "permit_empty|min_length[8]|max_length[20]|is_unique[customers.phone,id,{$id}]",
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $phone = trim((string) $this->request->getPost('phone'));
        $data = [
            'name'  => trim((string) $this->request->getPost('name')),
            'phone' => $phone !== '' ? $phone : null,
            'notes' => trim((string) $this->request->getPost('notes')),
        ];

        $this->customerModel->update($id, $data);

        return redirect()->to('/customers')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function show(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Pelanggan tidak ditemukan.');
        }

        // Ambil riwayat transaksi pelanggan
        $transactions = $this->transactionModel
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.created_by', 'left')
            ->where('transactions.customer_id', $id)
            ->orderBy('transactions.id', 'DESC')
            ->findAll();

        return view('customers/show', [
            'title'        => 'Riwayat Pelanggan: ' . $customer['name'],
            'customer'     => $customer,
            'transactions' => $transactions,
        ]);
    }

    public function delete(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Pelanggan tidak ditemukan.');
        }

        $this->customerModel->delete($id);

        return redirect()->to('/customers')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
