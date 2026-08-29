<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'email',
        'password_hash',
        'role',
        'phone',
        'status',
        'last_login_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[150]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'role'  => 'required|in_list[superadmin,admin,officer,pimpinan]',
    ];

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
