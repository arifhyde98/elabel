<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException;

class HealthCheck extends Controller
{
    /**
     * Endpoint untuk mengecek status kesehatan proyek (Spoke).
     */
    public function index()
    {
        $status = 'healthy';
        $dbStatus = 'connected';

        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
        } catch (DatabaseException $e) {
            $status = 'unhealthy';
            $dbStatus = 'disconnected: ' . $e->getMessage();
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $dbStatus = 'disconnected: ' . $e->getMessage();
        }

        return $this->response->setJSON([
            'project' => 'eLabel',
            'framework' => 'CodeIgniter ' . \CodeIgniter\CodeIgniter::CI_VERSION,
            'status' => $status,
            'database' => $dbStatus,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
