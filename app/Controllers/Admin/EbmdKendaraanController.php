<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class EbmdKendaraanController extends BaseController
{
    public function index(): string
    {
        return view('admin/ebmd_kendaraan/index', [
            'activeMenu' => 'ebmd_kendaraan',
            'title'      => 'Kendaraan E-BMD',
        ]);
    }
}
