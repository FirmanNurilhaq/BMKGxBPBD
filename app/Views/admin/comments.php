<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">💬 Monitoring Komentar</h1>
        <p class="text-slate-600 mt-1">Pantau dan kelola komentar pengguna</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <form method="get" action="/admin/komentar" class="flex items-center gap-4">
        <label class="text-sm text-slate-600">Filter Tanggal:</label>
        <input type="date" name="date" value="<?= $filterDate ?? '' ?>" 
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
            Filter
        </button>
        <?php if ($filterDate): ?>
            <a href="/admin/komentar" class="text-sm text-slate-500 hover:text-slate-700">✕ Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Comments Table -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <?php if (empty($comments)): ?>
        <div class="text-center py-12 text-slate-400">
            <div class="text-5xl mb-4">💬</div>
            <p class="text-lg">Tidak ada komentar</p>
            <p class="text-sm mt-2"><?= $filterDate ? 'Coba pilih tanggal lain' : 'Belum ada komentar dari pengguna' ?></p>
        </div>
    <?php else: ?>
        <div class="mb-4 text-sm text-slate-500">
            Menampilkan <?= count($comments) ?> komentar <?= $filterDate ? 'pada ' . date('d M Y', strtotime($filterDate)) : '(semua)' ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-slate-600">Pengguna</th>
                        <th class="text-left py-3 px-4 text-slate-600">Email</th>
                        <th class="text-left py-3 px-4 text-slate-600">Komentar</th>
                        <th class="text-left py-3 px-4 text-slate-600">Waktu</th>
                        <th class="text-left py-3 px-4 text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div>
                                    <p class="font-medium text-slate-800"><?= esc($comment['nama']) ?></p>
                                    <p class="text-xs text-slate-400">@<?= esc($comment['username']) ?></p>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-500"><?= esc($comment['email']) ?></td>
                            <td class="py-3 px-4 text-sm text-slate-700 max-w-md">
                                <p class="line-clamp-2"><?= esc($comment['content']) ?></p>
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-500 whitespace-nowrap">
                                <?= date('d M Y', strtotime($comment['created_at'])) ?><br>
                                <span class="text-xs"><?= date('H:i', strtotime($comment['created_at'])) ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="/admin/komentar/delete/<?= $comment['id'] ?>" 
                                   onclick="return confirm('Yakin hapus komentar ini?')"
                                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
