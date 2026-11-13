<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'phone',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari pelanggan berdasarkan nama atau nomor HP.
     */
    public function search(string $keyword, int $limit = 20): array
    {
        return $this->like('name', $keyword)
            ->orLike('phone', $keyword)
            ->orderBy('name', 'ASC')
            ->findAll($limit);
    }
}
