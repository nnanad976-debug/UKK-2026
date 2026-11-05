
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
$kelasId = 0;
$tahunAjaranId = 0;
$namaKelas = '';
$daftarSiswa = [];

$totalSiswa = 0;
$totalPelanggaran = 0;
$totalPoin = 0;

// Ambil kelas yang ditugaskan kepada Wali Kelas yang login.
$stmt = $koneksi->prepare("
    SELECT id, tahun_ajaran_id, kelas_id
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
    $pesan = "Penugasan Wali Kelas aktif tidak ditemukan. Periksa data guru_id pada tabel t_wali_kelas.";
} else {
    $kelasId = (int) $penugasan['kelas_id'];
    $tahunAjaranId = (int) $penugasan['tahun_ajaran_id'];

    // Ambil nama kelas dari data pelanggaran jika tersedia.
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

    // Ambil semua siswa yang terdaftar di kelas dan tahun ajaran tersebut.
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
    <title>Rekap Pelanggaran - Wali Kelas</title>

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

        .info-kelas {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            padding: 17px 20px;
            margin-bottom: 20px;
        }

        .info-kelas small {
            display: block;
            color: #7890ad;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-kelas strong {
            font-size: 16px;
            color: #203e68;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            padding: 20px;
        }

        .summary-card .label {
            font-size: 12px;
            color: #7890ad;
            margin-bottom: 10px;
        }

        .summary-card .number {
            font-size: 27px;
            font-weight: 700;
            color: #2878cf;
        }

        .content-card {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            padding: 21px;
        }

        .content-heading {
            font-size: 16px;
            font-weight: 700;
            color: #203e68;
            margin-bottom: 17px;
        }

        .table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table thead th {
            background: #f3f7fc;
            color: #617895;
            font-size: 12px;
            font-weight: 600;
            padding: 13px 12px;
            border-bottom: 1px solid #dce9f8;
            white-space: nowrap;
        }

        .table tbody td {
            font-size: 13px;
            color: #405875;
            padding: 13px 12px;
            border-color: #edf2f8;
        }

        .badge-poin {
            background: #eaf3ff;
            color: #2878cf;
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 600;
        }

        .empty-state {
            padding: 35px 15px;
            text-align: center;
            color: #7890ad;
            font-size: 13px;
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

            .summary-grid {
                grid-template-columns: 1fr;
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
        <a href="rekap_pelanggaran.php" class="menu-link active">Rekap Pelanggaran</a>
        <a href="cetak_rekap.php" class="menu-link">Cetak Rekap</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">

    <h1 class="page-title">Rekap Pelanggaran</h1>
    <p class="page-subtitle">
        Rekapitulasi pelanggaran siswa berdasarkan kelas yang menjadi tanggung jawab Anda.
    </p>

    <?php if ($pesan): ?>

        <div class="alert alert-warning">
            <?= e($pesan) ?>
        </div>

    <?php else: ?>

        <div class="info-kelas">
            <small>Kelas yang menjadi tanggung jawab Anda</small>
            <strong><?= e($namaKelas) ?></strong>
            <div class="text-muted small mt-1">
                Tahun Ajaran ID: <?= e($tahunAjaranId) ?>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Jumlah Siswa</div>
                <div class="number"><?= number_format($totalSiswa) ?></div>
            </div>

            <div class="summary-card">
                <div class="label">Jumlah Pelanggaran</div>
                <div class="number"><?= number_format($totalPelanggaran) ?></div>
            </div>

            <div class="summary-card">
                <div class="label">Total Poin</div>
                <div class="number"><?= number_format($totalPoin) ?></div>
            </div>
        </div>

        <section class="content-card">
            <h5 class="content-heading">Data Rekap Pelanggaran Siswa</h5>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="55">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Jumlah Pelanggaran</th>
                            <th class="text-center">Total Poin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($daftarSiswa) > 0): ?>

                        <?php foreach ($daftarSiswa as $index => $siswa): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= e($siswa['nis']) ?></td>
                                <td><?= e($siswa['nama']) ?></td>
                                <td class="text-center">
                                    <?= number_format((int) $siswa['jumlah_pelanggaran']) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-poin">
                                        <?= number_format((int) $siswa['total_poin']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="empty-state">
                                Belum ada data siswa pada kelas dan tahun ajaran ini.
                            </td>
                        </tr>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    <?php endif; ?>

</main>

</body>
</html>
