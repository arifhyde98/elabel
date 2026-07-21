<?php

namespace App\Models;

use CodeIgniter\Model;

class BoxYearModel extends Model
{
    protected $table            = 'box_years';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'box_id',
        'year',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'     => 'integer',
        'box_id' => 'integer',
        'year'   => 'integer',
    ];
}
