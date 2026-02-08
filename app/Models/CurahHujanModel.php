<?php

namespace App\Models;

use CodeIgniter\Model;

class CurahHujanModel extends Model
{
    protected $table = 'curah_hujan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kecamatan_id', 'nilai_curah_hujan', 'tanggal'];

    public function getLatestByKecamatan()
    {
        $subquery = $this->db->table('curah_hujan')
            ->select('kecamatan_id, MAX(tanggal) as max_tanggal')
            ->groupBy('kecamatan_id')
            ->getCompiledSelect();
            
        return $this->select('curah_hujan.*, kecamatan.nama_kecamatan')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->join("($subquery) as latest", 'curah_hujan.kecamatan_id = latest.kecamatan_id AND curah_hujan.tanggal = latest.max_tanggal')
            ->orderBy('kecamatan.nama_kecamatan', 'ASC')
            ->findAll();
    }

    public function getWithKecamatan()
    {
        return $this->select('curah_hujan.*, kecamatan.nama_kecamatan')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->orderBy('curah_hujan.tanggal', 'DESC')
            ->findAll();
    }
}
