<?php
ob_start();
require_once '../includes/header.php';

$db = getDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID fakultas tidak valid.'];
    header('Location: ' . BASE_URL . '/fakultas/index.php');
    exit;
}

// Pastikan data ada sebelum dihapus
$stmt = $db->prepare("SELECT id_fakultas, nama_fakultas FROM fakultas WHERE id_fakultas = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$fakultas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fakultas) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data fakultas tidak ditemukan.'];
    header('Location: ' . BASE_URL . '/fakultas/index.php');
    exit;
}

// Hapus data fakultas
$stmt = $db->prepare("DELETE FROM fakultas WHERE id_fakultas = :id");
$stmt->execute([':id' => $id]);

$_SESSION['flash'] = [
    'type'    => 'success',
    'message' => 'Fakultas "' . htmlspecialchars($fakultas['nama_fakultas']) . '" berhasil dihapus.',
];

header('Location: ' . BASE_URL . '/fakultas/index.php');
exit;
