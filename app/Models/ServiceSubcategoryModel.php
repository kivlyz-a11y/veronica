<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceSubcategoryModel extends Model
{
    protected $table            = 'service_subcategories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'service_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $useTimestamps = true;

    public function getByServiceId(int $serviceId)
    {
        return $this->where('service_id', $serviceId)
                    ->where('status', 'active')
                    ->findAll();
    }
}
