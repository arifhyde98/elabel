<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikatModel extends Model
{
    protected $table            = 'sertifikat_tanah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_sertipikat',
        'nibar',
        'status_penggunaan',
        'spesifikasi',
        'luas',
        'tanggal_perolehan',
        'nilai_perolehan',
        'nama_pemilik',
        'cara_perolehan',
        'alamat',
        'lokasi',
        'dinas',
        'box_id',
        'pdf_path',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'                => 'integer',
        'box_id'            => 'integer',
        'luas'              => 'float',
        'nilai_perolehan'   => 'float',
        'tanggal_perolehan' => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'no_sertipikat'     => 'required|max_length[100]',
        'nibar'             => 'permit_empty|max_length[100]',
        'status_penggunaan' => 'permit_empty|max_length[100]',
        'spesifikasi'       => 'permit_empty|max_length[255]',
        'luas'              => 'permit_empty|decimal',
        'tanggal_perolehan' => 'permit_empty|valid_date',
        'nilai_perolehan'   => 'permit_empty|decimal',
        'nama_pemilik'      => 'permit_empty|max_length[150]',
        'cara_perolehan'    => 'permit_empty|max_length[150]',
        'alamat'            => 'permit_empty|max_length[255]',
        'lokasi'            => 'permit_empty|max_length[255]',
        'dinas'             => 'permit_empty|max_length[150]',
        'box_id'            => 'permit_empty|integer',
        'pdf_path'          => 'permit_empty|max_length[255]',
    ];
}
