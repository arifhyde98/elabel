<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikatBoxModel extends Model
{
    protected $table            = 'sertifikat_boxes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'box_code',
        'lokasi',
        'created_by',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'box_code'   => 'required|max_length[30]|is_unique[sertifikat_boxes.box_code,id,{id}]',
        'lokasi'     => 'required|max_length[255]',
        'created_by' => 'permit_empty|integer',
    ];
}
