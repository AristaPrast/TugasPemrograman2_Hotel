# Create README.md inside extracted project and also provide a standalone copy for download.
readme = r"""# Hotel Luviana — Sistem Informasi Pemesanan Hotel (PHP + MySQL)

Aplikasi web sederhana untuk simulasi pemesanan kamar hotel dengan dua peran pengguna: Admin dan Member

## Fitur Utama

### Pengunjung (Landing Page)
- Halaman beranda berisi informasi hotel, daftar kamar & harga, galeri, dan kontak.

### Member
- Registrasi akun member (status awal **pending**).
- Login member.
- Kelola profil (nama & email).
- Ajukan **verifikasi ulang** jika status **gagal verifikasi**.
- Pemesanan kamar dari dashboard member (hanya untuk status **berhasil verifikasi/verified**).
- Riwayat pemesanan berdasarkan **email** akun member.
- Estimasi biaya berdasarkan:
  - **Jumlah kamar** = `ceil(jumlah_dewasa / kapasitas_kamar)`
  - **Total biaya** = `jumlah_kamar × harga_per_malam × jumlah_malam`

### Admin
- Login admin.
- Dashboard admin:
  - Lihat daftar pemesanan.
  - Edit status pemesanan (**Menunggu Konfirmasi / Diterima / Ditolak**) dan tipe kamar.
  - Hapus pemesanan.
  - Lihat rincian perhitungan biaya pemesanan.
  - Cetak (print) data pemesanan.
- Verifikasi member:
  - Ubah status member: **pending / gagal verifikasi / berhasil verifikasi**.

## Teknologi
- PHP (native)
- MySQL / MariaDB
- HTML, CSS, JavaScript
- CDN: FontAwesome, jQuery, OwlCarousel (dipakai di halaman beranda)

## Prasyarat
- PHP 7.4+ (disarankan 8.x)
- MySQL/MariaDB
- Web server lokal: XAMPP / Laragon / WAMP

## Cara Menjalankan (Contoh: XAMPP)

1. **Clone / download** repository ini, lalu letakkan folder proyek ke:
   - `C:\xampp\htdocs\TugasPemrograman2_Hotel` (Windows)
   - atau direktori web server Anda.

2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.

3. Buat database bernama: `hotel_db`

4. Import SQL berikut (via phpMyAdmin → tab SQL):

```sql
CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Tabel admin
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- Tabel member
CREATE TABLE IF NOT EXISTS members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pemesanan
CREATE TABLE IF NOT EXISTS pemesanan (
  id_pemesanan INT AUTO_INCREMENT PRIMARY KEY,
  nama_tamu VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  checkin DATE NOT NULL,
  checkout DATE NOT NULL,
  jumlah_dewasa INT NOT NULL DEFAULT 1,
  jumlah_anak INT NOT NULL DEFAULT 0,
  tipe_kamar ENUM('Standar','Deluxe','Suite') NOT NULL,
  status_pemesanan VARCHAR(30) NOT NULL DEFAULT 'Menunggu Konfirmasi',
  tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (Opsional) contoh akun admin
-- CATATAN: password admin di project ini dicek sebagai teks biasa (tanpa hash).
INSERT INTO users (username, password)
VALUES ('admin', 'admin123')
ON DUPLICATE KEY UPDATE username=username;
