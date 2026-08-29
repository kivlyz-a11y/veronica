<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table            = 'applicants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'nik',
        'phone',
        'email',
        'address',
        'institution',
        'applicant_role',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'           => 'required|min_length[3]|max_length[150]',
        'nik'            => 'required|min_length[16]|max_length[20]',
        'phone'          => 'required|min_length[9]|max_length[30]',
        'applicant_role' => 'required',
    ];
}
