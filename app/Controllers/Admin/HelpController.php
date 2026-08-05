<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class HelpController extends BaseController
{
    public function index(): string
    {
        return view('admin/bpkb/help', [
            'activeMenu' => 'help',
        ]);
    }
}
