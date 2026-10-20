-- ============================================================
--  SIMAK - Sistem Informasi Manajemen Akademik Mahasiswa
--  Database Schema untuk MySQL (InfinityFree / cPanel)
--  Import file ini melalui phpMyAdmin
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- Tabel: universitas
CREATE TABLE IF NOT EXISTS `universitas` (
  `id_universitas` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_universitas` VARCHAR(255) NOT NULL,
  `alamat` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: fakultas
CREATE TABLE IF NOT EXISTS `fakultas` (
  `id_fakultas` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_fakultas` VARCHAR(255) NOT NULL,
  `id_universitas` INT DEFAULT NULL,
  FOREIGN KEY (`id_universitas`) REFERENCES `universitas`(`id_universitas`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: program_studi
CREATE TABLE IF NOT EXISTS `program_studi` (
  `id_prodi` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_prodi` VARCHAR(255) NOT NULL,
  `id_fakultas` INT DEFAULT NULL,
  FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas`(`id_fakultas`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: mahasiswa
CREATE TABLE IF NOT EXISTS `mahasiswa` (
  `npm` VARCHAR(20) PRIMARY KEY,
  `nama_mahasiswa` VARCHAR(255) NOT NULL,
  `jenis_kelamin` ENUM('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` VARCHAR(100) NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `tanggal_masuk` DATE NOT NULL,
  `alamat` TEXT,
  `password` VARCHAR(255) NOT NULL,
  `id_prodi` INT DEFAULT NULL,
  FOREIGN KEY (`id_prodi`) REFERENCES `program_studi`(`id_prodi`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: admin (untuk login sistem)
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Data Awal (Seed)
-- ============================================================

INSERT INTO `universitas` (`nama_universitas`, `alamat`) VALUES
('Universitas Muhammadiyah Bengkulu', 'Jl. Bali No.118, Kota Bengkulu, Bengkulu 38119');

INSERT INTO `fakultas` (`nama_fakultas`, `id_universitas`) VALUES
('Teknik', 1),
('Ekonomi', 1);

INSERT INTO `program_studi` (`nama_prodi`, `id_fakultas`) VALUES
('Teknik Informatika', 1),
('Teknik Sipil', 1),
('Manajemen', 2);

INSERT INTO `mahasiswa` (`npm`, `nama_mahasiswa`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `tanggal_masuk`, `alamat`, `password`, `id_prodi`) VALUES
('20210001', 'Ahmad Budi Santoso', 'Laki-laki', 'Bengkulu', '2000-05-10', '2021-08-15', 'Jl. Manggis No. 5, Bengkulu', 'rahasia123', 1),
('20210002', 'Siti Rahayu', 'Perempuan', 'Curup', '2001-03-22', '2021-08-15', 'Jl. Melati No. 12, Bengkulu', 'pass456', 1);
