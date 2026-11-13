<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\ServiceModel;

class Services extends BaseController
{
    protected ServiceModel $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
    }

    public function index()
    {
        $services = $this->serviceModel
            ->orderBy('category', 'ASC')
            ->orderBy('price', 'ASC')
            ->findAll();

        return view('services/index', [
            'title'    => 'Kelola Layanan Cuci',
            'services' => $services,
        ]);
    }

    public function create()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'category' => 'required|in_list[mobil,helm]',
            'price'    => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $price = (float) $this->request->getPost('price');
        $data = [
            'name'      => trim((string) $this->request->getPost('name')),
            'category'  => (string) $this->request->getPost('category'),
            'price'     => $price,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $id = $this->serviceModel->insert($data);

        AuditLogger::log(
            (int) session()->get('user_id'),
            'CREATE',
            'service',
            (int) $id,
            "Menambahkan layanan baru: {$data['name']} (Kategori: {$data['category']}, Harga: Rp " . number_format($price, 0, ',', '.') . ')'
        );

        return redirect()->to('/services')->with('success', 'Layanan baru berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $service = $this->serviceModel->find($id);
        if (! $service) {
            return redirect()->to('/services')->with('error', 'Layanan tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'category' => 'required|in_list[mobil,helm]',
            'price'    => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newPrice = (float) $this->request->getPost('price');
        $oldPrice = (float) $service['price'];

        $data = [
            'name'      => trim((string) $this->request->getPost('name')),
            'category'  => (string) $this->request->getPost('category'),
            'price'     => $newPrice,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->serviceModel->update($id, $data);

        // Catat di audit log jika ada perubahan harga (FR-08)
        if (abs($newPrice - $oldPrice) > 0.01) {
            AuditLogger::log(
                (int) session()->get('user_id'),
                'UPDATE_PRICE',
                'service',
                $id,
                "Mengubah harga layanan '{$service['name']}' dari Rp " . number_format($oldPrice, 0, ',', '.') . ' menjadi Rp ' . number_format($newPrice, 0, ',', '.')
            );
        } else {
            AuditLogger::log(
                (int) session()->get('user_id'),
                'UPDATE',
                'service',
                $id,
                "Memperbarui data layanan '{$service['name']}'"
            );
        }

        return redirect()->to('/services')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggle(int $id)
    {
        $service = $this->serviceModel->find($id);
        if (! $service) {
            return redirect()->to('/services')->with('error', 'Layanan tidak ditemukan.');
        }

        $newStatus = $service['is_active'] ? 0 : 1;
        $this->serviceModel->update($id, ['is_active' => $newStatus]);

        $statusStr = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        AuditLogger::log(
            (int) session()->get('user_id'),
            'TOGGLE_STATUS',
            'service',
            $id,
            "Layanan '{$service['name']}' {$statusStr}"
        );

        return redirect()->to('/services')->with('success', "Layanan '{$service['name']}' berhasil {$statusStr}.");
    }

    public function delete(int $id)
    {
        $service = $this->serviceModel->find($id);
        if (! $service) {
            return redirect()->to('/services')->with('error', 'Layanan tidak ditemukan.');
        }

        // Cek apakah layanan sudah dipakai di transaksi
        $db = \Config\Database::connect();
        $usedCount = $db->table('transaction_items')->where('service_id', $id)->countAllResults();

        if ($usedCount > 0) {
            // Jika sudah pernah dipakai, sarankan nonaktifkan saja agar integritas data transaksi terjaga
            $this->serviceModel->update($id, ['is_active' => 0]);
            AuditLogger::log(
                (int) session()->get('user_id'),
                'DEACTIVATE',
                'service',
                $id,
                "Layanan '{$service['name']}' dinonaktifkan (karena memiliki riwayat transaksi)"
            );
            return redirect()->to('/services')->with('warning', "Layanan memiliki riwayat transaksi sehingga tidak dihapus permanen, melainkan dinonaktifkan otomatis.");
        }

        $this->serviceModel->delete($id);
        AuditLogger::log(
            (int) session()->get('user_id'),
            'DELETE',
            'service',
            $id,
            "Menghapus layanan '{$service['name']}'"
        );

        return redirect()->to('/services')->with('success', 'Layanan berhasil dihapus.');
    }
}
