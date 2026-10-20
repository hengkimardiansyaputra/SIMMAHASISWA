<?php
ob_start();
require_once '../includes/header.php';

$db = getDB();

// Ambil daftar universitas untuk dropdown
$universitas_list = $db->query("SELECT id_universitas, nama_universitas FROM universitas ORDER BY nama_universitas ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_fakultas  = trim($_POST['nama_fakultas'] ?? '');
    $id_universitas = trim($_POST['id_universitas'] ?? '');

    $errors = [];

    if ($nama_fakultas === '') {
        $errors[] = 'Nama fakultas wajib diisi.';
    }
    if ($id_universitas === '') {
        $errors[] = 'Universitas wajib dipilih.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO fakultas (nama_fakultas, id_universitas) VALUES (:nama_fakultas, :id_universitas)");
        $stmt->execute([
            ':nama_fakultas'  => $nama_fakultas,
            ':id_universitas' => $id_universitas,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Fakultas berhasil ditambahkan.',
        ];
        header('Location: ' . BASE_URL . '/fakultas/index.php');
        exit;
    }
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-diagram-3 me-2 text-primary"></i>Tambah Fakultas
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/fakultas/index.php">Fakultas</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/fakultas/index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <span class="fw-semibold"><i class="bi bi-plus-circle me-1 text-primary"></i>Form Tambah Fakultas</span>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/fakultas/tambah.php" novalidate>
                        <div class="mb-3">
                            <label for="nama_fakultas" class="form-label fw-semibold">
                                Nama Fakultas <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="nama_fakultas"
                                name="nama_fakultas"
                                value="<?= htmlspecialchars($_POST['nama_fakultas'] ?? '') ?>"
                                placeholder="Contoh: Fakultas Teknik"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="id_universitas" class="form-label fw-semibold">
                                Universitas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="id_universitas" name="id_universitas" required>
                                <option value="">-- Pilih Universitas --</option>
                                <?php foreach ($universitas_list as $univ): ?>
                                    <option
                                        value="<?= htmlspecialchars($univ['id_universitas']) ?>"
                                        <?= (isset($_POST['id_universitas']) && $_POST['id_universitas'] == $univ['id_universitas']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($univ['nama_universitas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="<?= BASE_URL ?>/fakultas/index.php" class="btn btn-outline-secondary px-4">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
