<?php

namespace App\Controllers;

use App\Models\PasswordResetTokenModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    private UserModel $users;
    private PasswordResetTokenModel $resetTokens;

    public function __construct()
    {
        helper(['form', 'url', 'text']);
        $this->users       = new UserModel();
        $this->resetTokens = new PasswordResetTokenModel();
    }

    public function login(): string
    {
        return view('auth/login');
    }

    public function attemptLogin(): RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|min_length[8]|max_length[72]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $user = $this->users->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password tidak valid.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan.');
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $this->users->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session()->regenerate(true);
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
        ]);

        $this->users->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->request->getIPAddress(),
        ]);

        $target = site_url('admin');

        return redirect()->to($target)->with('success', 'Login berhasil.');
    }

    public function register(): string
    {
        return redirect()->to(site_url('login'))->with('error', 'Registrasi ditutup. Hubungi super admin.');
    }

    public function attemptRegister(): RedirectResponse
    {
        return redirect()->to(site_url('login'))->with('error', 'Registrasi ditutup. Hubungi super admin.');

        /*
        $rules = [
            'name'                  => 'required|min_length[3]|max_length[100]',
            'email'                 => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password'              => 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'password_confirmation' => 'required|matches[password]',
        ];

        $messages = [
            'password' => [
                'regex_match' => 'Password wajib mengandung huruf kecil, huruf besar, dan angka.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->users->countAllResults() === 0 ? 'admin' : 'user';

        $this->users->insert([
            'name'      => (string) $this->request->getPost('name'),
            'email'     => (string) $this->request->getPost('email'),
            'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => $role,
            'is_active' => 1,
        ]);

        return redirect()->to(site_url('login'))
            ->with('success', 'Registrasi berhasil. Silakan login.');
        */
    }

    public function forgotPassword(): string
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink(): RedirectResponse
    {
        $rules = ['email' => 'required|valid_email|max_length[150]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = (string) $this->request->getPost('email');
        $user  = $this->users->where('email', $email)->first();

        // Keep message generic to avoid user enumeration.
        $response = redirect()->to(site_url('forgot-password'))
            ->with('success', 'Jika email terdaftar, link reset password sudah dikirim.');

        if (! $user) {
            return $response;
        }

        $token      = bin2hex(random_bytes(32));
        $tokenHash  = hash('sha256', $token);
        $expiresAt  = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $resetLink  = base_url('reset-password/' . $token);
        $emailSent  = false;

        $this->resetTokens->where('user_id', $user['id'])->delete();
        $this->resetTokens->insert([
            'user_id'    => $user['id'],
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);

        $emailService = service('email');
        $emailService->setTo($user['email']);
        $emailService->setSubject('Reset Password eLabel');
        $emailService->setMessage(
            "Halo {$user['name']},\n\n" .
            "Klik link berikut untuk reset password Anda:\n{$resetLink}\n\n" .
            "Link berlaku sampai {$expiresAt}."
        );
        $emailSent = $emailService->send();

        if (! $emailSent) {
            log_message('warning', 'Reset password email gagal dikirim ke {email}. Link: {link}', [
                'email' => $user['email'],
                'link'  => $resetLink,
            ]);
        }

        // Helper development when SMTP belum diset.
        if (ENVIRONMENT !== 'production' && ! $emailSent) {
            session()->setFlashdata('dev_reset_link', $resetLink);
        }

        return $response;
    }

    public function resetPassword(string $token): string
    {
        $record = $this->getValidResetRecord($token);
        if (! $record) {
            return view('auth/reset_password', [
                'invalidToken' => true,
                'token'        => '',
            ]);
        }

        return view('auth/reset_password', [
            'invalidToken' => false,
            'token'        => $token,
        ]);
    }

    public function attemptResetPassword(): RedirectResponse
    {
        $rules = [
            'token'                 => 'required',
            'password'              => 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'password_confirmation' => 'required|matches[password]',
        ];

        $messages = [
            'password' => [
                'regex_match' => 'Password wajib mengandung huruf kecil, huruf besar, dan angka.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token  = (string) $this->request->getPost('token');
        $record = $this->getValidResetRecord($token);

        if (! $record) {
            return redirect()->back()->with('error', 'Token reset password tidak valid atau sudah kedaluwarsa.');
        }

        $this->users->update($record['user_id'], [
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);
        $this->resetTokens->where('user_id', $record['user_id'])->delete();

        return redirect()->to(site_url('login'))->with('success', 'Password berhasil diubah. Silakan login.');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('/'))->with('success', 'Anda sudah logout.');
    }

    private function getValidResetRecord(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);
        $record    = $this->resetTokens->where('token_hash', $tokenHash)->first();

        if (! $record) {
            return null;
        }

        if (strtotime($record['expires_at']) < time()) {
            $this->resetTokens->delete($record['id']);
            return null;
        }

        return $record;
    }
}
