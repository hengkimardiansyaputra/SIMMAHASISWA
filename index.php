<?php
// index.php - Halaman Utama: Daftar Mahasiswa Per Program Studi
$pageTitle = 'Data Mahasiswa';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

$prodi_list = $db->query("
    SELECT p.id_prodi, p.nama_prodi, f.nama_fakultas
    FROM program_studi p
    JOIN fakultas f ON p.id_fakultas = f.id_fakultas
    ORDER BY f.nama_fakultas, p.nama_prodi
")->fetchAll();

$mahasiswa_by_prodi = [];
foreach ($prodi_list as $prodi) {
    $stmt = $db->prepare("SELECT * FROM mahasiswa WHERE id_prodi = ? ORDER BY nama_mahasiswa");
    $stmt->execute([$prodi['id_prodi']]);
    $mahasiswa_by_prodi[$prodi['id_prodi']] = $stmt->fetchAll();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Daftar Mahasiswa</h3>
        <p class="text-muted mb-0">Manajemen data mahasiswa berdasarkan program studi.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/mahasiswa/cetak.php" target="_blank" class="btn btn-success btn-custom shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak PDF
        </a>
        <a href="<?= BASE_URL ?>/mahasiswa/tambah.php" class="btn btn-primary btn-custom shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mahasiswa
        </a>
    </div>
</div>

<?php foreach ($prodi_list as $prodi): ?>
<?php $mhs_list = $mahasiswa_by_prodi[$prodi['id_prodi']]; ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                <i class="bi bi-journal-bookmark-fill fs-4"></i>
            </div>
            <div>
                <h5 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($prodi['nama_prodi']) ?></h5>
                <small class="text-muted fw-semibold">Fakultas: <?= htmlspecialchars($prodi['nama_fakultas']) ?></small>
            </div>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-25">
            <i class="bi bi-people-fill me-1"></i> <?= count($mhs_list) ?> Mahasiswa
        </span>
    </div>
    <div class="card-body pt-0">
        <hr class="mt-0 mb-4 text-muted opacity-25">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100" id="tbl-prodi-<?= $prodi['id_prodi'] ?>">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-muted" width="5%">No</th>
                        <th class="text-muted">NPM</th>
                        <th class="text-muted">Nama Lengkap</th>
                        <th class="text-muted">L/P</th>
                        <th class="text-muted">Tempat, Tgl Lahir</th>
                        <th class="text-muted">Tgl Masuk</th>
                        <th class="text-muted">Alamat</th>
                        <th class="text-center text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mhs_list as $i => $mhs): ?>
                    <tr>
                        <td class="text-center text-muted"><?= $i + 1 ?></td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><?= htmlspecialchars($mhs['npm']) ?></span></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                        <td>
                            <?php if ($mhs['jenis_kelamin'] === 'Laki-laki'): ?>
                                <span class="text-primary fw-medium"><i class="bi bi-gender-male"></i> L</span>
                            <?php else: ?>
                                <span class="text-danger fw-medium"><i class="bi bi-gender-female"></i> P</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><small><?= htmlspecialchars($mhs['tempat_lahir']) ?>, <?= $mhs['tanggal_lahir'] ?></small></td>
                        <td class="text-muted"><small><?= $mhs['tanggal_masuk'] ?></small></td>
                        <td><small class="text-muted d-inline-block text-truncate" style="max-width:150px" title="<?= htmlspecialchars($mhs['alamat']) ?>"><?= htmlspecialchars($mhs['alamat']) ?></small></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/mahasiswa/edit.php?npm=<?= urlencode($mhs['npm']) ?>" class="btn btn-sm btn-warning text-white me-1" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/mahasiswa/hapus.php?npm=<?= urlencode($mhs['npm']) ?>"
                               class="btn btn-sm btn-danger" title="Hapus"
                               onclick="return confirm('Yakin ingin menghapus data mahasiswa ini?')">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
