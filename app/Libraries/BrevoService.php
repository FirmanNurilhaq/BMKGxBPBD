<?php

namespace App\Libraries;

class BrevoService
{
    private $apiKey;
    private $senderEmail;
    private $senderName;
    private $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY', '');
        $this->senderEmail = env('BREVO_SENDER_EMAIL', 'noreply@example.com');
        $this->senderName = env('BREVO_SENDER_NAME', 'SIG Mitigasi Banjir');
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail(string $email, string $name, string $token): bool
    {
        $baseUrl = rtrim(env('app.baseURL', 'http://localhost:8080'), '/');
        $verifyUrl = $baseUrl . '/verify-email/' . $token;

        $htmlContent = $this->getVerificationEmailTemplate($name, $verifyUrl);

        $data = [
            'sender' => [
                'name' => $this->senderName,
                'email' => $this->senderEmail
            ],
            'to' => [
                [
                    'email' => $email,
                    'name' => $name
                ]
            ],
            'subject' => 'Verifikasi Email - SIG Mitigasi Banjir',
            'htmlContent' => $htmlContent
        ];

        return $this->sendEmail($data);
    }

    /**
     * Send email via Brevo API
     */
    private function sendEmail(array $data): bool
    {
        $ch = curl_init($this->apiUrl);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'api-key: ' . $this->apiKey
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Brevo API cURL error: ' . $error);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            log_message('info', 'Verification email sent successfully');
            return true;
        }

        log_message('error', 'Brevo API error: HTTP ' . $httpCode . ' - ' . $response);
        return false;
    }

    /**
     * Get email template HTML
     */
    private function getVerificationEmailTemplate(string $name, string $verifyUrl): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: #1e293b; padding: 30px; border-radius: 12px 12px 0 0; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 24px;">🌧️ SIG Mitigasi Banjir</h1>
                    <p style="color: #94a3b8; margin: 10px 0 0;">Kota Serang</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 12px 12px;">
                    <h2 style="color: #1e293b; margin-top: 0;">Halo, ' . htmlspecialchars($name) . '!</h2>
                    <p style="color: #475569; line-height: 1.6;">
                        Terima kasih telah mendaftar di SIG Mitigasi Banjir Kota Serang. 
                        Untuk menyelesaikan pendaftaran, silakan verifikasi email Anda dengan menekan tombol di bawah ini:
                    </p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $verifyUrl . '" 
                           style="background-color: #2563eb; color: white; padding: 14px 28px; 
                                  text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                            Verifikasi Email
                        </a>
                    </div>
                    <p style="color: #64748b; font-size: 14px;">
                        Atau salin link berikut ke browser Anda:<br>
                        <a href="' . $verifyUrl . '" style="color: #2563eb; word-break: break-all;">' . $verifyUrl . '</a>
                    </p>
                    <p style="color: #64748b; font-size: 14px;">
                        Link ini akan kadaluarsa dalam 24 jam.
                    </p>
                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                    <p style="color: #94a3b8; font-size: 12px; text-align: center;">
                        Jika Anda tidak mendaftar di SIG Mitigasi Banjir, abaikan email ini.
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Send rainfall alert to multiple users
     */
    public function sendRainfallAlert(array $users, string $kecamatan, float $nilai, string $level): int
    {
        $sentCount = 0;
        $tanggal = date('d F Y');

        foreach ($users as $user) {
            $htmlContent = $this->getRainfallAlertTemplate($user['nama'], $kecamatan, $nilai, $level, $tanggal);
            
            $subject = $level === 'high' 
                ? "⚠️ PERINGATAN BANJIR - Kecamatan $kecamatan"
                : "🌧️ Siaga Cuaca - Kecamatan $kecamatan";

            $data = [
                'sender' => [
                    'name' => $this->senderName,
                    'email' => $this->senderEmail
                ],
                'to' => [
                    [
                        'email' => $user['email'],
                        'name' => $user['nama']
                    ]
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ];

            if ($this->sendEmail($data)) {
                $sentCount++;
            }
        }

        log_message('info', "Rainfall alert sent to $sentCount users for $kecamatan ($level)");
        return $sentCount;
    }

    /**
     * Get rainfall alert email template
     */
    private function getRainfallAlertTemplate(string $name, string $kecamatan, float $nilai, string $level, string $tanggal): string
    {
        $isHigh = $level === 'high';
        $headerColor = $isHigh ? '#dc2626' : '#f59e0b';
        $headerIcon = $isHigh ? '⚠️' : '🌧️';
        $statusText = $isHigh ? 'RESIKO TINGGI' : 'SIAGA';
        
        $mitigationTips = $isHigh ? '
            <li>Segera evakuasi ke tempat yang lebih tinggi jika berada di daerah rawan banjir</li>
            <li>Hindari berjalan atau berkendara di area yang tergenang air</li>
            <li>Matikan listrik jika air mulai masuk ke rumah</li>
            <li>Siapkan tas darurat berisi dokumen penting, obat-obatan, dan makanan</li>
            <li>Pantau informasi dari BMKG dan BPBD setempat</li>
            <li>Hubungi hotline BPBD jika membutuhkan bantuan evakuasi</li>
        ' : '
            <li>Pantau kondisi cuaca dan tinggi muka air secara berkala</li>
            <li>Siapkan barang-barang penting yang mudah dibawa</li>
            <li>Pastikan saluran air di sekitar rumah tidak tersumbat</li>
            <li>Charge handphone dan siapkan senter/lampu darurat</li>
            <li>Simpan nomor darurat BPBD di handphone Anda</li>
            <li>Informasikan kepada keluarga tentang kondisi cuaca</li>
        ';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: ' . $headerColor . '; padding: 30px; border-radius: 12px 12px 0 0; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">' . $headerIcon . ' ' . $statusText . '</h1>
                    <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 18px;">Kecamatan ' . htmlspecialchars($kecamatan) . '</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 12px 12px;">
                    <h2 style="color: #1e293b; margin-top: 0;">Halo, ' . htmlspecialchars($name) . '!</h2>
                    
                    <div style="background-color: #f8fafc; border-left: 4px solid ' . $headerColor . '; padding: 15px; margin: 20px 0;">
                        <p style="margin: 0; color: #475569;">
                            <strong>Curah Hujan:</strong> ' . number_format($nilai, 1) . ' mm<br>
                            <strong>Lokasi:</strong> Kecamatan ' . htmlspecialchars($kecamatan) . '<br>
                            <strong>Tanggal:</strong> ' . $tanggal . '
                        </p>
                    </div>

                    <h3 style="color: #1e293b;">Rekomendasi Mitigasi:</h3>
                    <ul style="color: #475569; line-height: 1.8;">
                        ' . $mitigationTips . '
                    </ul>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . rtrim(env('app.baseURL', 'http://localhost:8080'), '/') . '" 
                           style="background-color: #2563eb; color: white; padding: 14px 28px; 
                                  text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                            Lihat Peta Risiko
                        </a>
                    </div>

                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                    <p style="color: #94a3b8; font-size: 12px; text-align: center;">
                        Pesan ini dikirim otomatis oleh SIG Mitigasi Banjir Kota Serang.<br>
                        Tetap waspada dan jaga keselamatan Anda.
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}
