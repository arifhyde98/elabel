<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index(): string
    {
        $items = $this->users->orderBy('created_at', 'desc')->findAll();

        return view('admin/users/index', [
            'items'      => $items,
            'activeMenu' => 'users',
        ]);
    }

    public function create(): string
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat menambahkan admin.');
        }

        return view('admin/users/create', [
            'activeMenu' => 'users',
        ]);
    }

    public function store()
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat menambahkan admin.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[72]',
            'role'     => 'required|in_list[admin,super_admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newId = $this->users->insert([
            'name'      => (string) $this->request->getPost('name'),
            'email'     => (string) $this->request->getPost('email'),
            'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => (string) $this->request->getPost('role'),
            'is_active' => 1,
        ]);
        $this->logActivity('create', 'User', 'Menambahkan user ' . (string) $this->request->getPost('name') . '.', 'users', (int) $newId);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat mengubah admin.');
        }

        $user = $this->users->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        return view('admin/users/edit', [
            'user'       => $user,
            'activeMenu' => 'users',
        ]);
    }

    public function update(int $id)
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat mengubah admin.');
        }

        $user = $this->users->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]|is_unique[users.email,id,' . $id . ']',
            'role'  => 'required|in_list[admin,super_admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'  => (string) $this->request->getPost('name'),
            'email' => (string) $this->request->getPost('email'),
            'role'  => (string) $this->request->getPost('role'),
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->users->update($id, $data);
        $this->logActivity('update', 'User', 'Mengubah user ' . $data['name'] . '.', 'users', $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil diperbarui.');
    }

    public function toggle(int $id)
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat mengubah status admin.');
        }

        $user = $this->users->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $newStatus = (int) $user['is_active'] === 1 ? 0 : 1;
        $this->users->update($id, ['is_active' => $newStatus]);
        $this->logActivity('toggle', 'User', (($newStatus === 1) ? 'Mengaktifkan' : 'Menonaktifkan') . ' user ' . ($user['name'] ?? '-') . '.', 'users', $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'Status user berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Hanya super admin yang dapat menghapus admin.');
        }

        $user = $this->users->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        if ($user['id'] === session()->get('user_id')) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->users->delete($id);
        $this->logActivity('delete', 'User', 'Menghapus user ' . ($user['name'] ?? '-') . '.', 'users', $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil dihapus.');
    }

    private function isSuperAdmin(): bool
    {
        return session()->get('user_role') === 'super_admin';
    }
}
