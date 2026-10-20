<?php
require_once '../includes/header.php';
$pdo = getDB();

$stmt = $pdo->query("SELECT * FROM universitas ORDER BY nama_universitas ASC");
$universitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-building me-2 text-primary"></i>Data Universitas
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Universitas</li>
                </ol>
            </nav>
        </div>
        <a href="<?= BASE_URL ?>/universitas/tambah.php" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Universitas
        </a>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Card Table -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 border-bottom">
            <h6 class="mb-0 fw-semibold text-secondary">
                <i class="bi bi-table me-2"></i>Daftar Universitas
                <span class="badge bg-primary ms-2"><?= count($universitas) ?></span>
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="tblUniversitas" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Universitas</th>
                            <th>Alamat</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($universitas as $i => $row): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill"><?= $i + 1 ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                <i class="bi bi-building fs-5"></i>
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars($row['nama_universitas']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= !empty($row['alamat']) ? htmlspecialchars($row['alamat']) : '<span class="fst-italic text-muted">-</span>' ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>/universitas/edit.php?id=<?= (int)$row['id_universitas'] ?>"
                                           class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/universitas/hapus.php?id=<?= (int)$row['id_universitas'] ?>"
                                           class="btn btn-sm btn-outline-danger" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus universitas \'<?= htmlspecialchars(addslashes($row['nama_universitas'])) ?>\'?')">
                                            <i class="bi bi-trash3"></i>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tblUniversitas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, 3] }
                ],
                order: [[1, 'asc']]
            });
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
