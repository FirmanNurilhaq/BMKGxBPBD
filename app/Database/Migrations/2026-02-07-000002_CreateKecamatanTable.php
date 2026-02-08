<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKecamatanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kecamatan');

        $kecamatan = ['Kasemen', 'Taktakan', 'Serang', 'Cipocok Jaya', 'Walantaka', 'Curug'];
        foreach ($kecamatan as $nama) {
            $this->db->table('kecamatan')->insert(['nama_kecamatan' => $nama]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('kecamatan');
    }
}
