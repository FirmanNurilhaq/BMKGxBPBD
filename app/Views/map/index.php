<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="relative w-full" style="height: calc(100vh - 64px);">
    <div id="map" class="w-full h-full z-0"></div>
    
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
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const mapData = <?= json_encode($mapData) ?>;

const map = L.map('map').setView([-6.12, 106.15], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

const riskDataByDistrict = {};
mapData.forEach(item => {
    riskDataByDistrict[item.nama_kecamatan.toLowerCase().trim()] = item;
});

function getColor(riskLevel) {
    if (riskLevel === 'resiko') {
        return '#ef4444';
    } else if (riskLevel === 'siaga') {
        return '#fbbf24';
    } else {
        return '#22c55e';
    }
}

function getRiskLabel(riskLevel) {
    if (riskLevel === 'resiko') {
        return 'RESIKO TINGGI';
    } else if (riskLevel === 'siaga') {
        return 'SIAGA';
    } else {
        return 'AMAN';
    }
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
                    layer.setStyle({
                        weight: 4,
                        fillOpacity: 0.8
                    });
                });
                
                layer.on('mouseout', function() {
                    layer.setStyle({
                        weight: 2,
                        fillOpacity: 0.6
                    });
                });
            }
        }).addTo(map);
    });
</script>
<?= $this->endSection() ?>
