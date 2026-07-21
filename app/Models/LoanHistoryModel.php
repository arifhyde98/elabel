<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanHistoryModel extends Model
{
    protected $table            = 'loan_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'loan_id',
        'status',
        'changed_by',
        'changed_at',
        'note',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'loan_id'    => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
    ];

    protected $validationRules = [
        'loan_id'    => 'required|integer',
        'status'     => 'required|max_length[20]',
        'changed_by' => 'permit_empty|integer',
        'changed_at' => 'required',
    ];
}
