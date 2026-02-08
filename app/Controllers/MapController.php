<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CurahHujanModel;
use App\Models\KecamatanModel;

class MapController extends BaseController
{
    public function index()
    {
        $curahHujanModel = new CurahHujanModel();
        $rainfallData = $curahHujanModel->getLatestByKecamatan();

        $mapData = [];
        foreach ($rainfallData as $row) {
            $nilai = (float) $row['nilai_curah_hujan'];
            
            if ($nilai < 20) {
                $riskLevel = 'aman';
                $riskColor = '#22c55e';
                $mitigation = 'Kondisi aman. Tetap pantau informasi cuaca dari BMKG.';
            } elseif ($nilai <= 50) {
                $riskLevel = 'siaga';
                $riskColor = '#fbbf24';
                $mitigation = 'Waspada potensi genangan. Hindari aktivitas di luar ruangan saat hujan lebat.';
            } else {
                $riskLevel = 'resiko';
                $riskColor = '#ef4444';
                $mitigation = 'Siapkan tas siaga bencana dan lakukan evakuasi jika air mulai naik.';
            }

            $mapData[] = [
                'nama_kecamatan' => $row['nama_kecamatan'],
                'nilai_curah_hujan' => $nilai,
                'tanggal' => $row['tanggal'],
                'risk_level' => $riskLevel,
                'risk_color' => $riskColor,
                'mitigation' => $mitigation,
            ];
        }

        return view('map/index', [
            'title' => 'Peta Risiko Banjir - Kota Serang',
            'mapData' => $mapData,
        ]);
    }

    public function apiMapData()
    {
        $curahHujanModel = new CurahHujanModel();
        $rainfallData = $curahHujanModel->getLatestByKecamatan();

        $result = [];
        foreach ($rainfallData as $row) {
            $nilai = (float) $row['nilai_curah_hujan'];
            
            if ($nilai < 20) {
                $riskLevel = 'aman';
                $riskColor = '#22c55e';
                $mitigation = 'Kondisi aman. Tetap pantau informasi cuaca dari BMKG.';
            } elseif ($nilai <= 50) {
                $riskLevel = 'siaga';
                $riskColor = '#fbbf24';
                $mitigation = 'Waspada potensi genangan. Hindari aktivitas di luar ruangan saat hujan lebat.';
            } else {
                $riskLevel = 'resiko';
                $riskColor = '#ef4444';
                $mitigation = 'Siapkan tas siaga bencana dan lakukan evakuasi jika air mulai naik.';
            }

            $result[] = [
                'nama_kecamatan' => $row['nama_kecamatan'],
                'nilai_curah_hujan' => $nilai,
                'tanggal' => $row['tanggal'],
                'risk_level' => $riskLevel,
                'risk_color' => $riskColor,
                'mitigation' => $mitigation,
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $result]);
    }
}
