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

// Ambil data fakultas yang akan diedit
$stmt = $db->prepare("SELECT * FROM fakultas WHERE id_fakultas = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$fakultas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fakultas) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data fakultas tidak ditemukan.'];
    header('Location: ' . BASE_URL . '/fakultas/index.php');
    exit;
}

// Ambil daftar universitas untuk dropdown
$universitas_list = $db->query("SELECT id_universitas, nama_universitas FROM universitas ORDER BY nama_universitas ASC")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_fakultas  = trim($_POST['nama_fakultas'] ?? '');
    $id_universitas = trim($_POST['id_universitas'] ?? '');

    if ($nama_fakultas === '') {
        $errors[] = 'Nama fakultas wajib diisi.';
    }
    if ($id_universitas === '') {
        $errors[] = 'Universitas wajib dipilih.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE fakultas
            SET nama_fakultas = :nama_fakultas, id_universitas = :id_universitas
            WHERE id_fakultas = :id
        ");
        $stmt->execute([
            ':nama_fakultas'  => $nama_fakultas,
            ':id_universitas' => $id_universitas,
            ':id'             => $id,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Data fakultas berhasil diperbarui.',
        ];
        header('Location: ' . BASE_URL . '/fakultas/index.php');
        exit;
    }

    // Jika ada error, gunakan nilai POST untuk re-fill form
    $fakultas['nama_fakultas']  = $nama_fakultas;
    $fakultas['id_universitas'] = $id_universitas;
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-diagram-3 me-2 text-warning"></i>Edit Fakultas
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/fakultas/index.php">Fakultas</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                    <span class="fw-semibold"><i class="bi bi-pencil-square me-1 text-warning"></i>Form Edit Fakultas</span>
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

                    <form method="POST" action="<?= BASE_URL ?>/fakultas/edit.php?id=<?= htmlspecialchars($id) ?>" novalidate>
                        <div class="mb-3">
                            <label for="nama_fakultas" class="form-label fw-semibold">
                                Nama Fakultas <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="nama_fakultas"
                                name="nama_fakultas"
                                value="<?= htmlspecialchars($fakultas['nama_fakultas']) ?>"
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
                                        <?= ($fakultas['id_universitas'] == $univ['id_universitas']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($univ['nama_universitas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i> Update
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
