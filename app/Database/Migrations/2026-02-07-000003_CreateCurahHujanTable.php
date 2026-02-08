<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCurahHujanTable extends Migration
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
            'kecamatan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nilai_curah_hujan' => [
                'type' => 'FLOAT',
            ],
            'tanggal' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kecamatan_id', 'kecamatan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('curah_hujan');

        $today = date('Y-m-d H:i:s');
        $data = [
            ['kecamatan_id' => 1, 'nilai_curah_hujan' => 15.5, 'tanggal' => $today],
            ['kecamatan_id' => 2, 'nilai_curah_hujan' => 55.0, 'tanggal' => $today],
            ['kecamatan_id' => 3, 'nilai_curah_hujan' => 35.0, 'tanggal' => $today],
            ['kecamatan_id' => 4, 'nilai_curah_hujan' => 60.0, 'tanggal' => $today],
            ['kecamatan_id' => 5, 'nilai_curah_hujan' => 10.0, 'tanggal' => $today],
            ['kecamatan_id' => 6, 'nilai_curah_hujan' => 25.0, 'tanggal' => $today],
        ];
        $this->db->table('curah_hujan')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('curah_hujan');
    }
}
