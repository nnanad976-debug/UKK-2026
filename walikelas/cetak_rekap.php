
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

$role = strtolower(trim($_SESSION['role'] ?? ''));
$isWaliKelas = !empty($_SESSION['is_wali_kelas']);
$guruId = (int) ($_SESSION['guru_id'] ?? 0);

if ($role !== 'guru' || !$isWaliKelas || $guruId <= 0) {
    die("Anda tidak memiliki akses ke halaman ini.");
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$pesan = '';
$namaKelas = '';
$tahunAjaranId = 0;
$daftarSiswa = [];

$totalSiswa = 0;
$totalPelanggaran = 0;
$totalPoin = 0;

// Ambil penugasan Wali Kelas yang masih aktif.
$stmt = $koneksi->prepare("
    SELECT tahun_ajaran_id, kelas_id
    FROM t_wali_kelas
    WHERE guru_id = ?
      AND status_aktif = 1
      AND tanggal_mulai <= CURDATE()
      AND (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE())
    ORDER BY id DESC
    LIMIT 1
");

$stmt->bind_param("i", $guruId);
$stmt->execute();
$penugasan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$penugasan) {
    $pesan = "Penugasan Wali Kelas aktif tidak ditemukan. Periksa data pada tabel t_wali_kelas.";
} else {
    $kelasId = (int) $penugasan['kelas_id'];
    $tahunAjaranId = (int) $penugasan['tahun_ajaran_id'];

    // Ambil nama kelas.
    $stmt = $koneksi->prepare("
        SELECT nama_kelas
        FROM t_pelanggaran_siswa
        WHERE kelas_id = ?
          AND tahun_ajaran_id = ?
          AND nama_kelas IS NOT NULL
          AND nama_kelas <> ''
        LIMIT 1
    ");

    $stmt->bind_param("ii", $kelasId, $tahunAjaranId);
    $stmt->execute();
    $dataKelas = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $namaKelas = $dataKelas['nama_kelas'] ?? "Kelas ID " . $kelasId;

    // Ambil rekap seluruh siswa, termasuk siswa tanpa pelanggaran.
    $stmt = $koneksi->prepare("
        SELECT
            s.id AS siswa_id,
            s.nis,
            s.nama,
            COUNT(ps.id) AS jumlah_pelanggaran,
            COALESCE(SUM(ps.poin), 0) AS total_poin
        FROM t_kelas_siswa ks
        INNER JOIN t_siswa s
            ON s.id = ks.siswa_id
        LEFT JOIN t_pelanggaran_siswa ps
            ON ps.siswa_id = ks.siswa_id
            AND ps.kelas_id = ks.kelas_id
            AND ps.tahun_ajaran_id = ks.tahun_ajaran_id
        WHERE ks.kelas_id = ?
          AND ks.tahun_ajaran_id = ?
          AND ks.status_aktif = 1
        GROUP BY s.id, s.nis, s.nama
        ORDER BY s.nama ASC
    ");

    $stmt->bind_param("ii", $kelasId, $tahunAjaranId);
    $stmt->execute();
    $hasil = $stmt->get_result();

    while ($row = $hasil->fetch_assoc()) {
        $daftarSiswa[] = $row;
        $totalSiswa++;
        $totalPelanggaran += (int) $row['jumlah_pelanggaran'];
        $totalPoin += (int) $row['total_poin'];
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Pelanggaran - Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f8fc;
            color: #263f63;
        }

        .sidebar {
            width: 282px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            padding: 25px 14px 16px;
            border-right: 1px solid #dce9f8;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid #e5edf7;
        }

        .brand h5 {
            font-size: 17px;
            font-weight: 700;
            color: #203e68;
            margin: 0 0 5px;
        }

        .brand p {
            font-size: 12px;
            color: #7890ad;
            margin: 0;
        }

        .menu-title {
            margin: 0 14px 11px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #91a2b9;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            min-height: 42px;
            padding: 11px 14px;
            border-radius: 8px;
            color: #617895;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: .2s;
        }

        .menu-link:hover {
            background: #f0f6ff;
            color: #2878cf;
        }

        .menu-link.active {
            background: #eaf3ff;
            color: #2878cf;
            font-weight: 600;
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #e5edf7;
        }

        .logout .menu-link {
            color: #d65b63;
        }

        .main {
            width: calc(100% - 282px);
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-title {
            font-size: 23px;
            font-weight: 700;
            color: #203e68;
            margin-bottom: 5px;
        }

        .page-subtitle {
            font-size: 13px;
            color: #7890ad;
            margin-bottom: 25px;
        }

        .content-card {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            padding: 24px;
        }

        .report-heading {
            text-align: center;
            padding-bottom: 18px;
            border-bottom: 2px solid #263f63;
            margin-bottom: 20px;
        }

        .report-heading h2 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #203e68;
        }

        .report-heading p {
            margin: 0;
            font-size: 13px;
            color: #617895;
        }

        .report-info {
            margin-bottom: 18px;
            font-size: 13px;
            line-height: 1.9;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #cbd8e8;
            padding: 10px;
            font-size: 12px;
        }

        .report-table th {
            background: #eaf3ff;
            color: #203e68;
            text-align: center;
        }

        .report-table td.center {
            text-align: center;
        }

        .report-total {
            background: #f3f7fc;
            font-weight: 700;
        }

        .print-button {
            background: #2878cf;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .print-button:hover {
            background: #203e68;
        }

        .signature {
            display: flex;
            justify-content: flex-end;
            margin-top: 35px;
            text-align: center;
            font-size: 13px;
        }

        .signature div {
            width: 230px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            body {
                background: white;
                color: #000;
            }

            .sidebar,
            .page-title,
            .page-subtitle,
            .print-button,
            .no-print {
                display: none !important;
            }

            .main {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .content-card {
                border: none;
                padding: 0;
                border-radius: 0;
            }

            .report-heading h2,
            .report-heading p,
            .report-info {
                color: #000;
            }

            .report-table th {
                background: #eaf3ff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 230px;
            }

            .main {
                width: calc(100% - 230px);
                margin-left: 230px;
                padding: 22px 18px;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .logout {
                margin-top: 15px;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 20px 14px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">
        <h5>Sistem Pelanggaran</h5>
        <p>SMK Muhammadiyah</p>
    </div>

    <div class="menu-title">MENU WALI KELAS</div>

    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="data_siswa.php" class="menu-link">Data Siswa</a>
        <a href="rekap_pelanggaran.php" class="menu-link">Rekap Pelanggaran</a>
        <a href="cetak_rekap.php" class="menu-link active">Cetak Rekap</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">

    <h1 class="page-title">Cetak Rekap</h1>
    <p class="page-subtitle">
        Cetak laporan rekap pelanggaran siswa pada kelas yang Anda ampu.
    </p>

    <?php if ($pesan): ?>

        <div class="alert alert-warning">
            <?= e($pesan) ?>
        </div>

    <?php else: ?>

        <button type="button" class="print-button" onclick="window.print()">
            Cetak Rekap
        </button>

        <section class="content-card">

            <div class="report-heading">
                <h2>REKAPITULASI PELANGGARAN SISWA</h2>
                <p>SMK Muhammadiyah</p>
            </div>

            <div class="report-info">
                <div><strong>Kelas:</strong> <?= e($namaKelas) ?></div>
                <div><strong>Tahun Ajaran ID:</strong> <?= e($tahunAjaranId) ?></div>
                <div><strong>Jumlah Siswa:</strong> <?= number_format($totalSiswa) ?></div>
            </div>

            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th width="45">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jumlah Pelanggaran</th>
                            <th>Total Poin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($daftarSiswa) > 0): ?>

                        <?php foreach ($daftarSiswa as $index => $siswa): ?>
                            <tr>
                                <td class="center"><?= $index + 1 ?></td>
                                <td><?= e($siswa['nis']) ?></td>
                                <td><?= e($siswa['nama']) ?></td>
                                <td class="center">
                                    <?= number_format((int) $siswa['jumlah_pelanggaran']) ?>
                                </td>
                                <td class="center">
                                    <?= number_format((int) $siswa['total_poin']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="report-total">
                            <td colspan="3" class="center">TOTAL</td>
                            <td class="center"><?= number_format($totalPelanggaran) ?></td>
                            <td class="center"><?= number_format($totalPoin) ?></td>
                        </tr>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="center">
                                Belum ada data siswa untuk dicetak.
                            </td>
                        </tr>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="signature">
                <div>
                    <p>Tasikmalaya, <?= date('d-m-Y') ?></p>
                    <p>Wali Kelas,</p>
                    <br><br><br>
                    <p><strong>(....................................)</strong></p>
                </div>
            </div>

        </section>

    <?php endif; ?>

</main>

</body>
</html>
