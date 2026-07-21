<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table            = 'loans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'bpkb_id',
        'requester_id',
        'requester_name',
        'requester_phone',
        'requester_email',
        'requester_org',
        'requester_address',
        'requester_note',
        'requested_at',
        'approved_by',
        'approved_at',
        'status',
        'note',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'           => 'integer',
        'bpkb_id'      => 'integer',
        'requester_id' => 'integer',
        'approved_by'  => 'integer',
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'bpkb_id'      => 'required|integer',
        'status'       => 'permit_empty|in_list[Menunggu,Disetujui,Ditolak,Selesai]',
    ];
}
