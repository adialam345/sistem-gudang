<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Untuk audit fields
    protected function beforeInsert(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }
}