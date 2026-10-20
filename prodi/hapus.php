<?php
ob_start(); // Tambahkan penahan HTML agar tidak error header
require_once '../includes/header.php';

$db = getDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID program studi tidak valid.'];
    header('Location: ' . BASE_URL . '/prodi/index.php'); // Ditambah garis miring
    exit;
}

// Pastikan data ada sebelum dihapus
$stmt = $db->prepare("SELECT id_prodi, nama_prodi FROM program_studi WHERE id_prodi = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$prodi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prodi) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data program studi tidak ditemukan.'];
    header('Location: ' . BASE_URL . '/prodi/index.php'); // Ditambah garis miring
    exit;
}

// Hapus data program studi
$stmt = $db->prepare("DELETE FROM program_studi WHERE id_prodi = :id");
$stmt->execute([':id' => $id]);

$_SESSION['flash'] = [
    'type'    => 'success',
    'message' => 'Program studi "' . htmlspecialchars($prodi['nama_prodi']) . '" berhasil dihapus.',
];

header('Location: ' . BASE_URL . '/prodi/index.php'); // Ditambah garis miring
exit;