<?php
require_once '../includes/header.php';

$db = getDB();

$stmt = $db->query("
    SELECT f.id_fakultas, f.nama_fakultas, u.nama_universitas,
           COUNT(ps.id_prodi) AS jumlah_prodi
    FROM fakultas f
    JOIN universitas u ON f.id_universitas = u.id_universitas
    LEFT JOIN program_studi ps ON ps.id_fakultas = f.id_fakultas
    GROUP BY f.id_fakultas, f.nama_fakultas, u.nama_universitas
    ORDER BY f.nama_fakultas ASC
");
$fakultas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <i class="bi bi-diagram-3 me-2 text-primary"></i>Daftar Fakultas
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fakultas</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/fakultas/tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Fakultas
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center">
            <i class="bi bi-table me-2 text-secondary"></i>
            <span class="fw-semibold">Data Fakultas</span>
            <span class="badge bg-secondary ms-2"><?= count($fakultas_list) ?> data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tbl-fakultas" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th><i class="bi bi-diagram-3 me-1 text-primary"></i>Nama Fakultas</th>
                            <th>Universitas</th>
                            <th class="text-center">Jumlah Prodi</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($fakultas_list as $i => $fak): ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($fak['nama_fakultas']) ?></td>
                                    <td><?= htmlspecialchars($fak['nama_universitas']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            <?= htmlspecialchars($fak['jumlah_prodi']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>/fakultas/edit.php?id=<?= htmlspecialchars($fak['id_fakultas']) ?>" ...
                                           class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="<?= BASE_URL ?>/fakultas/hapus.php?id=<?= htmlspecialchars($fak['id_fakultas']) ?>" ...
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus fakultas ini?')">
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
