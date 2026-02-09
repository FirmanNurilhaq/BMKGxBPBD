<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="relative w-full" style="height: calc(100vh - 64px);">
    <div id="map" class="w-full h-full z-0"></div>
    
    <!-- No Data Banner -->
    <?php if (!$hasData): ?>
    <div id="noDataBanner" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[1000] bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-8 text-center max-w-md">
        <div class="text-5xl mb-4">🌤️</div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Data Curah Hujan Hari Ini</h3>
        <p class="text-slate-600 mb-4">Data akan otomatis muncul setelah admin BMKG menginput data curah hujan.</p>
        <div class="flex items-center justify-center gap-2 text-sm text-slate-500">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span>Auto-refresh setiap 30 detik</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Auto-refresh Indicator -->
    <div class="absolute bottom-4 left-4 z-[1000] bg-white/95 backdrop-blur-sm rounded-lg shadow px-3 py-2 flex items-center gap-2 text-sm text-slate-600">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span>Live</span>
        <span id="mapLastUpdate"><?= date('H:i:s') ?></span>
    </div>
    
    <!-- Legend Panel -->
    <div class="absolute top-4 left-4 z-[1000] bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-4 max-w-xs">
        <h2 class="text-lg font-bold text-slate-800 mb-3">Peta Risiko Banjir</h2>
        <p class="text-sm text-slate-600 mb-4">Kota Serang - Data BMKG</p>
        
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded" style="background: #ef4444;"></span>
                <span class="text-sm text-slate-700">Resiko (> 50mm)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded" style="background: #fbbf24;"></span>
                <span class="text-sm text-slate-700">Siaga (20-50mm)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded" style="background: #22c55e;"></span>
                <span class="text-sm text-slate-700">Aman (< 20mm)</span>
            </div>
        </div>
    </div>

    <!-- Comment Sidebar -->
    <div class="absolute top-4 right-4 z-[1000] bg-white/95 backdrop-blur-sm rounded-xl shadow-lg w-80 max-h-[calc(100vh-100px)] flex flex-col">
        <div class="p-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                💬 Komentar Hari Ini
            </h3>
            <p id="commentDate" class="text-xs text-slate-500 mt-1"></p>
        </div>
        
        <!-- Comment Form -->
        <div class="p-4 border-b border-slate-200">
            <?php if (session()->get('logged_in')): ?>
                <form id="commentForm" class="space-y-2">
                    <textarea 
                        id="commentInput" 
                        placeholder="Tulis laporan atau komentar Anda..." 
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows="3"
                        maxlength="500"
                    ></textarea>
                    <div class="flex justify-between items-center">
                        <span id="charCount" class="text-xs text-slate-400">0/500</span>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                            Kirim
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <button id="openLoginPopup" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                    🔐 Login untuk berkomentar
                </button>
            <?php endif; ?>
        </div>
        
        <!-- Comments List -->
        <div id="commentsList" class="flex-1 overflow-y-auto p-4 space-y-3">
            <div class="text-center text-slate-400 text-sm py-8">
                Memuat komentar...
            </div>
        </div>
    </div>
</div>

