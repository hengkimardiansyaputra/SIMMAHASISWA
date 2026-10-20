<?php
// ============================================================
//  Konfigurasi Database Localhost
// ============================================================

define('DB_HOST', 'localhost');             // 1. Ubah jadi 'localhost'
define('DB_USER', 'root');                  // 2. Default XAMPP/Laragon adalah 'root'
define('DB_PASS', '');                      // 3. Default XAMPP kosongkan (''), jika Laragon biasanya juga kosong
define('DB_NAME', 'sim_mahasiswa_php');         // 4. Sesuaikan dengan nama database di phpMyAdmin lokal Anda

define('APP_NAME', 'SIMAK UMB');
define('APP_SUBTITLE', 'Universitas Muhammadiyah Bengkulu');

// 5. Sesuaikan dengan nama folder di htdocs laptop Anda
// Contoh jika folder proyek Anda ada di C:/xampp/htdocs/sistem-informasi-mahasiswa/
define('BASE_URL', '/sim_mahasiswa_php');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:30px;background:#fee;border:1px solid red;border-radius:8px;margin:20px;">
                <h3>❌ Koneksi Database Gagal</h3>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Periksa kembali pengaturan di <code>config/db.php</code></p>
            </div>');
        }
    }
    return $pdo;
}
