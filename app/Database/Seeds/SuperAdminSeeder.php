<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('users');

        $superAdmins = [
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@elabel.local',
                'password'   => password_hash('SuperAdmin123!', PASSWORD_DEFAULT),
                'role'       => 'super_admin',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Admin Baru',
                'email'      => 'admin2@elabel.local',
                'password'   => password_hash('AdminBaru123!', PASSWORD_DEFAULT),
                'role'       => 'super_admin',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($superAdmins as $admin) {
            $exists = $builder->where('email', $admin['email'])->get()->getRowArray();

            if (!$exists) {
                $builder->insert($admin);
            }
        }
    }
}
