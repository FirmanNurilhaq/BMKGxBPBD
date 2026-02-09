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

    /**
     * Get today's latest data by kecamatan
     */
    public function getTodayByKecamatan()
    {
        $today = date('Y-m-d');
        
        $subquery = $this->db->table('curah_hujan')
            ->select('kecamatan_id, MAX(tanggal) as max_tanggal')
            ->where('DATE(tanggal)', $today)
            ->groupBy('kecamatan_id')
            ->getCompiledSelect();
            
        return $this->select('curah_hujan.*, kecamatan.nama_kecamatan')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->join("($subquery) as latest", 'curah_hujan.kecamatan_id = latest.kecamatan_id AND curah_hujan.tanggal = latest.max_tanggal')
            ->orderBy('curah_hujan.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get all today's entries for dashboard table
     */
    public function getTodayEntries()
    {
        $today = date('Y-m-d');
        
        return $this->select('curah_hujan.*, kecamatan.nama_kecamatan')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->where('DATE(curah_hujan.tanggal)', $today)
            ->orderBy('curah_hujan.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get historical daily averages for trendline
     */
    public function getHistoricalTrend($days = 7)
    {
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        return $this->db->table('curah_hujan')
            ->select('DATE(tanggal) as date, AVG(nilai_curah_hujan) as avg_rainfall, COUNT(*) as entries')
            ->where('DATE(tanggal) >=', $startDate)
            ->groupBy('DATE(tanggal)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if there's data for today
     */
    public function hasTodayData()
    {
        $today = date('Y-m-d');
        return $this->where('DATE(tanggal)', $today)->countAllResults() > 0;
    }

    /**
     * Get archive data by date range with kecamatan
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->select('curah_hujan.*, kecamatan.nama_kecamatan')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->where('DATE(curah_hujan.tanggal) >=', $startDate)
            ->where('DATE(curah_hujan.tanggal) <=', $endDate)
            ->orderBy('curah_hujan.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get monthly aggregated data for yearly archive
     */
    public function getYearlyAggregated($year)
    {
        return $this->db->table('curah_hujan')
            ->select('kecamatan.nama_kecamatan, MONTH(tanggal) as bulan, AVG(nilai_curah_hujan) as avg_rainfall, MAX(nilai_curah_hujan) as max_rainfall, MIN(nilai_curah_hujan) as min_rainfall, COUNT(*) as total_entries')
            ->join('kecamatan', 'kecamatan.id = curah_hujan.kecamatan_id')
            ->where('YEAR(tanggal)', $year)
            ->groupBy('kecamatan.nama_kecamatan, MONTH(tanggal)')
            ->orderBy('bulan', 'ASC')
            ->orderBy('kecamatan.nama_kecamatan', 'ASC')
            ->get()
            ->getResultArray();
    }
}
