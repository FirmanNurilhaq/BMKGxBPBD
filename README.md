# PERANCANGAN SISTEM INFORMASI GEOGRAFIS BERBASIS WEBSITE UNTUK INFORMASI DAN REKOMENDASI MITIGASI BANJIR DI KOTA SERANG

Project ini merupakan sistem informasi geografis (SIG) yang memanfaatkan data curah hujan dari **BMKG Kota Serang** untuk memberikan informasi titik rawan serta rekomendasi mitigasi bencana banjir bagi masyarakat dan pihak **BPBD**.

---

## 🚀 Teknologi yang Digunakan

Sistem ini dibangun dengan stack teknologi modern untuk memastikan performa dan akurasi data:

* **Framework:** CodeIgniter 4 (PHP 8.x)
* **Database:** MySQL (Relational Database)
* **Mapping Engine:** Leaflet.js / OpenStreetMap (untuk visualisasi spasial)
* **Data Format:** GeoJSON (untuk boundary wilayah Kecamatan di Kota Serang)
* **Email Gateway:** Brevo API (untuk verifikasi akun dan notifikasi)
* **Styling:** Bootstrap 5 / AdminLTE

---

## 🛠️ Fitur Utama Website

1.  **Dashboard Monitoring:** Visualisasi data curah hujan terkini dari stasiun meteorologi.
2.  **Peta Sebaran Banjir:** Peta interaktif Kota Serang yang menampilkan zonasi rawan banjir berdasarkan data historis dan curah hujan.
3.  **Sistem Rekomendasi Mitigasi:** Memberikan arahan tindakan yang harus dilakukan masyarakat berdasarkan level kerawanan.
4.  **Verifikasi Akun Otomatis:** Sistem registrasi user yang terintegrasi dengan verifikasi email via Brevo.
5.  **Manajemen Data (Admin):** CRUD data curah hujan, data kecamatan, dan plotting titik koordinat baru.

---

## 📦 Cara Instalasi (Untuk Collaborator)

Bagi tim pengembang (Firman dan Atyaa), silakan ikuti langkah berikut:

1. **Clone Repository**
   ```bash
   git clone [https://github.com/FirmanNurilhaq/BMKGxBPBD.git](https://github.com/FirmanNurilhaq/BMKGxBPBD.git)
   composer install
2. **Konfigurasi Environment**
   ```bash
    Copy file env menjadi .env.
   Atur database.default.hostname, database.default.database, dll.
   Masukkan Brevo API Key ke dalam .env (Dapatkan dari Atyaa). 
3. **Migrasi Database**
   ```bash
   php spark migrate:refresh
   Import file .sql (tersedia di folder database/) ke phpMyAdmin anda.
4. **Run Server**
   ```bash
   php spark serve
5. **Akses Website**
   ```bash
   http://localhost:8081 atau http://localhost/8080


---
@COPYRIGHT 2026 FIRMAN NURIHALAQ & DYPA PRAMATYA SAHARA
