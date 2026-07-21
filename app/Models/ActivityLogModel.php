<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'description',
        'reference_type',
        'reference_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'           => 'integer',
        'user_id'      => 'integer',
        'reference_id' => 'integer',
        'created_at'   => 'datetime',
    ];

    protected $validationRules = [
        'action'      => 'required|max_length[40]',
        'module'      => 'required|max_length[80]',
        'description' => 'required|max_length[255]',
    ];
}
