<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'is_encrypted',
    ];

    protected $useTimestamps = true;

    /**
     * Get a setting by key with fallback default value
     */
    public function getVal(string $key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Set / update a setting by key
     */
    public function setVal(string $key, $value)
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
            'is_encrypted'  => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all settings as key => value dictionary
     */
    public function getAllAsMap(): array
    {
        $all = $this->findAll();
        $map = [];
        foreach ($all as $item) {
            $map[$item['setting_key']] = $item['setting_value'];
        }
        return $map;
    }
}
