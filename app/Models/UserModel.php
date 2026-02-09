<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'nama', 'password', 'role', 'email_verified', 'verification_token', 'token_expires_at', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get all verified public users for alert notifications
     */
    public function getVerifiedPublicUsers()
    {
        return $this->where('role', 'masyarakat')
                    ->where('email_verified', 1)
                    ->findAll();
    }
}