<!-- Login Popup Modal -->
<div id="loginModal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 animate-fadeIn">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🌧️</span>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Bergabung dengan Komunitas!</h3>
            <p class="text-slate-600 mt-2">
                Daftar sekarang untuk berbagi informasi cuaca dan kondisi banjir di wilayah Anda. Bersama kita bisa saling mengingatkan!
            </p>
        </div>
        
        <div class="space-y-3">
            <a href="/register" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-semibold transition">
                Daftar Sekarang - Gratis!
            </a>
            <a href="/login" class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-center py-3 rounded-lg font-medium transition">
                Sudah punya akun? Login
            </a>
        </div>
        
        <button id="closeLoginModal" class="mt-4 w-full text-slate-500 hover:text-slate-700 text-sm">
            Nanti saja
        </button>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn { animation: fadeIn 0.2s ease-out; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const mapData = <?= json_encode($mapData) ?>;
const isLoggedIn = <?= session()->get('logged_in') ? 'true' : 'false' ?>;

// Initialize map
const map = L.map('map').setView([-6.12, 106.15], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

const riskDataByDistrict = {};
mapData.forEach(item => {
    riskDataByDistrict[item.nama_kecamatan.toLowerCase().trim()] = item;
});

function getColor(riskLevel) {
    if (riskLevel === 'resiko') return '#ef4444';
    if (riskLevel === 'siaga') return '#fbbf24';
    return '#22c55e';
}

function getRiskLabel(riskLevel) {
    if (riskLevel === 'resiko') return 'RESIKO TINGGI';
    if (riskLevel === 'siaga') return 'SIAGA';
    return 'AMAN';
}

fetch('/assets/geojson/id3673_kota_serang.geojson')
    .then(res => res.json())
    .then(geojson => {
        L.geoJSON(geojson, {
            style: function(feature) {
                const districtName = feature.properties.district.toLowerCase().trim();
                const data = riskDataByDistrict[districtName];
                const riskLevel = data ? data.risk_level : 'aman';
                
                return {
                    fillColor: getColor(riskLevel),
                    color: getColor(riskLevel),
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.6
                };
            },
            onEachFeature: function(feature, layer) {
                const districtName = feature.properties.district.toLowerCase().trim();
                const data = riskDataByDistrict[districtName];
                
                if (!data) return;
                
                const popupContent = `
                    <div class="p-4 min-w-[280px]">
                        <h3 class="font-bold text-xl text-slate-800 mb-3">Kec. ${data.nama_kecamatan}</h3>
                        
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full text-white" style="background-color: ${data.risk_color}">
                                ${getRiskLabel(data.risk_level)}
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-slate-700">
                            <p><strong>Curah Hujan:</strong> ${data.nilai_curah_hujan} mm</p>
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal).toLocaleDateString('id-ID')}</p>
                        </div>
                        
                        <div class="mt-4 p-3 bg-slate-100 rounded-lg">
                            <p class="text-sm font-semibold text-slate-800 mb-1">Rekomendasi Mitigasi:</p>
                            <p class="text-sm text-slate-600">${data.mitigation}</p>
                        </div>
                    </div>
                `;
                
                layer.bindPopup(popupContent, { maxWidth: 350 });
                
                layer.on('mouseover', function() {
                    layer.setStyle({ weight: 4, fillOpacity: 0.8 });
                });
                
                layer.on('mouseout', function() {
                    layer.setStyle({ weight: 2, fillOpacity: 0.6 });
                });
            }
        }).addTo(map);
        
        // Store geojson layer reference for updates
        window.geoLayer = this;
    });

// Auto-refresh map data every 30 seconds
let geoJsonLayer = null;

function refreshMapData() {
    fetch('/api/map-data')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update risk data
                const newRiskData = {};
                data.data.forEach(item => {
                    newRiskData[item.nama_kecamatan.toLowerCase().trim()] = item;
                });
                
                // Update global data
                Object.assign(riskDataByDistrict, newRiskData);
                
                // Update last update time
                document.getElementById('mapLastUpdate').textContent = data.lastUpdate;
                
                // If we have data now, remove the no-data banner
                if (data.hasData) {
                    const banner = document.getElementById('noDataBanner');
                    if (banner) banner.remove();
                }
                
                // Reload the page if data state changed (to refresh geojson styling)
                if (data.hasData && Object.keys(newRiskData).length > 0) {
                    // Only reload if we had no data before
                    const hadNoData = document.getElementById('noDataBanner');
                    if (hadNoData) {
                        location.reload();
                    }
                }
            }
        })
        .catch(err => console.error('Failed to refresh map data:', err));
}

// Refresh every 30 seconds
setInterval(refreshMapData, 30000);

// Comment functionality
function loadComments() {
    fetch('/api/comments')
        .then(res => res.json())
        .then(data => {
            document.getElementById('commentDate').textContent = data.date;
            
            const container = document.getElementById('commentsList');
            
            if (data.comments.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-4xl mb-2">💬</div>
                        <p class="text-slate-400 text-sm">Belum ada komentar hari ini.</p>
                        <p class="text-slate-400 text-xs mt-1">Jadilah yang pertama!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = data.comments.map(c => `
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-medium text-sm text-slate-800">${escapeHtml(c.nama)}</span>
                        <span class="text-xs text-slate-400">${c.time}</span>
                    </div>
                    <p class="text-sm text-slate-600">${escapeHtml(c.content)}</p>
                </div>
            `).join('');
        })
        .catch(err => {
            document.getElementById('commentsList').innerHTML = `
                <div class="text-center text-red-500 text-sm py-4">
                    Gagal memuat komentar
                </div>
            `;
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load comments on page load
loadComments();

// Comment form handling
const commentForm = document.getElementById('commentForm');
if (commentForm) {
    const input = document.getElementById('commentInput');
    const charCount = document.getElementById('charCount');
    
    input.addEventListener('input', () => {
        charCount.textContent = `${input.value.length}/500`;
    });
    
    commentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const content = input.value.trim();
        if (!content) return;
        
        const btn = commentForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';
        
        try {
            const res = await fetch('/api/comments', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content=${encodeURIComponent(content)}`
            });
            
            const data = await res.json();
            
            if (data.success) {
                input.value = '';
                charCount.textContent = '0/500';
                loadComments();
            } else {
                alert(data.message);
            }
        } catch (err) {
            alert('Gagal mengirim komentar');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Kirim';
        }
    });
}

// Login popup handling
const loginModal = document.getElementById('loginModal');
const openBtn = document.getElementById('openLoginPopup');
const closeBtn = document.getElementById('closeLoginModal');

if (openBtn) {
    openBtn.addEventListener('click', () => {
        loginModal.classList.remove('hidden');
        loginModal.classList.add('flex');
    });
}

if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        loginModal.classList.add('hidden');
        loginModal.classList.remove('flex');
    });
}

if (loginModal) {
    loginModal.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.add('hidden');
            loginModal.classList.remove('flex');
        }
    });
}
</script>
<?= $this->endSection() ?>

