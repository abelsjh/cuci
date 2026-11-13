<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\UserModel;

class AuditLogs extends BaseController
{
    protected AuditLogModel $auditLogModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
        $this->userModel     = new UserModel();
    }

    public function index()
    {
        $filters = [
            'user_id'    => trim((string) $this->request->getGet('user_id')),
            'action'     => trim((string) $this->request->getGet('action')),
            'start_date' => trim((string) $this->request->getGet('start_date')),
            'end_date'   => trim((string) $this->request->getGet('end_date')),
        ];

        $builder = $this->auditLogModel->buildFilterQuery($filters);
        $logs = $builder->paginate(20);
        $pager = $this->auditLogModel->pager;

        $users = $this->userModel->findAll();

        return view('audit_logs/index', [
            'title'   => 'Audit Log Aktivitas',
            'logs'    => $logs,
            'pager'   => $pager,
            'filters' => $filters,
            'users'   => $users,
        ]);
    }
}
