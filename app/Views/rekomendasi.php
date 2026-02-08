<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="<?= base_url() ?>" class="text-cyan-400 hover:text-cyan-300 text-sm mb-4 inline-block">
            ← Kembali ke Peta
        </a>
        <h1 class="text-3xl font-bold text-white mb-4">Rekomendasi Mitigasi Banjir</h1>
        <p class="text-gray-400">Panduan dan langkah-langkah mitigasi berdasarkan tingkat risiko banjir.</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="<?= base_url('rekomendasi') ?>" 
            class="px-4 py-2 rounded-lg transition-colors <?= !$tingkat ? 'bg-cyan-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' ?>">
            Semua
        </a>
        <a href="<?= base_url('rekomendasi/aman') ?>" 
            class="px-4 py-2 rounded-lg transition-colors <?= $tingkat === 'aman' ? 'bg-green-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' ?>">
            Aman
        </a>
        <a href="<?= base_url('rekomendasi/siaga') ?>" 
            class="px-4 py-2 rounded-lg transition-colors <?= $tingkat === 'siaga' ? 'bg-yellow-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' ?>">
            Siaga
        </a>
        <a href="<?= base_url('rekomendasi/bahaya') ?>" 
            class="px-4 py-2 rounded-lg transition-colors <?= $tingkat === 'bahaya' ? 'bg-red-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' ?>">
            Bahaya
        </a>
    </div>

    <div class="space-y-6">
        <?php if (empty($rekomendasis)): ?>
        <div class="glass-card rounded-xl p-8 text-center">
            <p class="text-gray-400">Belum ada rekomendasi untuk tingkat risiko ini.</p>
        </div>
        <?php else: ?>
            <?php foreach ($rekomendasis as $rek): ?>
            <div class="glass-card rounded-xl overflow-hidden <?= $rek['tingkat_risiko'] === 'bahaya' ? 'border-l-4 border-red-500' : ($rek['tingkat_risiko'] === 'siaga' ? 'border-l-4 border-yellow-500' : 'border-l-4 border-green-500') ?>">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 text-sm font-semibold rounded <?= $rek['tingkat_risiko'] === 'bahaya' ? 'bg-red-500/20 text-red-400' : ($rek['tingkat_risiko'] === 'siaga' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') ?>">
                            <?= ucfirst($rek['tingkat_risiko']) ?>
                        </span>
                        <?php if ($rek['kontak_darurat']): ?>
                        <span class="text-gray-400 text-sm">📞 <?= $rek['kontak_darurat'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-3"><?= $rek['judul'] ?></h3>
                    <p class="text-gray-300 mb-4"><?= $rek['deskripsi'] ?></p>
                    
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-2">Langkah Mitigasi:</h4>
                        <div class="text-gray-300 whitespace-pre-line"><?= $rek['langkah_mitigasi'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
