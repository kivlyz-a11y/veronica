<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'application_id',
        'filename',
        'stored_filename',
        'filepath',
        'mime_type',
        'size',
        'uploaded_at',
    ];

    public $timestamps = false;

    public function getByApplicationId(int $applicationId)
    {
        return $this->where('application_id', $applicationId)->findAll();
    }
}
