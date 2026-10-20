<?php
// login.php - Halaman Login
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';

// Jika sudah login, langsung redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';

// Ambil flash dari logout
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama']     = $admin['nama_lengkap'];

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Selamat datang, ' . htmlspecialchars($admin['nama_lengkap']) . '!'
            ];
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        } else {
            $error = 'Username atau password salah. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f1923 0%, #1e2b3c 50%, #2b4a72 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi background lingkaran */
        body::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: rgba(43,108,176,.15);
            border-radius: 50%;
            top: -100px; right: -100px;
        }
        body::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            background: rgba(43,108,176,.1);
            border-radius: 50%;
            bottom: -80px; left: -80px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            z-index: 10;
            padding: 20px;
        }

        .login-card {
            border-radius: 24px;
            border: none;
            box-shadow: 0 30px 70px rgba(0,0,0,.4);
            overflow: hidden;
            display: flex;
            min-height: 520px;
        }

        /* Panel kiri — branding */
        .brand-panel {
            background: linear-gradient(160deg, #1a3a5c, #2b6cb0);
            flex: 0 0 45%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            top: -60px; right: -60px;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            bottom: -40px; left: -40px;
        }
        .brand-icon {
            width: 70px; height: 70px;
            background: rgba(255,255,255,.15);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 28px;
            backdrop-filter: blur(10px);
        }
        .brand-panel h2 { color: #fff; font-weight: 700; font-size: 1.8rem; line-height: 1.3; }
        .brand-panel p { color: rgba(255,255,255,.65); font-size: .85rem; margin-top: 12px; }
        .feature-item {
            display: flex; align-items: center; gap: 10px;
            margin-top: 20px;
            color: rgba(255,255,255,.8);
            font-size: .82rem;
        }
        .feature-item i { color: #63b3ed; font-size: 1rem; }

        /* Panel kanan — form */
        .form-panel {
            flex: 1;
            background: #fff;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .input-group-text { background: #f8fafc; border-right: none; }
        .form-control {
            border-left: none;
            padding-left: 0;
            background: #f8fafc;
        }
        .form-control:focus { border-color: #2b6cb0; box-shadow: 0 0 0 3px rgba(43,108,176,.1); }
        .input-group:focus-within .input-group-text { border-color: #2b6cb0; }

        .btn-login {
            background: linear-gradient(135deg, #1e2b3c, #2b6cb0);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: .3px;
            transition: all .3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(43,108,176,.4);
        }

        .toggle-password { cursor: pointer; background: #f8fafc; border-left: none; border-color: #dee2e6; }
        .toggle-password:hover { background: #e9ecef; }

        @media (max-width: 640px) {
            .brand-panel { display: none; }
            .form-panel { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Panel Kiri: Branding -->
            <div class="brand-panel">
                <div class="brand-icon">
                    <i class="bi bi-mortarboard-fill text-white fs-2"></i>
                </div>
                <h2><?= APP_NAME ?></h2>
                <p><?= APP_SUBTITLE ?></p>

                <div class="feature-item">
                    <i class="bi bi-people-fill"></i>
                    <span>Manajemen Data Mahasiswa</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Manajemen Fakultas & Prodi</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-building-fill"></i>
                    <span>Data Universitas Terpadu</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-shield-check-fill"></i>
                    <span>Sistem Login Aman & Terenkripsi</span>
                </div>
            </div>

            <!-- Panel Kanan: Form Login -->
            <div class="form-panel">
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-1">Selamat Datang 👋</h4>
                    <p class="text-muted" style="font-size:.88rem;">Masuk ke panel administrator SIMAK.</p>
                </div>

                <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show border-0 rounded-3 mb-4" role="alert" style="font-size:.85rem;">
                    <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-3 mb-4" style="font-size:.85rem;">
                    <i class="bi bi-x-circle-fill me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted" style="font-size:.85rem;">Username</label>
                        <div class="input-group rounded-3 overflow-hidden" style="border-radius:12px!important;">
                            <span class="input-group-text border-end-0 px-3">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" name="username" class="form-control py-3"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                placeholder="Masukkan username" autofocus required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-muted mb-0" style="font-size:.85rem;">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 px-3">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput" class="form-control py-3 border-end-0"
                                placeholder="Masukkan password" required>
                            <button type="button" class="toggle-password input-group-text px-3" onclick="togglePassword()">
                                <i class="bi bi-eye text-muted" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-primary btn-login text-white">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Dashboard
                        </button>
                    </div>
                </form>

                <p class="text-center text-muted mt-4 mb-0" style="font-size:.78rem;">
                    Belum punya akun? Jalankan <code>setup_admin.php</code> terlebih dahulu.
                </p>
            </div>
        </div>

        <p class="text-center text-white opacity-50 mt-3" style="font-size:.75rem;">
            &copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; <?= APP_SUBTITLE ?>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>
