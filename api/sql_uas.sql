CREATE DATABASE IF NOT EXISTS rest_api;
USE rest_api;

-- Tabel Pengguna (Anggota Perpustakaan)
CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

-- Tabel Transaksi Keluar & Masuk Buku
CREATE TABLE IF NOT EXISTS transaksi_buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    judul_buku VARCHAR(150) NOT NULL,
    jenis_transaksi ENUM('keluar', 'masuk') NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
);