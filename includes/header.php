<?php
// includes/header.php - Template header & sidebar
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php'; // ← Proteksi: wajib login

// Ambil pesan flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Tentukan halaman aktif
$current = basename(dirname($_SERVER['PHP_SELF'])) . '/' . basename($_SERVER['PHP_SELF']);
function isActive(string $page): string {
    global $current;
    return str_contains($current, $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $pageTitle ?? 'Dashboard' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; min-height: 100vh; background: #1e2b3c; color: #fff; position: fixed; box-shadow: 2px 0 10px rgba(0,0,0,.1); z-index: 999; }
        #sidebar .sidebar-header { padding: 25px 20px; background: #15202b; text-align: center; border-bottom: 1px solid rgba(255,255,255,.05); }
        #sidebar ul.components { padding: 15px 0; }
        #sidebar ul li { margin-bottom: 5px; padding: 0 15px; }
        #sidebar ul li a { padding: 12px 20px; font-size: 1.05em; display: block; color: #a0aabf; text-decoration: none; border-radius: 8px; transition: .3s ease; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: #fff; background: #2b6cb0; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.1em; }
        #content { width: calc(100% - 260px); margin-left: 260px; min-height: 100vh; }
        .top-navbar { background: #fff; padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,.03); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .main-container { padding: 0 30px 40px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,.03); margin-bottom: 30px; overflow: hidden; }
        .card-header { background-color: #fff; border-bottom: 1px solid #f0f2f5; padding: 20px 25px; }
        .btn-custom { border-radius: 8px; padding: 10px 20px; font-weight: 500; }
        .menu-label { color: #4a5568; font-size: .65rem; letter-spacing: .1em; padding: 0 18px; display: block; font-weight: 700; text-transform: uppercase; margin-top: 12px; margin-bottom: 4px; }
        hr.sidebar-divider { border-color: rgba(255,255,255,.07); margin: 8px 15px; }
    </style>
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <div class="bg-primary rounded p-2 text-white shadow-sm">
                    <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
                <div class="text-start">
                    <h5 class="mb-0 fw-bold"><?= APP_NAME ?></h5>
                    <small style="color:#8a96a8;font-size:.7rem;">Sistem Akademik</small>
                </div>
            </div>
        </div>

        <ul class="list-unstyled components">
            <span class="menu-label">Mahasiswa</span>
            <li class="<?= isActive('index') && !isActive('universitas') && !isActive('fakultas') && !isActive('prodi') ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/index.php"><i class="bi bi-people-fill"></i> Data Mahasiswa</a>
            </li>
            <li class="<?= isActive('mahasiswa/tambah') ?>">
                <a href="<?= BASE_URL ?>/mahasiswa/tambah.php"><i class="bi bi-person-plus-fill"></i> Tambah Mahasiswa</a>
            </li>

            <hr class="sidebar-divider">
            <span class="menu-label">Master Data</span>
            <li class="<?= isActive('universitas') ?>">
                <a href="<?= BASE_URL ?>/universitas/index.php"><i class="bi bi-building"></i> Universitas</a>
            </li>
            <li class="<?= isActive('fakultas') ?>">
                <a href="<?= BASE_URL ?>/fakultas/index.php"><i class="bi bi-diagram-3"></i> Fakultas</a>
            </li>
            <li class="<?= isActive('prodi') ?>">
                <a href="<?= BASE_URL ?>/prodi/index.php"><i class="bi bi-journal-bookmark-fill"></i> Program Studi</a>
            </li>
        </ul>
    </nav>

    <!-- Content -->
    <div id="content">
        <div class="top-navbar">
            <div>
                <h6 class="mb-0 text-muted fw-bold"><?= APP_SUBTITLE ?></h6>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Dropdown Admin -->
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 p-0 border-0 bg-transparent" type="button" data-bs-toggle="dropdown">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></div>
                            <div class="text-muted" style="font-size:.72rem;">Administrator</div>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_nama'] ?? 'Admin') ?>&background=2b6cb0&color=fff&bold=true"
                             class="rounded-circle shadow-sm border border-2 border-primary border-opacity-25"
                             width="42" alt="Admin">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width:200px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark" style="font-size:.9rem;"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></div>
                            <div class="text-muted" style="font-size:.75rem;">@<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>/logout.php"
                               onclick="return confirm('Yakin ingin logout?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="main-container">

        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
