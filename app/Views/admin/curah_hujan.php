<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Data Curah Hujan</h1>
        <p class="text-slate-600">Kelola data curah hujan per kecamatan</p>
    </div>
    <a href="/admin/curah-hujan/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
        + Tambah Data
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left py-4 px-6 text-slate-600 font-semibold">Kecamatan</th>
                    <th class="text-left py-4 px-6 text-slate-600 font-semibold">Curah Hujan (mm)</th>
                    <th class="text-left py-4 px-6 text-slate-600 font-semibold">Status</th>
                    <th class="text-left py-4 px-6 text-slate-600 font-semibold">Tanggal</th>
                    <th class="text-left py-4 px-6 text-slate-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
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
                        <td class="py-4 px-6 font-medium"><?= $row['nama_kecamatan'] ?></td>
                        <td class="py-4 px-6"><?= $row['nilai_curah_hujan'] ?></td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                <?= $status ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-500"><?= date('d M Y H:i', strtotime($row['tanggal'])) ?></td>
                        <td class="py-4 px-6">
                            <div class="flex space-x-2">
                                <a href="/admin/curah-hujan/edit/<?= $row['id'] ?>" 
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">
                                    Edit
                                </a>
                                <a href="/admin/curah-hujan/delete/<?= $row['id'] ?>" 
                                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
