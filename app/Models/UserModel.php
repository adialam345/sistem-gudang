<?php

namespace App\Models;

use App\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $allowedFields = [
        'name', 'email', 'username', 'password_hash', 'role',
        'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
    ];
    protected $useSoftDeletes = true;
} 