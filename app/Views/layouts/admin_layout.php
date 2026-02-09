<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin BMKG' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <nav class="bg-slate-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="/admin" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-xl">🌧️</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Admin BMKG</h1>
                        <p class="text-xs text-slate-400">SIG Mitigasi Banjir</p>
                    </div>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="/admin" class="hover:text-blue-400 transition">Dashboard</a>
                    <a href="/admin/arsip" class="hover:text-blue-400 transition">Arsip Data</a>
                    <a href="/admin/komentar" class="hover:text-blue-400 transition">Komentar</a>
                    <a href="/" class="hover:text-blue-400 transition">Lihat Peta</a>
                    <a href="/logout" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
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
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
