<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? 'Verifikasi Berhasil' : 'Verifikasi Gagal' ?> - SIG Mitigasi Banjir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-800 to-slate-900 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md text-center">
        <?php if ($success): ?>
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">✅</span>
            </div>
            <h1 class="text-2xl font-bold text-green-600 mb-4">Verifikasi Berhasil!</h1>
        <?php else: ?>
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">❌</span>
            </div>
            <h1 class="text-2xl font-bold text-red-600 mb-4">Verifikasi Gagal</h1>
        <?php endif; ?>
        
        <p class="text-slate-600 mb-6">
            <?= esc($message) ?>
        </p>

        <div class="space-y-3">
            <?php if ($success): ?>
                <a href="/login" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                    Login Sekarang
                </a>
            <?php else: ?>
                <a href="/register" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                    Daftar Ulang
                </a>
                <a href="/login" class="block text-blue-600 hover:underline">
                    ← Kembali ke Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
