<?php
ob_start();
require_once '../includes/header.php';

$db = getDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID program studi tidak valid.'];
    header('Location: ' . BASE_URL . '/prodi/index.php');
    exit;
}

// Ambil data prodi yang akan diedit
$stmt = $db->prepare("SELECT * FROM program_studi WHERE id_prodi = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$prodi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prodi) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data program studi tidak ditemukan.'];
    header('Location: ' . BASE_URL . '/prodi/index.php');
    exit;
}

// Ambil daftar fakultas untuk dropdown
$fakultas_list = $db->query("SELECT id_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_prodi  = trim($_POST['nama_prodi'] ?? '');
    $id_fakultas = trim($_POST['id_fakultas'] ?? '');

    if ($nama_prodi === '') {
        $errors[] = 'Nama program studi wajib diisi.';
    }
    if ($id_fakultas === '') {
        $errors[] = 'Fakultas wajib dipilih.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE program_studi
            SET nama_prodi = :nama_prodi, id_fakultas = :id_fakultas
            WHERE id_prodi = :id
        ");
        $stmt->execute([
            ':nama_prodi'  => $nama_prodi,
            ':id_fakultas' => $id_fakultas,
            ':id'          => $id,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Data program studi berhasil diperbarui.',
        ];
        header('Location: ' . BASE_URL . '/prodi/index.php');
        exit;
    }

    // Jika ada error, gunakan nilai POST untuk re-fill form
    $prodi['nama_prodi']  = $nama_prodi;
    $prodi['id_fakultas'] = $id_fakultas;
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-journal-bookmark me-2 text-warning"></i>Edit Program Studi
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/prodi/index.php">Program Studi</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/prodi/index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <span class="fw-semibold"><i class="bi bi-pencil-square me-1 text-warning"></i>Form Edit Program Studi</span>
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

                    <form method="POST" action="<?= BASE_URL ?>/prodi/edit.php?id=<?= htmlspecialchars($id) ?>" novalidate>
                        <div class="mb-3">
                            <label for="nama_prodi" class="form-label fw-semibold">
                                Nama Program Studi <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="nama_prodi"
                                name="nama_prodi"
                                value="<?= htmlspecialchars($prodi['nama_prodi']) ?>"
                                placeholder="Contoh: Teknik Informatika"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="id_fakultas" class="form-label fw-semibold">
                                Fakultas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="id_fakultas" name="id_fakultas" required>
                                <option value="">-- Pilih Fakultas --</option>
                                <?php foreach ($fakultas_list as $fak): ?>
                                    <option
                                        value="<?= htmlspecialchars($fak['id_fakultas']) ?>"
                                        <?= ($prodi['id_fakultas'] == $fak['id_fakultas']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($fak['nama_fakultas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i> Update
                            </button>
                            <a href="<?= BASE_URL ?>/prodi/index.php" class="btn btn-outline-secondary px-4">
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
