<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-600">Selamat datang, <?= esc(session()->get('nama') ?? 'Admin BMKG') ?></p>
    </div>
    <div class="flex items-center space-x-4">
        <a href="/admin/curah-hujan/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
            <span>+</span>
            <span>Tambah Data</span>
        </a>
        <div class="flex items-center space-x-2 text-sm text-slate-500">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span>Auto-refresh (30s)</span>
            <span id="lastUpdate"><?= date('H:i:s') ?></span>
        </div>
    </div>
</div>

<div id="statsCards" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-green-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Kecamatan Aman</p>
                <p class="text-4xl font-bold" id="statAman"><?= $stats['aman'] ?></p>
            </div>
            <div class="text-5xl opacity-50">✓</div>
        </div>
        <p class="text-green-100 text-sm mt-2">Curah hujan < 20mm</p>
    </div>
    
    <div class="bg-yellow-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">Kecamatan Siaga</p>
                <p class="text-4xl font-bold" id="statSiaga"><?= $stats['siaga'] ?></p>
            </div>
            <div class="text-5xl opacity-50">⚠</div>
        </div>
        <p class="text-yellow-100 text-sm mt-2">Curah hujan 20-50mm</p>
    </div>
    
    <div class="bg-red-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm">Kecamatan Resiko</p>
                <p class="text-4xl font-bold" id="statResiko"><?= $stats['resiko'] ?></p>
            </div>
            <div class="text-5xl opacity-50">⛔</div>
        </div>
        <p class="text-red-100 text-sm mt-2">Curah hujan > 50mm</p>
    </div>
</div>

<!-- Trendline Chart -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">📈 Tren Curah Hujan (7 Hari Terakhir)</h2>
    </div>
    <div class="h-64">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<!-- Today's Data Table -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">📋 Data Curah Hujan Hari Ini</h2>
        <span class="text-sm text-slate-500"><?= date('d M Y') ?></span>
    </div>
    
    <?php if (empty($curahHujanData)): ?>
        <div class="text-center py-12 text-slate-400">
            <div class="text-5xl mb-4">📭</div>
            <p class="text-lg">Belum ada data curah hujan hari ini</p>
            <p class="text-sm mt-2">Klik "Tambah Data" untuk menambahkan data baru</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-slate-600">Kecamatan</th>
                        <th class="text-left py-3 px-4 text-slate-600">Curah Hujan</th>
                        <th class="text-left py-3 px-4 text-slate-600">Status</th>
                        <th class="text-left py-3 px-4 text-slate-600">Waktu Input</th>
                        <th class="text-left py-3 px-4 text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTable">
                    <?php foreach ($curahHujanData as $row): ?>
                        <?php
                        $nilai = (float) $row['nilai_curah_hujan'];
                        if ($nilai < 20) {
                            $status = 'Aman';
                            $statusClass = 'bg-green-100 text-green-700';
                        } elseif ($nilai <= 50) {
                            $status = 'Siaga';
                            $statusClass = 'bg-yellow-100 text-yellow-700';
                        } else {
                            $status = 'Resiko';
                            $statusClass = 'bg-red-100 text-red-700';
                        }
                        ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="py-3 px-4 font-medium"><?= esc($row['nama_kecamatan']) ?></td>
                            <td class="py-3 px-4"><?= $row['nilai_curah_hujan'] ?> mm</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500"><?= date('H:i', strtotime($row['tanggal'])) ?></td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="/admin/curah-hujan/edit/<?= $row['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                    <a href="/admin/curah-hujan/delete/<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize trend chart
const trendData = <?= json_encode($trendData) ?>;
const ctx = document.getElementById('trendChart').getContext('2d');

const labels = trendData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
});
const values = trendData.map(d => parseFloat(d.avg_rainfall).toFixed(1));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Rata-rata Curah Hujan (mm)',
            data: values,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#3b82f6',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'mm' }
            }
        }
    }
});

// Auto-refresh dashboard
function refreshDashboard() {
    fetch('/api/dashboard-stats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('statAman').textContent = data.stats.aman;
                document.getElementById('statSiaga').textContent = data.stats.siaga;
                document.getElementById('statResiko').textContent = data.stats.resiko;
                document.getElementById('lastUpdate').textContent = data.lastUpdate;
                
                const tbody = document.getElementById('dataTable');
                if (tbody && data.curahHujan.length > 0) {
                    let html = '';
                    data.curahHujan.forEach(row => {
                        let status, statusClass;
                        if (row.nilai_curah_hujan < 20) {
                            status = 'Aman';
                            statusClass = 'bg-green-100 text-green-700';
                        } else if (row.nilai_curah_hujan <= 50) {
                            status = 'Siaga';
                            statusClass = 'bg-yellow-100 text-yellow-700';
                        } else {
                            status = 'Resiko';
                            statusClass = 'bg-red-100 text-red-700';
                        }
                        
                        html += `
                            <tr class="border-b hover:bg-slate-50">
                                <td class="py-3 px-4 font-medium">${row.nama_kecamatan}</td>
                                <td class="py-3 px-4">${row.nilai_curah_hujan} mm</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">${status}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-500">${row.time_formatted}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="/admin/curah-hujan/edit/${row.id}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                        <a href="/admin/curah-hujan/delete/${row.id}" onclick="return confirm('Yakin hapus data ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            }
        });
}

setInterval(refreshDashboard, 30000);
</script>
<?= $this->endSection() ?>

