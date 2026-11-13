<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionItemModel extends Model
{
    protected $table            = 'transaction_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'service_id',
        'service_name',
        'price',
        'qty',
        'subtotal',
    ];

    public function getItems(int $transactionId): array
    {
        return $this->select('transaction_items.*, services.category')
            ->join('services', 'services.id = transaction_items.service_id', 'left')
            ->where('transaction_id', $transactionId)
            ->findAll();
    }
}
