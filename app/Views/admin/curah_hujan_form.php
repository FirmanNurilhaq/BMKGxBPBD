<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-800"><?= $title ?></h1>
    <p class="text-slate-600"><?= $data ? 'Edit data curah hujan' : 'Tambah data curah hujan baru' ?></p>
</div>

<div class="bg-white rounded-xl shadow-lg p-6 max-w-xl">
    <form action="<?= $data ? '/admin/curah-hujan/update/' . $data['id'] : '/admin/curah-hujan/store' ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kecamatan</label>
            <select name="kecamatan_id" required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Kecamatan</option>
                <?php foreach ($kecamatan as $kec): ?>
                    <option value="<?= $kec['id'] ?>" <?= ($data && $data['kecamatan_id'] == $kec['id']) ? 'selected' : '' ?>>
                        <?= $kec['nama_kecamatan'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nilai Curah Hujan (mm)</label>
            <input type="number" step="0.1" name="nilai_curah_hujan" required
                value="<?= $data['nilai_curah_hujan'] ?? '' ?>"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Contoh: 25.5">
            <p class="text-sm text-slate-500 mt-1">< 20mm = Aman, 20-50mm = Siaga, > 50mm = Resiko</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal & Waktu</label>
            <input type="datetime-local" name="tanggal" id="tanggalInput" required
                value="<?= $data ? date('Y-m-d\TH:i', strtotime($data['tanggal'])) : '' ?>"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <div class="flex space-x-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                <?= $data ? 'Update' : 'Simpan' ?>
            </button>
            <a href="/admin"
                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold px-6 py-3 rounded-lg transition">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tanggalInput = document.getElementById('tanggalInput');
    if (tanggalInput && !tanggalInput.value) {
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        tanggalInput.value = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
    }
});
</script>
<?= $this->endSection() ?>
