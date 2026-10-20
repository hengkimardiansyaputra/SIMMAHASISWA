<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();

// Ambil data mahasiswa beserta nama prodi (dan fakultas jika diperlukan)
$stmt = $db->query("
    SELECT m.*, ps.nama_prodi 
    FROM mahasiswa m
    LEFT JOIN program_studi ps ON m.id_prodi = ps.id_prodi
    ORDER BY m.npm ASC
");
$mahasiswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Mahasiswa</title>
    <style>
        /* Gaya khusus untuk tampilan cetak/PDF */
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .header h2, .header h3 {
            margin: 0;
            padding: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 12px;
        }
        th {
            background-color: #e0e0e0;
            text-align: center;
        }
        td {
            text-align: left;
        }
        .text-center { text-align: center; }
        
        /* Mengatur margin saat dicetak */
        @media print {
            @page { margin: 1cm; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>SISTEM INFORMASI AKADEMIK</h2>
        <h3>LAPORAN DATA MAHASISWA</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NPM</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 15%;">Jenis Kelamin</th>
                <th style="width: 20%;">Program Studi</th>
                <th style="width: 20%;">Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mahasiswa_list)): ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data mahasiswa.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mahasiswa_list as $index => $mhs): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td class="text-center"><?= htmlspecialchars($mhs['npm']) ?></td>
                        <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($mhs['jenis_kelamin']) ?></td>
                        <td><?= htmlspecialchars($mhs['nama_prodi'] ?? '-') ?></td>
                        <td class="text-center">
                            <?= date('d-m-Y', strtotime($mhs['tanggal_masuk'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Script otomatis membuka jendela print browser -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>