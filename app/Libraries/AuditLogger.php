<?php

namespace App\Libraries;

use Config\Database;

class AuditLogger
{
    /**
     * Catat aktivitas audit ke tabel audit_logs.
     *
     * @param int|null $userId
     * @param string $action (misal: 'CREATE', 'UPDATE_PRICE', 'CANCEL_TRANSACTION', 'RESET_PASSWORD', 'LOGIN_FAILED')
     * @param string $entity (misal: 'service', 'transaction', 'user', 'auth')
     * @param int|null $entityId
     * @param string|array|null $detail
     * @return bool
     */
    public static function log(?int $userId, string $action, string $entity, ?int $entityId, $detail = null): bool
    {
        $db = Database::connect();

        $detailStr = null;
        if (is_array($detail)) {
            // Hilangkan field sensitif bila ada
            unset($detail['password'], $detail['current_password'], $detail['new_password']);
            $detailStr = json_encode($detail, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($detail)) {
            $detailStr = $detail;
        }

        return (bool) $db->table('audit_logs')->insert([
            'user_id'    => $userId,
            'action'     => strtoupper($action),
            'entity'     => strtolower($entity),
            'entity_id'  => $entityId,
            'detail'     => $detailStr,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
