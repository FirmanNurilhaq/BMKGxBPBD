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
        $rainfallData = $curahHujanModel->getTodayByKecamatan();
        $hasData = !empty($rainfallData);

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
            'hasData' => $hasData,
        ]);
    }

    public function apiMapData()
    {
        $curahHujanModel = new CurahHujanModel();
        $rainfallData = $curahHujanModel->getTodayByKecamatan();
        $hasData = !empty($rainfallData);

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

        return $this->response->setJSON([
            'success' => true, 
            'data' => $result,
            'hasData' => $hasData,
            'lastUpdate' => date('H:i:s'),
        ]);
    }

    /**
     * Get today's comments
     */
    public function getComments()
    {
        $commentModel = new \App\Models\CommentModel();
        $comments = $commentModel->getTodayComments();

        $formattedComments = [];
        foreach ($comments as $comment) {
            $formattedComments[] = [
                'id' => $comment['id'],
                'nama' => $comment['nama'],
                'content' => $comment['content'],
                'time' => date('H:i', strtotime($comment['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'comments' => $formattedComments,
            'date' => date('d M Y'),
        ]);
    }

    /**
     * Post a new comment (requires login)
     */
    public function postComment()
    {
        // Check if logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu untuk berkomentar.',
                'requireLogin' => true,
            ])->setStatusCode(401);
        }

        $content = trim($this->request->getPost('content'));

        // Validate content
        if (empty($content)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar tidak boleh kosong.',
            ])->setStatusCode(400);
        }

        if (strlen($content) > 500) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Komentar maksimal 500 karakter.',
            ])->setStatusCode(400);
        }

        // Hardcoded word filter
        $blockedWords = [
            'bodoh', 'goblok', 'tolol', 'bangsat', 'anjing', 'babi', 
            'setan', 'kampret', 'bajingan', 'brengsek', 'sialan',
            'kontol', 'memek', 'ngentot', 'jancok', 'asu',
        ];

        $lowerContent = strtolower($content);
        foreach ($blockedWords as $word) {
            if (strpos($lowerContent, $word) !== false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Komentar mengandung kata yang tidak pantas.',
                ])->setStatusCode(400);
            }
        }

        // Save comment
        $commentModel = new \App\Models\CommentModel();
        $commentModel->insert([
            'user_id' => session()->get('user_id'),
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Komentar berhasil ditambahkan.',
        ]);
    }
}
