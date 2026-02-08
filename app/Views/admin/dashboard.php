<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-600">Selamat datang, Admin BMKG</p>
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

<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="mb-4">
        <h2 class="text-xl font-bold text-slate-800">Data Curah Hujan Terbaru</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 text-slate-600">Kecamatan</th>
                    <th class="text-left py-3 px-4 text-slate-600">Curah Hujan</th>
                    <th class="text-left py-3 px-4 text-slate-600">Status</th>
                    <th class="text-left py-3 px-4 text-slate-600">Tanggal</th>
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
                        <td class="py-3 px-4 font-medium"><?= $row['nama_kecamatan'] ?></td>
                        <td class="py-3 px-4"><?= $row['nilai_curah_hujan'] ?> mm</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                <?= $status ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
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
</div>

<script>
function refreshDashboard() {
    fetch('/api/dashboard-stats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('statAman').textContent = data.stats.aman;
                document.getElementById('statSiaga').textContent = data.stats.siaga;
                document.getElementById('statResiko').textContent = data.stats.resiko;
                
                let tableHtml = '';
                data.curahHujan.forEach(row => {
                    let statusClass, status;
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
                    
                    tableHtml += `
                        <tr class="border-b hover:bg-slate-50">
                            <td class="py-3 px-4 font-medium">${row.nama_kecamatan}</td>
                            <td class="py-3 px-4">${row.nilai_curah_hujan} mm</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                    ${status}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">${row.tanggal_formatted}</td>
                        </tr>
                    `;
                });
                document.getElementById('dataTable').innerHTML = tableHtml;
                document.getElementById('lastUpdate').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');
            }
        });
}

setInterval(refreshDashboard, 30000);
</script>
<?= $this->endSection() ?>
