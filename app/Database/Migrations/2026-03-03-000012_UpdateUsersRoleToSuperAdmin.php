<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersRoleToSuperAdmin extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE users SET role = 'admin' WHERE role = 'user'");
        $this->db->query("ALTER TABLE users MODIFY role ENUM('admin','super_admin') NOT NULL DEFAULT 'admin'");
    }

    public function down()
    {
        $this->db->query("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
        $this->db->query("ALTER TABLE users MODIFY role ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
}
