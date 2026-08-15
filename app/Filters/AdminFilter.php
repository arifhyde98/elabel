<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = (string) session()->get('user_role');
        if (! in_array($role, ['admin', 'super_admin'], true)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
