<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIG Mitigasi Banjir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-800 to-slate-900 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🌧️</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Login</h1>
            <p class="text-slate-500">SIG Mitigasi Banjir Kota Serang</p>
        </div>

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

        <form action="/login" method="post" class="space-y-6">
            <?= csrf_field() ?>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input type="text" name="username" required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan username">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan password">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                Login
            </button>
        </form>

        <p class="text-center text-slate-500 text-sm mt-6">
            Belum punya akun? <a href="/register" class="text-blue-600 hover:underline">Daftar</a>
        </p>
        <p class="text-center text-slate-500 text-sm mt-2">
            <a href="/" class="text-blue-600 hover:underline">← Kembali ke Peta</a>
        </p>
    </div>
</body>
</html>

