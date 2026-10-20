<?php
ob_start();
require_once '../includes/header.php';
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'ID universitas tidak valid.',
    ];
    header('Location: ' . BASE_URL . '/universitas/index.php');
    exit;
}

// Pastikan data ada sebelum dihapus
$stmt = $pdo->prepare("SELECT nama_universitas FROM universitas WHERE id_universitas = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$universitas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$universitas) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'Data universitas tidak ditemukan.',
    ];
    header('Location: ' . BASE_URL . '/universitas/index.php');
    exit;
}

try {
    $stmtDel = $pdo->prepare("DELETE FROM universitas WHERE id_universitas = :id");
    $stmtDel->execute([':id' => $id]);

    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'Universitas <strong>' . htmlspecialchars($universitas['nama_universitas']) . '</strong> berhasil dihapus.',
    ];
} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'Gagal menghapus data: ' . $e->getMessage(),
    ];
}

header('Location: ' . BASE_URL . '/universitas/index.php');
exit;
