<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusLogModel extends Model
{
    protected $table            = 'status_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'status',
        'changed_by',
        'created_at',
    ];

    public function getLogs(int $transactionId): array
    {
        return $this->select('status_logs.*, users.name as changer_name')
            ->join('users', 'users.id = status_logs.changed_by', 'left')
            ->where('transaction_id', $transactionId)
            ->orderBy('status_logs.id', 'ASC')
            ->findAll();
    }
}
