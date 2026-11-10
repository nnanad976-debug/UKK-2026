
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

/*
|--------------------------------------------------------------------------
| PEMERIKSAAN AKSES
|--------------------------------------------------------------------------
*/
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

/*
|--------------------------------------------------------------------------
| FUNGSI BANTU
|--------------------------------------------------------------------------
*/
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function tanggalValid($tanggal)
{
    if ($tanggal === '') {
        return true;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        return false;
    }

    [$tahun, $bulan, $hari] = array_map('intval', explode('-', $tanggal));

    return checkdate($bulan, $hari, $tahun);
}

/*
|--------------------------------------------------------------------------
| MENGAMBIL ID GURU
|--------------------------------------------------------------------------
*/
$user_id = (int)($_SESSION['user_id'] ?? 0);
$guru_id = (int)($_SESSION['guru_id'] ?? 0);

if ($guru_id <= 0 && $user_id > 0) {
    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    if ($stmt_guru) {
        mysqli_stmt_bind_param($stmt_guru, "i", $user_id);
        mysqli_stmt_execute($stmt_guru);

        $result_guru = mysqli_stmt_get_result($stmt_guru);
        $data_guru = mysqli_fetch_assoc($result_guru);

        $guru_id = (int)($data_guru['id'] ?? 0);

        mysqli_stmt_close($stmt_guru);
    }
}

if ($guru_id <= 0) {
    http_response_code(403);
    exit("Data guru tidak ditemukan. Silakan login kembali.");
}

/*
|--------------------------------------------------------------------------
| MENGAMBIL TAHUN AJARAN AKTIF
|--------------------------------------------------------------------------
*/
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama, tanggal_mulai, tanggal_selesai
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

if (!$query_tahun) {
    exit("Gagal mengambil tahun ajaran: " . e(mysqli_error($koneksi)));
}

$tahun_aktif = mysqli_fetch_assoc($query_tahun);

$tahun_ajaran_id = (int)($tahun_aktif['id'] ?? 0);
$nama_tahun_ajaran = $tahun_aktif['nama'] ?? '-';

/*
|--------------------------------------------------------------------------
| FILTER KELAS DAN TANGGAL
|--------------------------------------------------------------------------
*/
$filter_kelas = max(0, (int)($_GET['kelas_id'] ?? 0));

$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

$pesan_filter = '';

if (!tanggalValid($tanggal_mulai) || !tanggalValid($tanggal_selesai)) {
    $tanggal_mulai = '';
    $tanggal_selesai = '';
    $pesan_filter = 'Format tanggal tidak valid. Silakan pilih tanggal kembali.';
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    $tanggal_mulai = '';
    $tanggal_selesai = '';
    $pesan_filter = 'Tanggal mulai tidak boleh melebihi tanggal selesai.';
}

/*
|--------------------------------------------------------------------------
| MENGAMBIL DAFTAR KELAS WALI KELAS
|--------------------------------------------------------------------------
*/
$daftar_kelas = [];

