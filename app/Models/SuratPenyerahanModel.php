<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratPenyerahanModel extends Model
{
    protected $table            = 'surat_penyerahan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nibar',
        'no_surat',
        'status_penggunaan',
        'luas',
        'tahun',
        'lokasi',
        'pemberi_hibah',
        'box_id',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'luas'       => 'float',
        'tahun'      => 'integer',
        'box_id'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nibar'             => 'permit_empty|max_length[100]',
        'no_surat'          => 'required|max_length[150]',
        'status_penggunaan' => 'permit_empty|max_length[150]',
        'luas'              => 'permit_empty|decimal',
        'tahun'             => 'permit_empty|integer',
        'lokasi'            => 'permit_empty|max_length[255]',
        'pemberi_hibah'     => 'permit_empty|max_length[150]',
        'box_id'            => 'permit_empty|integer',
    ];
}
