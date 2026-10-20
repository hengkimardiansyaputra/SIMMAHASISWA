<?php
ob_start();
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$npm = trim($_GET['npm'] ?? '');

if ($npm === '') {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'NPM tidak valid. Tidak ada data yang dihapus.',
    ];
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare("DELETE FROM mahasiswa WHERE npm = :npm");
    $stmt->execute([':npm' => $npm]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Data mahasiswa berhasil dihapus.',
        ];
    } else {
        $_SESSION['flash'] = [
            'type'    => 'warning',
            'message' => 'Data mahasiswa tidak ditemukan.',
        ];
    }
} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'Gagal menghapus data: ' . $e->getMessage(),
    ];
}

header('Location: ' . BASE_URL . '/index.php');
exit;
