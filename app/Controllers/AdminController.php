<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CurahHujanModel;
use App\Models\KecamatanModel;

class AdminController extends BaseController
{
    protected $curahHujanModel;
    protected $kecamatanModel;

    public function __construct()
    {
        $this->curahHujanModel = new CurahHujanModel();
        $this->kecamatanModel = new KecamatanModel();
    }

    public function dashboard()
    {
        $curahHujanData = $this->curahHujanModel->getLatestByKecamatan();
        
        $stats = ['aman' => 0, 'siaga' => 0, 'resiko' => 0];
        foreach ($curahHujanData as $row) {
            $nilai = (float) $row['nilai_curah_hujan'];
            if ($nilai < 20) {
                $stats['aman']++;
            } elseif ($nilai <= 50) {
                $stats['siaga']++;
            } else {
                $stats['resiko']++;
            }
        }

        return view('admin/dashboard', [
            'title' => 'Dashboard Admin BMKG',
            'stats' => $stats,
            'curahHujanData' => $curahHujanData,
        ]);
    }

    public function dashboardStats()
    {
        $curahHujanData = $this->curahHujanModel->getLatestByKecamatan();
        
        $stats = ['aman' => 0, 'siaga' => 0, 'resiko' => 0];
        $formattedData = [];
        
        foreach ($curahHujanData as $row) {
            $nilai = (float) $row['nilai_curah_hujan'];
            if ($nilai < 20) {
                $stats['aman']++;
            } elseif ($nilai <= 50) {
                $stats['siaga']++;
            } else {
                $stats['resiko']++;
            }
            
            $formattedData[] = [
                'nama_kecamatan' => $row['nama_kecamatan'],
                'nilai_curah_hujan' => $row['nilai_curah_hujan'],
                'tanggal_formatted' => date('d M Y', strtotime($row['tanggal'])),
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats,
            'curahHujan' => $formattedData,
        ]);
    }

    public function curahHujan()
    {
        $data = $this->curahHujanModel->getWithKecamatan();
        $kecamatan = $this->kecamatanModel->findAll();

        return view('admin/curah_hujan', [
            'title' => 'Data Curah Hujan',
            'data' => $data,
            'kecamatan' => $kecamatan,
        ]);
    }

    public function curahHujanCreate()
    {
        $kecamatan = $this->kecamatanModel->findAll();
        return view('admin/curah_hujan_form', [
            'title' => 'Tambah Data Curah Hujan',
            'kecamatan' => $kecamatan,
            'data' => null,
        ]);
    }

    public function curahHujanStore()
    {
        $this->curahHujanModel->insert([
            'kecamatan_id' => $this->request->getPost('kecamatan_id'),
            'nilai_curah_hujan' => $this->request->getPost('nilai_curah_hujan'),
            'tanggal' => $this->request->getPost('tanggal'),
        ]);

        return redirect()->to('/admin')->with('success', 'Data berhasil ditambahkan');
    }

    public function curahHujanEdit($id)
    {
        $data = $this->curahHujanModel->find($id);
        $kecamatan = $this->kecamatanModel->findAll();

        return view('admin/curah_hujan_form', [
            'title' => 'Edit Data Curah Hujan',
            'kecamatan' => $kecamatan,
            'data' => $data,
        ]);
    }

    public function curahHujanUpdate($id)
    {
        $this->curahHujanModel->update($id, [
            'kecamatan_id' => $this->request->getPost('kecamatan_id'),
            'nilai_curah_hujan' => $this->request->getPost('nilai_curah_hujan'),
            'tanggal' => $this->request->getPost('tanggal'),
        ]);

        return redirect()->to('/admin')->with('success', 'Data berhasil diperbarui');
    }

    public function curahHujanDelete($id)
    {
        $this->curahHujanModel->delete($id);
        return redirect()->to('/admin')->with('success', 'Data berhasil dihapus');
    }
}
