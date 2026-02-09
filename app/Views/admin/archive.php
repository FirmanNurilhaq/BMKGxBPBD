<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">📁 Arsip Data Curah Hujan</h1>
    <p class="text-slate-600 mt-1">Riwayat data curah hujan berdasarkan periode</p>
</div>

<!-- Period Tabs -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex rounded-lg bg-slate-100 p-1">
            <?php 
            $periods = ['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'];
            foreach ($periods as $key => $label): 
            ?>
                <a href="/admin/arsip?period=<?= $key ?>&date=<?= $date ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $period === $key ? 'bg-blue-600 text-white shadow' : 'text-slate-600 hover:text-slate-800' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <form class="flex items-center gap-2 ml-auto" method="get" action="/admin/arsip">
            <input type="hidden" name="period" value="<?= $period ?>">
            <?php if ($period === 'yearly'): ?>
                <select name="date" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>-01-01" <?= date('Y', strtotime($date)) == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            <?php elseif ($period === 'monthly'): ?>
                <input type="month" name="date" value="<?= date('Y-m', strtotime($date)) ?>" 
                       onchange="this.value += '-01'; this.form.submit();"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <?php else: ?>
                <input type="date" name="date" value="<?= $date ?>" onchange="this.form.submit()"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <?php endif; ?>
        </form>
    </div>
    <div class="mt-3 text-sm text-slate-500">
        📅 Menampilkan: <strong><?= $periodLabel ?></strong>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <?php if ($period === 'yearly' && $yearlyData): ?>
        <!-- Yearly Aggregated View -->
        <?php if (empty($yearlyData)): ?>
            <div class="text-center py-12 text-slate-400">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-lg">Tidak ada data untuk periode ini</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-slate-600">Kecamatan</th>
                            <th class="text-left py-3 px-4 text-slate-600">Bulan</th>
                            <th class="text-left py-3 px-4 text-slate-600">Rata-rata (mm)</th>
                            <th class="text-left py-3 px-4 text-slate-600">Maks (mm)</th>
                            <th class="text-left py-3 px-4 text-slate-600">Min (mm)</th>
                            <th class="text-left py-3 px-4 text-slate-600">Jumlah Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        foreach ($yearlyData as $row): 
                        ?>
                            <tr class="border-b hover:bg-slate-50">
                                <td class="py-3 px-4 font-medium"><?= esc($row['nama_kecamatan']) ?></td>
                                <td class="py-3 px-4"><?= $bulanNames[(int)$row['bulan']] ?></td>
                                <td class="py-3 px-4"><?= number_format((float)$row['avg_rainfall'], 1) ?></td>
                                <td class="py-3 px-4"><?= number_format((float)$row['max_rainfall'], 1) ?></td>
                                <td class="py-3 px-4"><?= number_format((float)$row['min_rainfall'], 1) ?></td>
                                <td class="py-3 px-4"><?= $row['total_entries'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Daily/Weekly/Monthly View -->
        <?php if (empty($data)): ?>
            <div class="text-center py-12 text-slate-400">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-lg">Tidak ada data untuk periode ini</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-slate-600">Kecamatan</th>
                            <th class="text-left py-3 px-4 text-slate-600">Curah Hujan</th>
                            <th class="text-left py-3 px-4 text-slate-600">Status</th>
                            <th class="text-left py-3 px-4 text-slate-600">Tanggal</th>
                            <th class="text-left py-3 px-4 text-slate-600">Waktu</th>
                            <?php if ($period === 'daily'): ?>
                            <th class="text-left py-3 px-4 text-slate-600">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <?php
                            $nilai = (float) $row['nilai_curah_hujan'];
                            $rowDate = date('Y-m-d', strtotime($row['tanggal']));
                            $isToday = ($rowDate === $today);
                            
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
                                <td class="py-3 px-4 text-slate-500"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="py-3 px-4 text-slate-500"><?= date('H:i', strtotime($row['tanggal'])) ?></td>
                                <?php if ($period === 'daily'): ?>
                                <td class="py-3 px-4">
                                    <?php if ($isToday): ?>
                                        <div class="flex space-x-2">
                                            <a href="/admin/curah-hujan/edit/<?= $row['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                            <a href="/admin/curah-hujan/delete/<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">🔒 Arsip</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-slate-500">
                Total: <?= count($data) ?> entri
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
