<?php

namespace App\Models;

use CodeIgniter\Model;

class BpkbModel extends Model
{
    protected $table            = 'bpkb';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'box_id',
        'year',
        'vehicle_type',
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
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'box_id'     => 'integer',
        'year'       => 'integer',
        'input_by'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'box_id'       => 'required|integer',
        'year'         => 'required|integer',
        'vehicle_type' => 'required|in_list[R4,R2]',
        'plate_number' => 'required|max_length[20]',
        'no_bpkb'      => 'permit_empty|max_length[50]',
        'no_rangka'    => 'permit_empty|max_length[50]',
        'no_mesin'     => 'permit_empty|max_length[50]',
        'merek'        => 'permit_empty|max_length[100]',
        'tipe'         => 'permit_empty|max_length[100]',
        'isi_silinder' => 'permit_empty|max_length[50]',
        'warna'        => 'permit_empty|max_length[100]',
        'pengguna'     => 'permit_empty|max_length[100]',
        'status'       => 'permit_empty|in_list[Tersedia,Dipinjam,Dihapus]',
        'pdf_path'     => 'permit_empty|max_length[255]',
        'input_by'     => 'required|integer',
    ];
}
