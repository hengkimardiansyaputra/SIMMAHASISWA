<?php
require_once '../includes/header.php';
$pdo = getDB();
$errors = [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'ID universitas tidak valid.',
    ];
    header('Location: ' . BASE_URL . 'universitas/index.php');
    exit;
}

// Ambil data universitas
$stmt = $pdo->prepare("SELECT * FROM universitas WHERE id_universitas = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$universitas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$universitas) {
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'Data universitas tidak ditemukan.',
    ];
    header('Location: ' . BASE_URL . 'universitas/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_universitas = trim($_POST['nama_universitas'] ?? '');
    $alamat           = trim($_POST['alamat'] ?? '');

    // Validasi
    if ($nama_universitas === '') {
        $errors[] = 'Nama universitas tidak boleh kosong.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE universitas SET nama_universitas = :nama, alamat = :alamat WHERE id_universitas = :id");
            $stmt->execute([
                ':nama'   => $nama_universitas,
                ':alamat' => $alamat,
                ':id'     => $id,
            ]);
            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Data universitas berhasil diperbarui.',
            ];
            header('Location: ' . BASE_URL . 'universitas/index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Gagal memperbarui data: ' . $e->getMessage();
        }
    }

    // Jika ada error, gunakan input POST bukan DB
    $universitas['nama_universitas'] = $nama_universitas;
    $universitas['alamat']           = $alamat;
}
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Universitas
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>universitas/index.php" class="text-decoration-none">Universitas</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>universitas/index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-warning text-dark py-3 rounded-top-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pencil-square me-2"></i>Form Edit Universitas
                        <span class="badge bg-dark ms-2 small">ID: <?= (int)$id ?></span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= BASE_URL ?>universitas/edit.php?id=<?= (int)$id ?>" novalidate>
                        <!-- Nama Universitas -->
                        <div class="mb-4">
                            <label for="nama_universitas" class="form-label fw-semibold">
                                Nama Universitas <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-building text-warning"></i>
                                </span>
                                <input type="text"
                                       id="nama_universitas"
                                       name="nama_universitas"
                                       class="form-control border-start-0"
                                       placeholder="Contoh: Universitas Indonesia"
                                       value="<?= htmlspecialchars($universitas['nama_universitas']) ?>"
                                       required>
                            </div>
                            <div class="form-text text-muted">Masukkan nama lengkap universitas.</div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-4">
                            <label for="alamat" class="form-label fw-semibold">Alamat</label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2">
                                    <i class="bi bi-geo-alt text-warning"></i>
                                </span>
                                <textarea id="alamat"
                                          name="alamat"
                                          class="form-control border-start-0"
                                          rows="4"
                                          placeholder="Masukkan alamat lengkap universitas (opsional)"><?= htmlspecialchars($universitas['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <hr class="my-3">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= BASE_URL ?>universitas/index.php" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning px-4 shadow-sm text-dark fw-semibold">
                                <i class="bi bi-save me-1"></i> Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
