<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - SIG Mitigasi Banjir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-800 to-slate-900 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl">📧</span>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800 mb-4">Cek Email Anda</h1>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <p class="text-slate-600 mb-6">
            Kami telah mengirim email verifikasi ke alamat email Anda. 
            Silakan klik link di email tersebut untuk mengaktifkan akun Anda.
        </p>
        
        <div class="bg-slate-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-slate-500">
                <strong>Tidak menerima email?</strong><br>
                Cek folder spam atau klik tombol di bawah untuk mengirim ulang.
            </p>
        </div>

        <div class="space-y-3">
            <a href="/resend-verification<?= session()->getFlashdata('pending_email') ? '?email=' . urlencode(session()->getFlashdata('pending_email')) : '' ?>" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                Kirim Ulang Email
            </a>
            
            <a href="/login" class="block text-blue-600 hover:underline">
                ← Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>