if ($tahun_ajaran_id > 0) {
    $sql_kelas = "
        SELECT DISTINCT
            wk.kelas_id,
            COALESCE(k.nama, CONCAT('Kelas ID ', wk.kelas_id)) AS nama_kelas
        FROM t_wali_kelas wk
        LEFT JOIN t_kelas k
            ON k.id = wk.kelas_id
        WHERE wk.guru_id = ?
          AND wk.tahun_ajaran_id = ?
          AND wk.status_aktif = 1
          AND wk.tanggal_mulai <= CURDATE()
          AND (
                wk.tanggal_selesai IS NULL
                OR wk.tanggal_selesai >= CURDATE()
          )
        ORDER BY nama_kelas ASC
    ";

    $stmt_kelas = mysqli_prepare($koneksi, $sql_kelas);

    if (!$stmt_kelas) {
        exit("Gagal menyiapkan daftar kelas: " . e(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param(
        $stmt_kelas,
        "ii",
        $guru_id,
        $tahun_ajaran_id
    );

    mysqli_stmt_execute($stmt_kelas);

    $result_kelas = mysqli_stmt_get_result($stmt_kelas);

    while ($row_kelas = mysqli_fetch_assoc($result_kelas)) {
        $daftar_kelas[] = $row_kelas;
    }

    mysqli_stmt_close($stmt_kelas);
}

/*
|--------------------------------------------------------------------------
| MEMASTIKAN FILTER KELAS MILIK WALI KELAS
|--------------------------------------------------------------------------
*/
$kelas_valid = false;

foreach ($daftar_kelas as $kelas) {
    if ((int)$kelas['kelas_id'] === $filter_kelas) {
        $kelas_valid = true;
        break;
    }
}

if ($filter_kelas > 0 && !$kelas_valid) {
    $filter_kelas = 0;
}

/*
|--------------------------------------------------------------------------
| MENGAMBIL DATA REKAP PELANGGARAN
|--------------------------------------------------------------------------
*/
$data_rekap = [];

if ($tahun_ajaran_id > 0 && count($daftar_kelas) > 0) {

    $sql_rekap = "
        SELECT
            ps.siswa_id,
            ps.kelas_id,

            COALESCE(s.nis, '-') AS nis,
            COALESCE(s.nisn, '-') AS nisn,
            COALESCE(s.nama, ps.nama_siswa, '-') AS nama_siswa,

            COALESCE(
                k.nama,
                MAX(NULLIF(ps.nama_kelas, '')),
                CONCAT('Kelas ID ', ps.kelas_id)
            ) AS nama_kelas,

            COUNT(ps.id) AS jumlah_pelanggaran,

            COALESCE(SUM(ps.poin), 0) AS total_poin,

            GROUP_CONCAT(
                CONCAT(
                    COALESCE(
                        NULLIF(ps.nama_pelanggaran, ''),
                        'Pelanggaran'
                    ),
                    ' — Dicatat oleh: ',
                    COALESCE(
                        NULLIF(ps.nama_guru, ''),
                        NULLIF(g.nama, ''),
                        'Tidak diketahui'
                    )
                )
                ORDER BY ps.tanggal ASC, ps.id ASC
                SEPARATOR '\n'
            ) AS daftar_pelanggaran,

            MAX(ps.tanggal) AS tanggal_terakhir

        FROM t_pelanggaran_siswa ps

        LEFT JOIN t_siswa s
            ON s.id = ps.siswa_id

        LEFT JOIN t_kelas k
            ON k.id = ps.kelas_id

        LEFT JOIN t_guru g
            ON g.id = ps.guru_id

        WHERE ps.tahun_ajaran_id = ?

          AND EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                WHERE wk.guru_id = ?
                  AND wk.kelas_id = ps.kelas_id
                  AND wk.tahun_ajaran_id = ?
                  AND wk.status_aktif = 1
                  AND wk.tanggal_mulai <= CURDATE()
                  AND (
                        wk.tanggal_selesai IS NULL
                        OR wk.tanggal_selesai >= CURDATE()
                  )
          )

          AND EXISTS (
                SELECT 1
                FROM t_kelas_siswa ks
                WHERE ks.siswa_id = ps.siswa_id
                  AND ks.kelas_id = ps.kelas_id
                  AND ks.tahun_ajaran_id = ?
                  AND ks.status_aktif = 1
                  AND ks.tanggal_mulai <= CURDATE()
                  AND (
                        ks.tanggal_selesai IS NULL
                        OR ks.tanggal_selesai >= CURDATE()
                  )
          )

          AND (? = 0 OR ps.kelas_id = ?)

          AND (? = '' OR ps.tanggal >= ?)

          AND (? = '' OR ps.tanggal <= ?)

        GROUP BY
            ps.siswa_id,
            ps.kelas_id,
            s.nis,
            s.nisn,
            s.nama,
            ps.nama_siswa,
            k.nama

        ORDER BY
            total_poin DESC,
            nama_siswa ASC
    ";

    $stmt_rekap = mysqli_prepare($koneksi, $sql_rekap);

    if (!$stmt_rekap) {
        exit("Gagal menyiapkan data rekap: " . e(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param(
        $stmt_rekap,
        "iiiiisssss",
        $tahun_ajaran_id,
        $guru_id,
        $tahun_ajaran_id,
        $tahun_ajaran_id,
        $filter_kelas,
        $filter_kelas,
        $tanggal_mulai,
        $tanggal_mulai,
        $tanggal_selesai,
        $tanggal_selesai
    );

    mysqli_stmt_execute($stmt_rekap);

    $result_rekap = mysqli_stmt_get_result($stmt_rekap);

    while ($row = mysqli_fetch_assoc($result_rekap)) {
        $data_rekap[] = $row;
    }

    mysqli_stmt_close($stmt_rekap);
}

/*
|--------------------------------------------------------------------------
| MENGHITUNG RINGKASAN
|--------------------------------------------------------------------------
*/
$total_siswa = count($data_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($data_rekap as $data) {
    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];
    $total_poin += (int)$data['total_poin'];
}

/*
|--------------------------------------------------------------------------
| PARAMETER FILTER EKSPOR
|--------------------------------------------------------------------------
*/
$parameter_filter = [
    'kelas_id' => $filter_kelas,
    'tanggal_mulai' => $tanggal_mulai,
    'tanggal_selesai' => $tanggal_selesai
];

/*
|--------------------------------------------------------------------------
| EKSPOR CSV
|--------------------------------------------------------------------------
*/
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    $nama_file = 'rekap-pelanggaran-' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nama_file . '"');

    $output = fopen('php://output', 'w');

    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, [
        'No',
        'NIS',
        'NISN',
        'Nama Siswa',
        'Kelas',
        'Jumlah Pelanggaran',
        'Total Poin',
        'Keterangan Pelanggaran dan Nama Pencatat',
        'Tanggal Pelanggaran Terakhir'
    ], ';');

    $no = 1;

    foreach ($data_rekap as $data) {
        fputcsv($output, [
            $no++,
            $data['nis'],
            $data['nisn'],
            $data['nama_siswa'],
            $data['nama_kelas'],
            $data['jumlah_pelanggaran'],
            $data['total_poin'],
            $data['daftar_pelanggaran'] ?: '-',
            $data['tanggal_terakhir'] ?: '-'
        ], ';');
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pelanggaran - Sistem Pelanggaran</title>

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
            --red: #d9534f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
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
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 12px;
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
            transition: .2s;
            text-decoration: none;
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
            color: var(--red);
        }

        .logout .menu-link:hover {
            background: #fff0ef;
        }

        /* KONTEN UTAMA */
        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 22px;
        }

        .page-title {
            margin: 0 0 7px;
            color: var(--blue-dark);
            font-size: 24px;
            font-weight: 700;
        }

        .page-description {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .year-label {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 12px;
            background: var(--blue-soft);
            color: var(--blue);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        /* RINGKASAN */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .summary-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 19px 20px;
        }

        .summary-label {
            margin-bottom: 10px;
            color: var(--muted);
            font-size: 12px;
        }

        .summary-value {
            color: var(--blue-dark);
            font-size: 25px;
            font-weight: 700;
        }

        /* PANEL */
        .panel {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 21px;
            margin-bottom: 20px;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .panel-title {
            margin: 0;
            color: var(--blue-dark);
            font-size: 16px;
            font-weight: 700;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 12px;
        }

        .form-group {
            min-width: 180px;
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #526d8e;
            font-size: 12px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            height: 41px;
            padding: 0 12px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .09);
        }

        /* TOMBOL */
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 15px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            transition: .2s;
        }

        .button-primary {
            background: var(--blue);
            color: white;
        }

        .button-primary:hover {
            background: #1d68b7;
        }

        .button-light {
            background: #f0f5fb;
            color: #526d8e;
        }

        .button-light:hover {
            background: #e5eef8;
        }

        .button-green {
            background: #e9f7ef;
            color: #23844d;
        }

        .button-green:hover {
            background: #d9f0e3;
        }

        .button-dark {
            background: var(--blue-dark);
            color: white;
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* TABEL */
        .table-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .table-search {
            width: 260px;
            max-width: 100%;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        thead th {
            padding: 13px 12px;
            background: #f3f7fc;
            color: #607b9c;
            border-bottom: 1px solid var(--line);
            text-align: left;
            white-space: nowrap;
            font-size: 11px;
            font-weight: 700;
        }

        tbody td {
            padding: 13px 12px;
            color: #526d8e;
            border-bottom: 1px solid #edf2f8;
            vertical-align: top;
            font-size: 12px;
            line-height: 1.6;
        }

        tbody tr:hover {
            background: #fbfdff;
        }

        .student-name {
            color: var(--blue-dark);
            font-weight: 600;
        }

        .point-value {
            color: #d17a25;
            font-weight: 700;
        }

        .violation-list {
            min-width: 250px;
            max-width: 380px;
            white-space: normal;
            line-height: 1.8;
        }

        .empty-state {
            padding: 35px 15px;
            color: var(--muted);
            text-align: center;
        }

        .notice {
            padding: 13px 15px;
            margin-bottom: 18px;
            border: 1px solid #f2dfb5;
            border-radius: 9px;
            background: #fff9eb;
            color: #98702b;
            font-size: 13px;
        }

        .table-count {
            color: var(--muted);
            font-size: 12px;
        }

        @media (max-width: 1100px) {
            .main {
                padding: 22px 18px 35px;
            }

            .summary-grid {
                gap: 10px;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .sidebar,
            .filter-panel,
            .action-group,
            .table-tools,
            .no-print {
                display: none !important;
            }

            .main {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .panel,
            .summary-card {
                border: 1px solid #ddd;
                box-shadow: none;
            }

            .summary-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            table {
                min-width: 0;
            }

            thead th,
            tbody td {
                color: #222;
                font-size: 9px;
                padding: 7px 5px;
            }

            .violation-list {
                max-width: none;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">

        <a href="../dashboard.php" class="menu-link">
            Dashboard
        </a>

        <a href="catat_pelanggaran.php" class="menu-link">
            Catat Pelanggaran
        </a>

        <a href="data_siswa.php" class="menu-link">
            Data Siswa
        </a>

        <a href="rekap_pelanggaran.php" class="menu-link active">
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php" class="menu-link">
            Laporan
        </a>

        <a href="../about.php" class="menu-link">
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

    <header class="page-header">
        <div>
            <h1 class="page-title">Rekap Pelanggaran</h1>

            <p class="page-description">
                Ringkasan pelanggaran siswa pada kelas yang menjadi tanggung jawab Anda.
            </p>

            <div class="year-label">
                Tahun Ajaran: <?= e($nama_tahun_ajaran) ?>
            </div>
        </div>
    </header>

    <?php if ($tahun_ajaran_id <= 0): ?>

        <div class="notice">
            Tahun ajaran aktif belum tersedia. Periksa kembali data pada tabel
            <strong>t_tahun_ajaran</strong>.
        </div>

    <?php elseif (count($daftar_kelas) === 0): ?>

        <div class="notice">
            Belum ditemukan kelas yang ditugaskan kepada akun Wali Kelas ini
            pada tahun ajaran <?= e($nama_tahun_ajaran) ?>.
        </div>

    <?php endif; ?>

    <?php if ($pesan_filter !== ''): ?>

        <div class="notice">
            <?= e($pesan_filter) ?>
        </div>

    <?php endif; ?>

    <!-- RINGKASAN -->
    <section class="summary-grid">

        <div class="summary-card">
            <div class="summary-label">Jumlah Siswa dengan Pelanggaran</div>
            <div class="summary-value">
                <?= number_format($total_siswa, 0, ',', '.') ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Pelanggaran</div>
            <div class="summary-value">
                <?= number_format($total_pelanggaran, 0, ',', '.') ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Poin Pelanggaran</div>
            <div class="summary-value">
                <?= number_format($total_poin, 0, ',', '.') ?>
            </div>
        </div>

    </section>

    <!-- FILTER KELAS DAN TANGGAL -->
    <section class="panel filter-panel">

        <div class="panel-heading">
            <h2 class="panel-title">Filter Rekap</h2>
        </div>

        <form method="GET" class="filter-form">

            <div class="form-group">
                <label for="kelas_id" class="form-label">
                    Pilih Kelas
                </label>

                <select
                    name="kelas_id"
                    id="kelas_id"
                    class="form-control"
                >
                    <option value="0">Semua Kelas Wali Kelas</option>

                    <?php foreach ($daftar_kelas as $kelas): ?>

                        <option
                            value="<?= (int)$kelas['kelas_id'] ?>"
                            <?= $filter_kelas === (int)$kelas['kelas_id'] ? 'selected' : '' ?>
                        >
                            <?= e($kelas['nama_kelas']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_mulai" class="form-label">
                    Tanggal Mulai
                </label>

                <input
                    type="date"
                    name="tanggal_mulai"
                    id="tanggal_mulai"
                    class="form-control"
                    value="<?= e($tanggal_mulai) ?>"
                >
            </div>

            <div class="form-group">
                <label for="tanggal_selesai" class="form-label">
                    Tanggal Selesai
                </label>

                <input
                    type="date"
                    name="tanggal_selesai"
                    id="tanggal_selesai"
                    class="form-control"
                    value="<?= e($tanggal_selesai) ?>"
                >
            </div>

            <button type="submit" class="button button-primary">
                Tampilkan
            </button>

            <a href="rekap_pelanggaran.php" class="button button-light">
                Reset
            </a>

        </form>

    </section>

    <!-- TABEL REKAP -->
    <section class="panel">

        <div class="panel-heading">

            <h2 class="panel-title">Data Rekap Siswa</h2>

            <div class="action-group">

                <a
                    href="rekap_pelanggaran.php?<?= e(http_build_query(array_merge($parameter_filter, ['export' => 'csv']))) ?>"
                    class="button button-green"
                >
                    Ekspor CSV
                </a>

                <button
                    type="button"
                    onclick="window.print()"
                    class="button button-dark"
                >
                    Cetak
                </button>

            </div>

        </div>

        <div class="table-tools no-print">

            <div class="table-count">
                Menampilkan <?= count($data_rekap) ?> data siswa
            </div>

            <input
                type="text"
                id="searchTable"
                class="form-control table-search"
                placeholder="Cari nama, NIS, atau pelanggaran..."
            >

        </div>

        <div class="table-wrapper">

            <table id="tabelRekap">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jumlah Pelanggaran</th>
                        <th>Total Poin</th>
                        <th>Keterangan Pelanggaran</th>
                        <th>Tanggal Terakhir</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($data_rekap) > 0): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($data_rekap as $data): ?>

                            <tr>

                                <td><?= $no++ ?></td>

                                <td><?= e($data['nis']) ?></td>

                                <td><?= e($data['nisn']) ?></td>

                                <td>
                                    <span class="student-name">
                                        <?= e($data['nama_siswa']) ?>
                                    </span>
                                </td>

                                <td><?= e($data['nama_kelas']) ?></td>

                                <td>
                                    <?= number_format(
                                        (int)$data['jumlah_pelanggaran'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <span class="point-value">
                                        <?= number_format(
                                            (int)$data['total_poin'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </span>
                                </td>

                                <td class="violation-list">
                                    <?= $data['daftar_pelanggaran']
                                        ? nl2br(e($data['daftar_pelanggaran']))
                                        : '-' ?>
                                </td>

                                <td>
                                    <?= e($data['tanggal_terakhir'] ?: '-') ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="9" class="empty-state">
                                Belum ada data pelanggaran untuk kelas dan tanggal yang dipilih.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<script>
    /*
    |--------------------------------------------------------------------------
    | PENCARIAN TABEL
    |--------------------------------------------------------------------------
    */
    const searchInput = document.getElementById('searchTable');
    const table = document.getElementById('tabelRekap');

    if (searchInput && table) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();

                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }
</script>

</body>
</html>
