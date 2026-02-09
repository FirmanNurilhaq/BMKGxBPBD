DROP DATABASE IF EXISTS sig_banjir;
CREATE DATABASE sig_banjir;
USE sig_banjir;

CREATE TABLE users (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'masyarakat') DEFAULT 'masyarakat',
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(64) NULL,
    token_expires_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY username (username),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kecamatan (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nama_kecamatan VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE curah_hujan (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    kecamatan_id INT(11) UNSIGNED NOT NULL,
    nilai_curah_hujan FLOAT NOT NULL,
    tanggal DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE comments (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(11) UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, email, nama, password, role, email_verified) VALUES 
('admin_bmkg', 'admin@bmkg.go.id', 'Admin BMKG', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

INSERT INTO kecamatan (nama_kecamatan) VALUES 
('Kasemen'),
('Taktakan'),
('Serang'),
('Cipocok Jaya'),
('Walantaka'),
('Curug');

INSERT INTO curah_hujan (kecamatan_id, nilai_curah_hujan, tanggal) VALUES 
(1, 15.5, NOW()),
(2, 55.0, NOW()),
(3, 35.0, NOW()),
(4, 60.0, NOW()),
(5, 10.0, NOW()),
(6, 25.0, NOW());
