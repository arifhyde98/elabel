<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function edit(): string|RedirectResponse
    {
        $user = $this->currentUser();
        if (! $user) {
            return redirect()->to(site_url('login'))->with('error', 'Silakan login kembali.');
        }

        return view('admin/users/edit', [
            'user'        => $user,
            'activeMenu'  => 'profile',
            'pageTitle'   => 'Edit Profil',
            'formAction'  => site_url('admin/profile'),
            'backUrl'     => site_url('admin'),
            'lockRole'    => true,
            'profileMode' => true,
        ]);
    }

    public function update(): RedirectResponse
    {
        $user = $this->currentUser();
        if (! $user) {
            return redirect()->to(site_url('login'))->with('error', 'Silakan login kembali.');
        }

        $userId = (int) $user['id'];
        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]|is_unique[users.email,id,' . $userId . ']',
        ];

        $newPassword = (string) $this->request->getPost('new_password');
        if ($newPassword !== '') {
            $rules['current_password'] = 'required|max_length[72]';
            $rules['new_password'] = 'required|min_length[8]|max_length[72]';
            $rules['new_password_confirmation'] = 'required|matches[new_password]';
        }

        $file = $this->request->getFile('profile_photo');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['profile_photo'] = 'uploaded[profile_photo]|max_size[profile_photo,2048]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png,image/webp]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'  => (string) $this->request->getPost('name'),
            'email' => (string) $this->request->getPost('email'),
        ];

        if ($newPassword !== '') {
            $currentPassword = (string) $this->request->getPost('current_password');
            if (! password_verify($currentPassword, (string) $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Password saat ini tidak sesuai.');
            }

            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $oldPhoto = $user['profile_photo'] ?? null;
            $data['profile_photo'] = $this->storeProfilePhoto($file, $userId);

            if (! empty($oldPhoto) && $oldPhoto !== $data['profile_photo']) {
                $oldPath = WRITEPATH . $oldPhoto;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        try {
            $this->users->skipValidation(true)->update($userId, $data);
        } finally {
            $this->users->skipValidation(false);
        }
        session()->set([
            'user_name'  => $data['name'],
            'user_email' => $data['email'],
            'user_photo' => $data['profile_photo'] ?? ($user['profile_photo'] ?? null),
        ]);

        $this->logActivity('update', 'Profil', 'Mengubah profil sendiri.', 'users', $userId);

        return redirect()->to(site_url('admin/profile'))->with('success', 'Profil berhasil diperbarui.');
    }

    public function photo(): ResponseInterface
    {
        $user = $this->currentUser();
        if (! $user || empty($user['profile_photo'])) {
            return $this->response->setStatusCode(404);
        }

        $path = WRITEPATH . $user['profile_photo'];
        if (! is_file($path)) {
            return $this->response->setStatusCode(404);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];

        return $this->response
            ->setHeader('Content-Type', $contentTypes[$extension] ?? 'application/octet-stream')
            ->setHeader('Cache-Control', 'private, max-age=300')
            ->setBody(file_get_contents($path) ?: '');
    }

    private function currentUser(): ?array
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return null;
        }

        $user = $this->users->find($userId);

        return $user ?: null;
    }

    private function storeProfilePhoto($file, int $userId): string
    {
        $dir = WRITEPATH . 'uploads/profile_photos';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $sourcePath = $file->getTempName();
        $filename = 'user-' . $userId . '-' . uniqid('', true) . '.webp';
        $targetPath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (! $this->compressProfilePhoto($sourcePath, $targetPath)) {
            $extension = strtolower((string) $file->getClientExtension());
            if ($extension === '') {
                $extension = strtolower((string) $file->getExtension());
            }
            if ($extension === '') {
                $extension = 'jpg';
            }

            $filename = 'user-' . $userId . '-' . uniqid('', true) . '.' . $extension;
            $file->move($dir, $filename);
        }

        return 'uploads/profile_photos/' . $filename;
    }

    private function compressProfilePhoto(string $sourcePath, string $targetPath): bool
    {
        if (! is_file($sourcePath) || ! function_exists('imagewebp')) {
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (! is_array($info) || empty($info[2])) {
            return false;
        }

        $source = match ((int) $info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG  => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            default        => false,
        };

        if (! $source) {
            return false;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            imagedestroy($source);
            return false;
        }

        $maxSize = 512;
        $scale = min(1, $maxSize / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if (! $target) {
            imagedestroy($source);
            return false;
        }

        imagealphablending($target, true);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        $resampled = imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $saved = $resampled && imagewebp($target, $targetPath, 82);
        imagedestroy($target);
        imagedestroy($source);

        return $saved && is_file($targetPath);
    }
}
