
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =====================================
   PEMERIKSAAN AKSES WALI KELAS
===================================== */

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (
    $role !== 'guru' ||
    empty($_SESSION['is_wali_kelas']) ||
    empty($_SESSION['guru_id'])
) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

$guru_id = (int) $_SESSION['guru_id'];
$nama = $_SESSION['nama'] ?? 'Wali Kelas';

function e($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

/* =====================================
   AMBIL PENUGASAN KELAS WALI KELAS
===================================== */

$sql_kelas = "
    SELECT DISTINCT
        wk.kelas_id,
        wk.tahun_ajaran_id,
        k.nama AS nama_kelas
    FROM t_wali_kelas wk
    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND k.status_aktif = 1
      AND wk.tanggal_mulai <= CURDATE()
      AND (
          wk.tanggal_selesai IS NULL
          OR wk.tanggal_selesai >= CURDATE()
      )
    ORDER BY k.nama ASC
";

$stmt = $koneksi->prepare($sql_kelas);

if (!$stmt) {
    exit("Query kelas gagal: " . e($koneksi->error));
}

$stmt->bind_param("i", $guru_id);
$stmt->execute();

$result_kelas = $stmt->get_result();
$daftar_kelas = [];

while ($row = $result_kelas->fetch_assoc()) {
    $daftar_kelas[] = $row;
}

$stmt->close();

/* =====================================
   PILIH KELAS
===================================== */

$kelas_id = 0;
$tahun_ajaran_id = 0;
$nama_kelas_aktif = '';

$pilihan_kelas = trim($_GET['penugasan'] ?? '');
$kelas_valid = false;

if ($pilihan_kelas !== '') {
    $bagian = explode('|', $pilihan_kelas);

    if (count($bagian) === 2) {
        $pilih_kelas_id = (int) $bagian[0];
        $pilih_tahun_id = (int) $bagian[1];

        foreach ($daftar_kelas as $kelas) {
            if (
                (int) $kelas['kelas_id'] === $pilih_kelas_id &&
                (int) $kelas['tahun_ajaran_id'] === $pilih_tahun_id
            ) {
                $kelas_id = $pilih_kelas_id;
                $tahun_ajaran_id = $pilih_tahun_id;
                $nama_kelas_aktif = $kelas['nama_kelas'];
                $kelas_valid = true;
                break;
            }
        }
    }
}

/* =====================================
   DEFAULT PENUGASAN PERTAMA
===================================== */

if (!$kelas_valid && !empty($daftar_kelas)) {
    $kelas_id = (int) $daftar_kelas[0]['kelas_id'];
    $tahun_ajaran_id = (int) $daftar_kelas[0]['tahun_ajaran_id'];
    $nama_kelas_aktif = $daftar_kelas[0]['nama_kelas'];
}

/* =====================================
   AMBIL DATA SISWA
   SATU SISWA SATU PENEMPATAN
===================================== */

$siswa = [];

if ($kelas_id > 0 && $tahun_ajaran_id > 0) {

    $sql_siswa = "
        SELECT
            s.id,
            s.nis,
            s.nisn,
            s.nama,
            s.jenis_kelamin,
            s.tanggal_lahir,
            s.alamat
        FROM t_kelas_siswa ks

        INNER JOIN (
            SELECT MAX(id) AS id
            FROM t_kelas_siswa
            WHERE kelas_id = ?
              AND tahun_ajaran_id = ?
              AND status_aktif = 1
              AND tanggal_mulai <= CURDATE()
              AND (
                  tanggal_selesai IS NULL
                  OR tanggal_selesai >= CURDATE()
              )
            GROUP BY siswa_id
        ) penempatan_aktif
            ON penempatan_aktif.id = ks.id

        INNER JOIN t_siswa s
            ON s.id = ks.siswa_id

        WHERE ks.kelas_id = ?
          AND ks.tahun_ajaran_id = ?
          AND ks.status_aktif = 1
          AND s.status_aktif = 1

        ORDER BY s.nama ASC
    ";

    $stmt_siswa = $koneksi->prepare($sql_siswa);

    if (!$stmt_siswa) {
        exit("Query siswa gagal: " . e($koneksi->error));
    }

    $stmt_siswa->bind_param(
        "iiii",
        $kelas_id,
        $tahun_ajaran_id,
        $kelas_id,
        $tahun_ajaran_id
    );

    $stmt_siswa->execute();

    $result_siswa = $stmt_siswa->get_result();

    while ($row = $result_siswa->fetch_assoc()) {
        $siswa[] = $row;
    }

    $stmt_siswa->close();
}

/* =====================================
   HALAMAN AKTIF SIDEBAR
===================================== */

$halamanAktifSidebar = basename($_SERVER['PHP_SELF'] ?? '');

function menuAktif(string $file, string $halamanAktifSidebar): string
{
    return $file === $halamanAktifSidebar ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Siswa - Sistem Pelanggaran</title>

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f6f9fd;
            --white: #ffffff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32, 62, 104, .07);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--page);
            color: var(--text);
        }

        a {
            text-decoration: none;
        }

        /* LAYOUT */

        .layout {
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            background: var(--white);
            border-right: 1px solid var(--line);
            z-index: 1000;
            overflow-y: auto;
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            color: var(--blue-dark);
            font-size: 18px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .menu-title {
            margin: 0 14px 11px;
            color: #91a5bd;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
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
            border-radius: 9px;
            color: #5e7695;
            font-size: 13px;
            font-weight: 500;
            transition: .2s ease;
        }

        .menu-link:hover {
            background: #f2f7fd;
            color: var(--blue);
        }

        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--blue);
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .logout .menu-link {
            color: #d9534f;
        }

        .logout .menu-link:hover {
            background: #fff0ef;
        }

        /* MAIN CONTENT */

        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        /* TOPBAR */

        .topbar {
            background: linear-gradient(
                135deg,
                #f4f9ff 0%,
                #edf6ff 100%
            );
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
            box-shadow: var(--shadow);
        }

        .welcome h2 {
            font-size: 25px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 6px;
        }

        .welcome p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .user-box {
            display: flex;
            align-items: center;
            color: var(--blue-dark);
            font-size: 13px;
            font-weight: 600;
            background: rgba(255, 255, 255, .65);
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 8px 15px;
        }

        .user-name {
            white-space: nowrap;
        }

        /* PAGE HEADER */

        .page-head {
            margin: 4px 6px 18px;
        }

        .page-head h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .page-head p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        /* PANEL */

        .panel {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 22px;
        }

        .panel-header h4 {
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .panel-header p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        /* PILIH KELAS */

        .form-select {
            min-width: 220px;
            max-width: 300px;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: #fff;
            padding: 10px 13px;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            outline: none;
            cursor: pointer;
            transition: .2s;
        }

        .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        /* TABEL */

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f5f8fc;
            color: #7188a5;
            font-size: 12px;
            font-weight: 700;
            padding: 14px;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid var(--line);
        }

        .table tbody td {
            padding: 14px;
            font-size: 13px;
            color: #526b89;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f8;
        }

        .table tbody tr:hover {
            background: #fafcff;
        }

        .student-name {
            color: var(--blue-dark);
            font-weight: 600;
        }

        .badge-gender {
            display: inline-block;
            background: var(--blue-soft);
            color: var(--blue);
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* TOMBOL CATAT */

        .btn-catat {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 13px;
            background: var(--blue);
            color: #fff;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            transition: .2s ease;
        }

        .btn-catat:hover {
            background: var(--blue-dark);
            color: #fff;
        }

        /* EMPTY STATE */

        .empty {
            text-align: center;
            padding: 35px;
            color: #8498b1;
            font-size: 13px;
        }

        /* TOTAL */

        .total-info {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
        }

        .total-info strong {
            color: var(--blue-dark);
            font-weight: 700;
        }

        /* FOOTER */

        .footer {
            text-align: center;
            margin-top: 28px;
            color: #9aabc0;
            font-size: 10px;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {
            .table {
                min-width: 1050px;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .logout {
                margin-top: 15px;
            }

            .main {
                width: 100%;
                margin-left: 0;
            }
        }

        @media (max-width: 650px) {
            .main {
                padding: 18px 14px 30px;
            }

            .topbar {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .page-head h3 {
                font-size: 24px;
            }

            .panel {
                padding: 18px;
            }

            .form-select {
                width: 100%;
                max-width: 100%;
            }

            .panel-header form {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR WALI KELAS -->

    <aside class="sidebar">

        <div class="brand">
            <div class="brand-title">
                Sistem Pelanggaran
            </div>

            <div class="brand-subtitle">
                SMK Muhammadiyah
            </div>
        </div>

        <div class="menu-title">
            Menu Utama
        </div>

        <nav class="menu">

            <a
                href="../dashboard.php"
                class="menu-link"
            >
                Dashboard
            </a>

            <a
                href="catat_pelanggaran.php"
                class="menu-link"
            >
                Catat Pelanggaran
            </a>

            <a
                href="riwayat.php"
                class="menu-link"
            >
                Riwayat
            </a>

            <a
                href="data_siswa.php"
                class="menu-link active"
                aria-current="page"
            >
                Data Siswa
            </a>

            <a
                href="rekap_poin.php"
                class="menu-link"
            >
                Rekap Poin Siswa
            </a>

            <a
                href="rekap_pelanggaran.php"
                class="menu-link"
            >
                Rekap Pelanggaran
            </a>

            <a
                href="cetak_rekap.php"
                class="menu-link"
            >
                Laporan
            </a>

            <a
                href="../about.php"
                class="menu-link"
            >
                About Me
            </a>

        </nav>

        <div class="logout">
            <a href="../logout.php" class="menu-link">
                Logout
            </a>
        </div>

    </aside>

    <!-- KONTEN UTAMA -->

    <main class="main">

        <section class="topbar">

            <div class="welcome">
                <h2>Data Siswa</h2>

                <p>
                    Lihat data siswa sesuai kelas yang menjadi tanggung jawab Anda.
                </p>
            </div>

            <div class="user-box">
                <span class="user-name">
                    <?= e($nama) ?>
                </span>
            </div>

        </section>

        <div class="page-head">

            <h3>Data Siswa</h3>

            <p>
                Informasi siswa pada kelas yang Anda ampu.
            </p>

        </div>

        <section class="panel">

            <div class="panel-header">

                <div>
                    <h4>Daftar Siswa</h4>

                    <p>
                        Data siswa berdasarkan kelas yang menjadi tanggung jawab Anda.
                    </p>
                </div>

                <?php if (!empty($daftar_kelas)): ?>

                    <form method="GET">

                        <select
                            name="penugasan"
                            class="form-select"
                            onchange="this.form.submit()"
                        >

                            <?php foreach ($daftar_kelas as $kelas): ?>

                                <?php
                                $value_kelas =
                                    (int) $kelas['kelas_id'] . '|' .
                                    (int) $kelas['tahun_ajaran_id'];

                                $value_aktif =
                                    $kelas_id . '|' .
                                    $tahun_ajaran_id;
                                ?>

                                <option
                                    value="<?= e($value_kelas) ?>"
                                    <?= $value_kelas === $value_aktif ? 'selected' : '' ?>
                                >
                                    <?= e($kelas['nama_kelas']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </form>

                <?php endif; ?>

            </div>

            <?php if (empty($daftar_kelas)): ?>

                <div class="empty">
                    Belum ada penugasan kelas Wali Kelas yang aktif.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($siswa)): ?>

                                <tr>
                                    <td colspan="8" class="empty">
                                        Belum ada siswa aktif di kelas ini.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($siswa as $index => $row): ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1 ?>
                                        </td>

                                        <td>
                                            <?= e($row['nis'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <?= e($row['nisn'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <span class="student-name">
                                                <?= e($row['nama']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge-gender">
                                                <?= ($row['jenis_kelamin'] ?? '') === 'L'
                                                    ? 'Laki-laki'
                                                    : (($row['jenis_kelamin'] ?? '') === 'P'
                                                        ? 'Perempuan'
                                                        : '-') ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= e($row['tanggal_lahir'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <?= e($row['alamat'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <a
                                                href="catat_pelanggaran.php?siswa_id=<?= (int) $row['id'] ?>&kelas_id=<?= $kelas_id ?>&tahun_ajaran_id=<?= $tahun_ajaran_id ?>"
                                                class="btn-catat"
                                            >
                                                Catat Pelanggaran
                                            </a>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="total-info">

                    Total siswa:
                    <strong><?= number_format(count($siswa)) ?></strong>

                    &nbsp; | &nbsp;

                    Kelas:
                    <strong><?= e($nama_kelas_aktif) ?></strong>

                </div>

            <?php endif; ?>

        </section>

        <div class="footer">
            Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
        </div>

    </main>

</div>

</body>
</html>
