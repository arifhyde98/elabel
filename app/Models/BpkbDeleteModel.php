<?php

namespace App\Models;

use CodeIgniter\Model;

class BpkbDeleteModel extends Model
{
    protected $table            = 'bpkb_deletes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'bpkb_id',
        'box_id',
        'box_code',
        'year',
        'vehicle_type',
        'deleted_by',
        'deleted_at',
        'reason',
        'reason_detail',
        'plate_number',
        'no_bpkb',
        'no_rangka',
        'no_mesin',
        'merek',
        'tipe',
        'isi_silinder',
        'warna',
        'pengguna',
        'status',
        'pdf_path',
        'input_by',
        'support_doc_path',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'bpkb_id'    => 'integer',
        'deleted_by' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $validationRules = [
        'bpkb_id'    => 'required|integer',
        'deleted_by' => 'required|integer',
        'deleted_at' => 'required',
        'reason'     => 'required|max_length[50]',
    ];
}
