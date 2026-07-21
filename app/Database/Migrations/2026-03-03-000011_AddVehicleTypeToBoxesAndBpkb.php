<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVehicleTypeToBoxesAndBpkb extends Migration
{
    public function up()
    {
        $this->forge->addColumn('boxes', [
            'vehicle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'mobil',
                'after'      => 'location',
            ],
        ]);

        $this->forge->addColumn('bpkb', [
            'vehicle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'mobil',
                'after'      => 'year',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bpkb', 'vehicle_type');
        $this->forge->dropColumn('boxes', 'vehicle_type');
    }
}
