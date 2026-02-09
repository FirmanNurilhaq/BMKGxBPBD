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
        // Get today's data for dashboard
        $curahHujanData = $this->curahHujanModel->getTodayEntries();
        
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

        // Get trend data for chart
        $trendData = $this->curahHujanModel->getHistoricalTrend(7);

        return view('admin/dashboard', [
            'title' => 'Dashboard Admin BMKG',
            'stats' => $stats,
            'curahHujanData' => $curahHujanData,
            'trendData' => $trendData,
        ]);
    }

    public function dashboardStats()
    {
        $curahHujanData = $this->curahHujanModel->getTodayEntries();
        
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
                'id' => $row['id'],
                'nama_kecamatan' => $row['nama_kecamatan'],
                'nilai_curah_hujan' => $row['nilai_curah_hujan'],
                'tanggal' => $row['tanggal'],
                'time_formatted' => date('H:i', strtotime($row['tanggal'])),
                'date_formatted' => date('d M Y', strtotime($row['tanggal'])),
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats,
            'curahHujan' => $formattedData,
            'lastUpdate' => date('H:i:s'),
        ]);
    }

    public function trendData()
    {
        $trendData = $this->curahHujanModel->getHistoricalTrend(7);
        
        $labels = [];
        $values = [];
        
        foreach ($trendData as $row) {
            $labels[] = date('d M', strtotime($row['date']));
            $values[] = round((float) $row['avg_rainfall'], 1);
        }

        return $this->response->setJSON([
            'success' => true,
            'labels' => $labels,
            'values' => $values,
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
        $kecamatanId = $this->request->getPost('kecamatan_id');
        $nilai = (float) $this->request->getPost('nilai_curah_hujan');
        
        $this->curahHujanModel->insert([
            'kecamatan_id' => $kecamatanId,
            'nilai_curah_hujan' => $nilai,
            'tanggal' => $this->request->getPost('tanggal'),
        ]);

        // Send rainfall alerts if intensity is medium or high
        $this->sendRainfallAlertIfNeeded($kecamatanId, $nilai);

        return redirect()->to('/admin')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Send rainfall alert to verified public users if intensity is medium/high
     */
    private function sendRainfallAlertIfNeeded(int $kecamatanId, float $nilai)
    {
        // Only send alerts for medium (20-50) or high (>50) intensity
        if ($nilai < 20) {
            return; // Low intensity, no alert needed
        }

        $level = $nilai > 50 ? 'high' : 'medium';
        
        // Get kecamatan name
        $kecamatan = $this->kecamatanModel->find($kecamatanId);
        if (!$kecamatan) {
            return;
        }

        // Get all verified public users
        $userModel = new \App\Models\UserModel();
        $users = $userModel->getVerifiedPublicUsers();
        
        if (empty($users)) {
            log_message('info', 'No verified users to send rainfall alert');
            return;
        }

        // Send alerts via Brevo
        $brevo = new \App\Libraries\BrevoService();
        $sentCount = $brevo->sendRainfallAlert($users, $kecamatan['nama_kecamatan'], $nilai, $level);
        
        log_message('info', "Sent $level rainfall alert to $sentCount users for {$kecamatan['nama_kecamatan']}");
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
        $kecamatanId = $this->request->getPost('kecamatan_id');
        $nilai = (float) $this->request->getPost('nilai_curah_hujan');

        $this->curahHujanModel->update($id, [
            'kecamatan_id' => $kecamatanId,
            'nilai_curah_hujan' => $nilai,
            'tanggal' => $this->request->getPost('tanggal'),
        ]);

        // Send rainfall alerts if intensity is medium or high
        $this->sendRainfallAlertIfNeeded($kecamatanId, $nilai);

        return redirect()->to('/admin')->with('success', 'Data berhasil diperbarui');
    }

    public function curahHujanDelete($id)
    {
        $this->curahHujanModel->delete($id);
        return redirect()->to('/admin')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Archive page - historical data view
     */
    public function archive()
    {
        $period = $this->request->getGet('period') ?? 'daily';
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $today = date('Y-m-d');
        $data = [];
        $periodLabel = '';
        $yearlyData = null;

        switch ($period) {
            case 'daily':
                $data = $this->curahHujanModel->getByDateRange($date, $date);
                $periodLabel = date('d M Y', strtotime($date));
                break;

            case 'weekly':
                $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
                $data = $this->curahHujanModel->getByDateRange($weekStart, $weekEnd);
                $periodLabel = date('d M', strtotime($weekStart)) . ' - ' . date('d M Y', strtotime($weekEnd));
                break;

            case 'monthly':
                $year = date('Y', strtotime($date));
                $month = date('m', strtotime($date));
                $monthStart = "$year-$month-01";
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                $data = $this->curahHujanModel->getByDateRange($monthStart, $monthEnd);
                $periodLabel = date('F Y', strtotime($date));
                break;

            case 'yearly':
                $year = date('Y', strtotime($date));
                $yearlyData = $this->curahHujanModel->getYearlyAggregated($year);
                $periodLabel = "Tahun $year";
                break;
        }

        return view('admin/archive', [
            'title' => 'Arsip Data Curah Hujan',
            'data' => $data,
            'yearlyData' => $yearlyData,
            'period' => $period,
            'date' => $date,
            'today' => $today,
            'periodLabel' => $periodLabel,
        ]);
    }

    /**
     * Comment monitoring page
     */
    public function comments()
    {
        $commentModel = new \App\Models\CommentModel();
        $date = $this->request->getGet('date');
        $comments = $commentModel->getAllComments($date);

        return view('admin/comments', [
            'title' => 'Monitoring Komentar',
            'comments' => $comments,
            'filterDate' => $date,
        ]);
    }

    /**
     * Delete a comment
     */
    public function deleteComment($id)
    {
        $commentModel = new \App\Models\CommentModel();
        $commentModel->delete($id);
        return redirect()->to('/admin/komentar')->with('success', 'Komentar berhasil dihapus');
    }
}
