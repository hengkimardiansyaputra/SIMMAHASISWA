<?php
ob_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $npm             = trim($_POST['npm'] ?? '');
    $nama_mahasiswa  = trim($_POST['nama_mahasiswa'] ?? '');
    $jenis_kelamin   = trim($_POST['jenis_kelamin'] ?? '');
    $tempat_lahir    = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir   = trim($_POST['tanggal_lahir'] ?? '');
    $tanggal_masuk   = trim($_POST['tanggal_masuk'] ?? '');
    $alamat          = trim($_POST['alamat'] ?? '');
    $password        = $_POST['password'] ?? '';
    $id_prodi        = trim($_POST['id_prodi'] ?? '');

    // Validasi
    if ($npm === '')            $errors['npm']            = 'NPM wajib diisi.';
    if ($nama_mahasiswa === '') $errors['nama_mahasiswa'] = 'Nama mahasiswa wajib diisi.';
    if ($jenis_kelamin === '')  $errors['jenis_kelamin']  = 'Jenis kelamin wajib dipilih.';
    if ($tempat_lahir === '')   $errors['tempat_lahir']   = 'Tempat lahir wajib diisi.';
    if ($tanggal_lahir === '')  $errors['tanggal_lahir']  = 'Tanggal lahir wajib diisi.';
    if ($tanggal_masuk === '')  $errors['tanggal_masuk']  = 'Tanggal masuk wajib diisi.';
    if ($alamat === '')         $errors['alamat']         = 'Alamat wajib diisi.';
    if ($password === '')       $errors['password']       = 'Password wajib diisi.';
    if ($id_prodi === '')       $errors['id_prodi']       = 'Program studi wajib dipilih.';

    if (empty($errors)) {
        try {
            $db = getDB();

            // Cek duplikat NPM
            $cek = $db->prepare("SELECT COUNT(*) FROM mahasiswa WHERE npm = :npm");
            $cek->execute([':npm' => $npm]);
            if ($cek->fetchColumn() > 0) {
                $errors['npm'] = 'NPM sudah terdaftar.';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("
                    INSERT INTO mahasiswa
                        (npm, nama_mahasiswa, jenis_kelamin, tempat_lahir, tanggal_lahir,
                         tanggal_masuk, alamat, password, id_prodi)
                    VALUES
                        (:npm, :nama_mahasiswa, :jenis_kelamin, :tempat_lahir, :tanggal_lahir,
                         :tanggal_masuk, :alamat, :password, :id_prodi)
                ");
                $stmt->execute([
                    ':npm'            => $npm,
                    ':nama_mahasiswa' => $nama_mahasiswa,
                    ':jenis_kelamin'  => $jenis_kelamin,
                    ':tempat_lahir'   => $tempat_lahir,
                    ':tanggal_lahir'  => $tanggal_lahir,
                    ':tanggal_masuk'  => $tanggal_masuk,
                    ':alamat'         => $alamat,
                    ':password'       => $hashed,
                    ':id_prodi'       => $id_prodi,
                ]);

                $_SESSION['flash'] = [
                    'type'    => 'success',
                    'message' => 'Data mahasiswa berhasil ditambahkan.',
                ];
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors['db'] = 'Kesalahan database: ' . $e->getMessage();
        }
    }
}

// Ambil daftar program studi
try {
    $db = getDB();
    $prodiList = $db->query("SELECT id_prodi, nama_prodi FROM program_studi ORDER BY nama_prodi ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $prodiList = [];
}

$pageTitle = 'Tambah Mahasiswa';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Mahasiswa
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tambah Mahasiswa</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i>Kembali
        </a>
    </div>

    <?php if (!empty($errors['db'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($errors['db']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>

        <!-- SECTION 1: Data Akademik -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-mortarboard-fill me-2"></i>Data Akademik
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">

                    <!-- NPM -->
                    <div class="col-md-6">
                        <label for="npm" class="form-label fw-semibold">NPM <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" id="npm" name="npm"
                                   class="form-control <?= isset($errors['npm']) ? 'is-invalid' : '' ?>"
                                   placeholder="Nomor Pokok Mahasiswa"
                                   value="<?= htmlspecialchars($old['npm'] ?? '') ?>">
                            <?php if (isset($errors['npm'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['npm']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Program Studi -->
                    <div class="col-md-6">
                        <label for="id_prodi" class="form-label fw-semibold">Program Studi <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select id="id_prodi" name="id_prodi"
                                    class="form-select <?= isset($errors['id_prodi']) ? 'is-invalid' : '' ?>">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($prodiList as $prodi): ?>
                                    <option value="<?= htmlspecialchars($prodi['id_prodi']) ?>"
                                        <?= (($old['id_prodi'] ?? '') == $prodi['id_prodi']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prodi['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['id_prodi'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['id_prodi']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div class="col-md-6">
                        <label for="tanggal_masuk" class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" id="tanggal_masuk" name="tanggal_masuk"
                                   class="form-control <?= isset($errors['tanggal_masuk']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($old['tanggal_masuk'] ?? '') ?>">
                            <?php if (isset($errors['tanggal_masuk'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['tanggal_masuk']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SECTION 2: Data Pribadi -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-person-lines-fill me-2"></i>Data Pribadi
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">

                    <!-- Nama Mahasiswa -->
                    <div class="col-md-12">
                        <label for="nama_mahasiswa" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" id="nama_mahasiswa" name="nama_mahasiswa"
                                   class="form-control <?= isset($errors['nama_mahasiswa']) ? 'is-invalid' : '' ?>"
                                   placeholder="Nama lengkap mahasiswa"
                                   value="<?= htmlspecialchars($old['nama_mahasiswa'] ?? '') ?>">
                            <?php if (isset($errors['nama_mahasiswa'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_mahasiswa']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-md-6">
                        <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                    class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki"  <?= (($old['jenis_kelamin'] ?? '') === 'Laki-laki')  ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan"  <?= (($old['jenis_kelamin'] ?? '') === 'Perempuan')  ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <?php if (isset($errors['jenis_kelamin'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['jenis_kelamin']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="col-md-6">
                        <label for="tempat_lahir" class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                   class="form-control <?= isset($errors['tempat_lahir']) ? 'is-invalid' : '' ?>"
                                   placeholder="Kota tempat lahir"
                                   value="<?= htmlspecialchars($old['tempat_lahir'] ?? '') ?>">
                            <?php if (isset($errors['tempat_lahir'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['tempat_lahir']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="col-md-6">
                        <label for="tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                   class="form-control <?= isset($errors['tanggal_lahir']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($old['tanggal_lahir'] ?? '') ?>">
                            <?php if (isset($errors['tanggal_lahir'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['tanggal_lahir']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12">
                        <label for="alamat" class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                            <textarea id="alamat" name="alamat" rows="3"
                                      class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>"
                                      placeholder="Alamat lengkap mahasiswa"><?= htmlspecialchars($old['alamat'] ?? '') ?></textarea>
                            <?php if (isset($errors['alamat'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['alamat']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SECTION 3: Keamanan Akun -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-shield-lock-fill me-2"></i>Keamanan Akun
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">

                    <!-- Password -->
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password"
                                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                   placeholder="Masukkan password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text text-muted">Minimal 8 karakter disarankan.</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 justify-content-end">
            <button type="reset" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </button>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i>Simpan Data
            </button>
        </div>

    </form>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (pwInput.type === 'password') {
            pwInput.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwInput.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
