<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/index', [
            'name'  => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role'  => session()->get('user_role'),
            'activeMenu' => 'dashboard',
        ]);
    }
}
