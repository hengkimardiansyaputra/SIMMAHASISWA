<?php
require_once '../includes/header.php';

$db = getDB();

$stmt = $db->query("
    SELECT ps.id_prodi, ps.nama_prodi, f.nama_fakultas,
           COUNT(m.npm) AS jumlah_mahasiswa
    FROM program_studi ps
    JOIN fakultas f ON ps.id_fakultas = f.id_fakultas
    LEFT JOIN mahasiswa m ON m.id_prodi = ps.id_prodi
    GROUP BY ps.id_prodi, ps.nama_prodi, f.nama_fakultas
    ORDER BY ps.nama_prodi ASC
");
$prodi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-journal-bookmark me-2 text-primary"></i>Daftar Program Studi
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Program Studi</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/prodi/tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Program Studi
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center">
            <i class="bi bi-table me-2 text-secondary"></i>
            <span class="fw-semibold">Data Program Studi</span>
            <span class="badge bg-secondary ms-2"><?= count($prodi_list) ?> data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th><i class="bi bi-journal-bookmark me-1 text-primary"></i>Nama Program Studi</th>
                            <th>Fakultas</th>
                            <th class="text-center">Jumlah Mahasiswa</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($prodi_list as $i => $prodi): ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($prodi['nama_prodi']) ?></td>
                                    <td><?= htmlspecialchars($prodi['nama_fakultas']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill">
                                            <?= htmlspecialchars($prodi['jumlah_mahasiswa']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>/prodi/edit.php?id=<?= htmlspecialchars($prodi['id_prodi']) ?>"
                                           class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="<?= BASE_URL ?>/prodi/hapus.php?id=<?= htmlspecialchars($prodi['id_prodi']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus program studi ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
