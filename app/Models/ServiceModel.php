<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table            = 'services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'icon',
        'status',
    ];

    protected $useTimestamps = true;

    public function getActiveServices()
    {
        return $this->where('status', 'active')->findAll();
    }
}
