<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'detail',
        'created_at',
    ];

    public function buildFilterQuery(array $filters)
    {
        $builder = $this->select('audit_logs.*, users.name as user_name, users.username as username, users.role as user_role')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.id', 'DESC');

        if (! empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', (int) $filters['user_id']);
        }

        if (! empty($filters['action'])) {
            $builder->where('audit_logs.action', strtoupper(trim($filters['action'])));
        }

        if (! empty($filters['start_date'])) {
            $builder->where('DATE(audit_logs.created_at) >=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $builder->where('DATE(audit_logs.created_at) <=', $filters['end_date']);
        }

        return $builder;
    }
}
