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
        'spesifikasi',
        'jenis_penyerahan',
        'luas',
        'tanggal_perolehan',
        'alamat',
        'lokasi',
        'dinas',
        'pemberi_hibah',
        'pdf_path',
        'box_id',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'         => 'integer',
        'luas'       => 'float',
        'tanggal_perolehan' => 'date',
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
        'spesifikasi'       => 'permit_empty|max_length[255]',
        'jenis_penyerahan'  => 'permit_empty|max_length[150]',
        'luas'              => 'permit_empty|decimal',
        'tanggal_perolehan' => 'permit_empty|valid_date',
        'alamat'            => 'permit_empty|max_length[255]',
        'lokasi'            => 'permit_empty|max_length[255]',
        'dinas'             => 'permit_empty|max_length[150]',
        'pemberi_hibah'     => 'permit_empty|max_length[150]',
        'pdf_path'          => 'permit_empty|max_length[255]',
        'box_id'            => 'permit_empty|integer',
    ];
}
