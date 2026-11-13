<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'key',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSetting(string $key, string $default = ''): string
    {
        $row = $this->where('key', $key)->first();
        return $row['value'] ?? $default;
    }

    public function setSetting(string $key, ?string $value): bool
    {
        $row = $this->where('key', $key)->first();
        if ($row) {
            return (bool) $this->update($row['id'], ['value' => $value]);
        }

        return (bool) $this->insert([
            'key'   => $key,
            'value' => $value,
        ]);
    }

    public function getAllAssoc(): array
    {
        $rows = $this->findAll();
        $assoc = [];
        foreach ($rows as $row) {
            $assoc[$row['key']] = $row['value'];
        }
        return $assoc;
    }
}
