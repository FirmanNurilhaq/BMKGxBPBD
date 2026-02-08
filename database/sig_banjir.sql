CREATE DATABASE IF NOT EXISTS sig_banjir;
USE sig_banjir;

DROP TABLE IF EXISTS laporan_masyarakat;
DROP TABLE IF EXISTS rekomendasi;
DROP TABLE IF EXISTS rawan_banjir;
DROP TABLE IF EXISTS curah_hujan;
DROP TABLE IF EXISTS kecamatan;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin_bmkg', 'admin_bpbd') NOT NULL,
    email VARCHAR(100),
    no_telepon VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kecamatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kecamatan VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    luas_wilayah DECIMAL(10, 2),
    jumlah_penduduk INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE curah_hujan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kecamatan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    curah_hujan_mm DECIMAL(8, 2) NOT NULL,
    intensitas ENUM('ringan', 'sedang', 'lebat', 'sangat_lebat') NOT NULL,
    status_cuaca ENUM('cerah', 'berawan', 'hujan', 'hujan_petir') NOT NULL,
    keterangan TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rawan_banjir (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kecamatan_id INT NOT NULL,
    nama_lokasi VARCHAR(150) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    tingkat_risiko ENUM('aman', 'siaga', 'bahaya') NOT NULL,
    radius_meter INT DEFAULT 500,
    deskripsi TEXT,
    penyebab TEXT,
    dampak TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE laporan_masyarakat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelapor VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20),
    email VARCHAR(100),
    kecamatan_id INT NOT NULL,
    alamat_kejadian TEXT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    tanggal_kejadian DATE NOT NULL,
    waktu_kejadian TIME,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255),
    status ENUM('pending', 'diverifikasi', 'ditolak') DEFAULT 'pending',
    catatan_verifikasi TEXT,
    verified_by INT,
    verified_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rekomendasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tingkat_risiko ENUM('aman', 'siaga', 'bahaya') NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    langkah_mitigasi TEXT NOT NULL,
    kontak_darurat VARCHAR(100),
    prioritas INT DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, nama_lengkap, role, email) VALUES
('admin_bmkg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator BMKG', 'admin_bmkg', 'bmkg@serangkota.go.id'),
('admin_bpbd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator BPBD', 'admin_bpbd', 'bpbd@serangkota.go.id');

INSERT INTO kecamatan (nama_kecamatan, latitude, longitude, luas_wilayah, jumlah_penduduk) VALUES
('Serang', -6.1149, 106.1502, 25.88, 75432),
('Cipocok Jaya', -6.1380, 106.1540, 31.54, 85210),
('Curug', -6.1890, 106.1890, 49.60, 62340),
('Walantaka', -6.1720, 106.2150, 48.48, 58120),
('Taktakan', -6.1450, 106.2380, 47.88, 45670),
('Kasemen', -6.0520, 106.1340, 63.36, 95430);

INSERT INTO curah_hujan (kecamatan_id, tanggal, curah_hujan_mm, intensitas, status_cuaca, keterangan, created_by) VALUES
(1, CURDATE(), 45.5, 'sedang', 'hujan', 'Hujan sedang sejak pagi', 1),
(2, CURDATE(), 78.2, 'lebat', 'hujan_petir', 'Hujan lebat disertai petir', 1),
(3, CURDATE(), 15.0, 'ringan', 'berawan', 'Gerimis ringan', 1),
(4, CURDATE(), 5.0, 'ringan', 'cerah', 'Cuaca cerah berawan', 1),
(5, CURDATE(), 120.5, 'sangat_lebat', 'hujan_petir', 'Hujan sangat lebat, waspada banjir', 1),
(6, CURDATE(), 55.0, 'sedang', 'hujan', 'Hujan sedang', 1);

INSERT INTO rawan_banjir (kecamatan_id, nama_lokasi, latitude, longitude, tingkat_risiko, radius_meter, deskripsi, penyebab, dampak, created_by) VALUES
(1, 'Perumahan Bumi Serang Timur', -6.1180, 106.1580, 'siaga', 300, 'Area perumahan dengan drainase kurang baik', 'Drainase tersumbat dan intensitas hujan tinggi', 'Genangan air setinggi 30-50cm', 2),
(2, 'Kampung Sawah Luhur', -6.1420, 106.1490, 'bahaya', 500, 'Dataran rendah dekat sungai', 'Luapan sungai saat hujan lebat', 'Banjir hingga 1 meter, evakuasi diperlukan', 2),
(3, 'Jalan Raya Curug', -6.1850, 106.1920, 'siaga', 200, 'Jalan utama dengan genangan rutin', 'Saluran air tidak memadai', 'Kemacetan dan genangan 20-40cm', 2),
(5, 'Desa Taman Baru', -6.1480, 106.2420, 'bahaya', 600, 'Wilayah rawan banjir tahunan', 'Topografi rendah dan curah hujan ekstrem', 'Banjir 1-2 meter, kerusakan rumah warga', 2),
(6, 'Pesisir Kasemen', -6.0480, 106.1280, 'aman', 400, 'Area pesisir dengan tanggul', 'Potensi rob saat pasang tinggi', 'Minimal dengan penanganan baik', 2);

INSERT INTO rekomendasi (tingkat_risiko, judul, deskripsi, langkah_mitigasi, kontak_darurat, prioritas, created_by) VALUES
('aman', 'Tetap Waspada', 'Area dengan risiko banjir rendah namun tetap perlu kewaspadaan', 'Pantau informasi cuaca, pastikan saluran air bersih, siapkan nomor darurat', 'BPBD: 112', 1, 2),
('siaga', 'Siaga Banjir Level 1', 'Potensi genangan air saat hujan lebat', 'Pindahkan barang berharga ke tempat tinggi, siapkan tas darurat, pantau ketinggian air, hindari aktivitas luar ruangan', 'BPBD: 112, Damkar: 113', 2, 2),
('bahaya', 'Evakuasi Segera', 'Risiko banjir tinggi dengan potensi kerusakan', 'SEGERA EVAKUASI ke tempat aman, bawa dokumen penting, ikuti arahan petugas, jangan melewati genangan, hubungi pos evakuasi terdekat', 'BPBD: 112, SAR: 115, Damkar: 113', 3, 2);

INSERT INTO laporan_masyarakat (nama_pelapor, no_telepon, kecamatan_id, alamat_kejadian, latitude, longitude, tanggal_kejadian, waktu_kejadian, deskripsi, status) VALUES
('Ahmad Surya', '081234567890', 2, 'Jl. Raya Cipocok No. 45', -6.1395, 106.1510, CURDATE(), '08:30:00', 'Genangan air setinggi lutut di depan rumah warga', 'pending'),
('Siti Aminah', '085678901234', 5, 'Perumahan Taktakan Indah Blok C', -6.1465, 106.2395, CURDATE(), '10:15:00', 'Banjir masuk ke rumah warga, ketinggian 50cm', 'diverifikasi');
