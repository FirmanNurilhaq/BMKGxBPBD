<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIG Mitigasi Banjir' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
        }
        .leaflet-popup-content {
            margin: 0;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <nav class="bg-slate-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-xl">🌧️</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">SIG Mitigasi Banjir</h1>
                        <p class="text-xs text-slate-400">Kota Serang</p>
                    </div>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="/" class="hover:text-blue-400 transition">Peta</a>
                    <?php if (session()->get('logged_in')): ?>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <a href="/admin" class="hover:text-blue-400 transition">Dashboard</a>
                        <?php endif; ?>
                        <span class="text-slate-300">Halo, <?= esc(session()->get('nama')) ?></span>
                        <a href="/logout" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition">Logout</a>
                    <?php else: ?>
                        <a href="/register" class="hover:text-blue-400 transition">Daftar</a>
                        <a href="/login" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
