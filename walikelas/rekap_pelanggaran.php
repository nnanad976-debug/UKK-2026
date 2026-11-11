
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   PEMERIKSAAN AKSES WALI KELAS
========================================= */

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

/* =========================================
   FUNGSI BANTU
========================================= */

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function tanggalValid($tanggal): bool
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

/* =========================================
   IDENTITAS GURU / WALI KELAS
========================================= */

$user_id = (int)($_SESSION['user_id'] ?? 0);
$guru_id = (int)($_SESSION['guru_id'] ?? 0);

if ($guru_id <= 0 && $user_id > 0) {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    if ($stmt_guru) {
        mysqli_stmt_bind_param($stmt_guru, "i", $user_id);
        mysqli_stmt_execute($stmt_guru);

        $data_guru = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt_guru)
        );

        $guru_id = (int)($data_guru['id'] ?? 0);

        mysqli_stmt_close($stmt_guru);
    }
}

if ($guru_id <= 0) {
    http_response_code(403);
    exit("Data guru tidak ditemukan. Silakan login kembali.");
}

$nama_login = $_SESSION['nama'] ?? 'Wali Kelas';

$stmt_nama = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_guru WHERE id = ? LIMIT 1"
);

if ($stmt_nama) {
    mysqli_stmt_bind_param($stmt_nama, "i", $guru_id);
    mysqli_stmt_execute($stmt_nama);

    $data_nama = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt_nama)
    );

    $nama_login = $data_nama['nama'] ?? $nama_login;

    mysqli_stmt_close($stmt_nama);
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
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

/* =========================================
   FILTER
========================================= */

$filter_kelas = max(0, (int)($_GET['kelas_id'] ?? 0));

$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

$pesan_filter = '';

if (
    !tanggalValid($tanggal_mulai) ||
    !tanggalValid($tanggal_selesai)
) {
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

/* =========================================
   DAFTAR KELAS WALI KELAS
========================================= */

$daftar_kelas = [];

if ($tahun_ajaran_id > 0) {

    $sql_kelas = "
        SELECT DISTINCT
            wk.kelas_id,
            COALESCE(
                k.nama,
                CONCAT('Kelas ID ', wk.kelas_id)
            ) AS nama_kelas

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

    while ($row = mysqli_fetch_assoc($result_kelas)) {
        $daftar_kelas[] = $row;
    }

    mysqli_stmt_close($stmt_kelas);
}

/* =========================================
   VALIDASI FILTER KELAS
========================================= */

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

/* =========================================
   DATA REKAP PELANGGARAN
========================================= */

$data_rekap = [];

if (
    $tahun_ajaran_id > 0 &&
    count($daftar_kelas) > 0
) {

    $sql_rekap = "
        SELECT
            ps.siswa_id,
            ps.kelas_id,

            COALESCE(s.nis, '-') AS nis,
            COALESCE(s.nisn, '-') AS nisn,

            COALESCE(
                s.nama,
                ps.nama_siswa,
                '-'
            ) AS nama_siswa,

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
        "iiiiiissss",
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

/* =========================================
   RINGKASAN
========================================= */

$total_siswa = count($data_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($data_rekap as $data) {
    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];
    $total_poin += (int)$data['total_poin'];
}

/* =========================================
   PARAMETER FILTER
========================================= */

$parameter_filter = [
    'kelas_id' => $filter_kelas,
    'tanggal_mulai' => $tanggal_mulai,
    'tanggal_selesai' => $tanggal_selesai
];

/* =========================================
   EKSPOR EXCEL
========================================= */

if (isset($_GET['export']) && $_GET['export'] === 'excel') {

    $nama_file = 'rekap-pelanggaran-' . date('Y-m-d') . '.xls';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nama_file . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    echo "\xEF\xBB\xBF";
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10pt;
            }

            h2, p {
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th {
                background: #dce9f8;
                color: #203e68;
                font-weight: bold;
                text-align: center;
                vertical-align: middle;
            }

            th, td {
                border: 1px solid #9eafc3;
                padding: 7px;
                vertical-align: top;
            }

            .center {
                text-align: center;
            }

            .text {
                mso-number-format: "\@";
            }

            .keterangan {
                width: 420px;
                white-space: normal;
                mso-wrap-text: yes;
            }

            .tanggal {
                width: 110px;
                text-align: center;
                mso-number-format: "dd-mm-yyyy";
            }
        </style>
    </head>
    <body>

        <h2>REKAP PELANGGARAN SISWA</h2>
        <p>SMK Muhammadiyah Tasikmalaya</p>
        <p>
            Tahun Ajaran: <?= e($nama_tahun_ajaran) ?><br>
            Dicetak: <?= date('d-m-Y') ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jumlah Pelanggaran</th>
                    <th>Total Poin</th>
                    <th>Keterangan Pelanggaran dan Nama Pencatat</th>
                    <th>Tanggal Pelanggaran Terakhir</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($data_rekap) > 0): ?>

                    <?php foreach ($data_rekap as $i => $data): ?>
                        <tr>
                            <td class="center"><?= $i + 1 ?></td>
                            <td class="text"><?= e($data['nis']) ?></td>
                            <td class="text"><?= e($data['nisn']) ?></td>
                            <td><?= e($data['nama_siswa']) ?></td>
                            <td><?= e($data['nama_kelas']) ?></td>

                            <td class="center">
                                <?= (int)$data['jumlah_pelanggaran'] ?>
                            </td>

                            <td class="center">
                                <?= (int)$data['total_poin'] ?>
                            </td>

                            <td class="keterangan">
                                <?= nl2br(e($data['daftar_pelanggaran'] ?: '-')) ?>
                            </td>

                            <td class="tanggal">
                                <?php
                                $tanggal = $data['tanggal_terakhir'] ?? '';

                                echo $tanggal && $tanggal !== '0000-00-00'
                                    ? e(date('d-m-Y', strtotime($tanggal)))
                                    : '-';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9" class="center">
                            Belum ada data pelanggaran.
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>

    </body>
    </html>
    <?php
    exit;
}

/* =========================================
   HALAMAN AKTIF
========================================= */

$halamanAktif = basename($_SERVER['PHP_SELF'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Pelanggaran - Sistem Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >

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

        /* MAIN */

        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        /* PAGE HEADER */

        .page-head {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 24px 25px;
            margin-bottom: 22px;
            box-shadow: 0 5px 15px rgba(32, 62, 104, .04);
        }

        .page-head h3 {
            font-size: 28px;
            font-weight: 600;
            color: var(--blue-dark);
            margin-bottom: 3px;
        }

        .page-head p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
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

        /* SUMMARY */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 22px;
        }

        .summary-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            min-height: 145px;
            padding: 22px;
            box-shadow: var(--shadow);
            transition: .2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(32, 62, 104, .10);
        }

        .summary-label {
            margin-bottom: 12px;
            color: var(--muted);
            font-size: 12px;
        }

        .summary-value {
            color: var(--blue-dark);
            font-size: 27px;
            font-weight: 700;
        }

        /* PANEL */

        .panel {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .panel-title {
            margin: 0;
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
        }

        /* FILTER */

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 14px;
        }

        .form-group {
            min-width: 180px;
            flex: 1;
        }

        .form-label {
            display: block;
            color: #405873;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--line);
            border-radius: 9px;
            padding: 10px 13px;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            box-shadow: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        /* BUTTON */

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
            transition: .2s ease;
            white-space: nowrap;
        }

        .button-primary {
            background: var(--blue);
            color: #fff;
        }

        .button-primary:hover {
            background: var(--blue-dark);
            color: #fff;
        }

        .button-light {
            background: #edf3fa;
            border: 1px solid var(--line);
            color: #526b89;
        }

        .button-light:hover {
            background: #e3edf8;
            color: var(--blue-dark);
        }

        .button-green {
            background: #e9f7ef;
            color: #23844d;
        }

        .button-green:hover {
            background: #d9f0e3;
            color: #176b3b;
        }

        .button-dark {
            background: var(--blue-dark);
            color: #fff;
        }

        .button-dark:hover {
            background: #172f50;
            color: #fff;
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* TABLE */

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
            vertical-align: middle;
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
            padding: 13px 14px;
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

        .point-value {
            color: #d17a25;
            font-weight: 700;
        }

        .violation-list {
            min-width: 250px;
            max-width: 380px;
            white-space: normal;
            overflow-wrap: anywhere;
            line-height: 1.8;
        }

        .empty-state {
            text-align: center;
            padding: 28px;
            color: #8498b1;
            font-size: 13px;
        }

        /* DATATABLES */

        .dt-container {
            font-size: 13px;
            color: var(--muted);
        }

        .dt-container .dt-layout-row {
            margin: 13px 0;
            gap: 12px;
        }

        .dt-container .dt-layout-row:first-child {
            margin-top: 20px;
        }

        .dt-container .dt-layout-cell {
            display: flex;
            align-items: center;
        }

        .dt-container .dt-length,
        .dt-container .dt-search {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dt-container .dt-length label,
        .dt-container .dt-search label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 12px;
            white-space: nowrap;
        }

        .dt-container .dt-length select {
            width: 65px !important;
            min-width: 65px;
            height: 36px;
            padding: 5px 8px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--text);
        }

        .dt-container .dt-search input {
            width: 190px;
            height: 36px;
            padding: 7px 11px;
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-left: 0 !important;
            color: var(--text);
        }

        .dt-container .dt-search input:focus,
        .dt-container .dt-length select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        .dt-container .dt-info {
            font-size: 12px;
            color: var(--muted);
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 7px !important;
            font-size: 12px;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            background: var(--blue-soft) !important;
            border-color: var(--line) !important;
            color: var(--blue) !important;
        }

        /* NOTICE */

        .notice {
            padding: 13px 15px;
            margin-bottom: 18px;
            border: 1px solid #f2dfb5;
            border-radius: 9px;
            background: #fff9eb;
            color: #98702b;
            font-size: 13px;
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
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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

            .page-head {
                padding: 20px;
            }

            .page-head h3 {
                font-size: 24px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .panel {
                padding: 18px;
            }

            .panel-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .filter-form .form-group {
                min-width: 100%;
            }

            .filter-form button,
            .filter-form a {
                flex: 1;
            }

            .dt-container .dt-search input {
                width: 140px;
            }
        }

        /* =====================================
           PRINT - A4 LANDSCAPE
        ===================================== */

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {

            html,
            body {
                width: 100%;
                min-height: 0;
                background: #fff !important;
                color: #222 !important;
                font-family: Arial, sans-serif !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sidebar,
            .filter-panel,
            .action-group,
            .dt-container .dt-layout-row,
            .footer,
            .no-print {
                display: none !important;
            }

            .main {
                width: 100% !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .page-head {
                display: block !important;
                margin: 0 0 12px !important;
                padding: 0 0 10px !important;
                border: 0 !important;
                border-bottom: 1px solid #777 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                text-align: center;
            }

            .page-head h3 {
                color: #203e68 !important;
                font-size: 18px !important;
                font-weight: 700 !important;
                margin-bottom: 4px !important;
            }

            .page-head p {
                color: #444 !important;
                font-size: 10px !important;
            }

            .year-label {
                display: inline-block !important;
                margin-top: 5px !important;
                padding: 4px 8px !important;
                color: #203e68 !important;
                background: #eaf3ff !important;
                font-size: 9px !important;
            }

            .summary-grid {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 8px !important;
                margin-bottom: 12px !important;
            }

            .summary-card {
                min-height: 0 !important;
                padding: 9px 12px !important;
                border: 1px solid #bbb !important;
                border-radius: 5px !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .summary-label {
                margin-bottom: 4px !important;
                color: #555 !important;
                font-size: 9px !important;
            }

            .summary-value {
                color: #203e68 !important;
                font-size: 16px !important;
            }

            .panel {
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .panel-heading {
                margin-bottom: 8px !important;
            }

            .panel-title {
                color: #203e68 !important;
                font-size: 12px !important;
            }

            .table-responsive {
                width: 100% !important;
                overflow: visible !important;
            }

            #tabelRekap {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                font-size: 8px !important;
            }

            #tabelRekap thead {
                display: table-header-group;
            }

            #tabelRekap tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            #tabelRekap th,
            #tabelRekap td {
                padding: 5px 4px !important;
                border: 1px solid #aaa !important;
                color: #222 !important;
                font-size: 8px !important;
                line-height: 1.4 !important;
                vertical-align: top !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            #tabelRekap thead th {
                background: #eaf0f7 !important;
                color: #203e68 !important;
                font-weight: 700 !important;
                text-align: center !important;
            }

            #tabelRekap th:nth-child(1),
            #tabelRekap td:nth-child(1) {
                width: 3% !important;
                text-align: center !important;
            }

            #tabelRekap th:nth-child(2),
            #tabelRekap td:nth-child(2) {
                width: 8% !important;
            }

            #tabelRekap th:nth-child(3),
            #tabelRekap td:nth-child(3) {
                width: 9% !important;
            }

            #tabelRekap th:nth-child(4),
            #tabelRekap td:nth-child(4) {
                width: 16% !important;
            }

            #tabelRekap th:nth-child(5),
            #tabelRekap td:nth-child(5) {
                width: 9% !important;
            }

            #tabelRekap th:nth-child(6),
            #tabelRekap td:nth-child(6) {
                width: 8% !important;
                text-align: center !important;
            }

            #tabelRekap th:nth-child(7),
            #tabelRekap td:nth-child(7) {
                width: 7% !important;
                text-align: center !important;
            }

            #tabelRekap th:nth-child(8),
            #tabelRekap td:nth-child(8) {
                width: 30% !important;
            }

            #tabelRekap th:nth-child(9),
            #tabelRekap td:nth-child(9) {
                width: 10% !important;
                text-align: center !important;
            }

            .violation-list {
                min-width: 0 !important;
                max-width: none !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
                line-height: 1.4 !important;
            }

            .student-name,
            .point-value {
                color: #222 !important;
            }

            .alert {
                display: none !important;
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

        <a href="riwayat.php" class="menu-link">
            Riwayat
        </a>

        <a href="data_siswa.php" class="menu-link">
            Data Siswa
        </a>

        <a href="rekap_poin.php" class="menu-link">
            Rekap Poin Siswa
        </a>

        <a
            href="rekap_pelanggaran.php"
            class="menu-link <?= $halamanAktif === 'rekap_pelanggaran.php' ? 'active' : '' ?>"
            aria-current="page"
        >
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

<!-- MAIN CONTENT -->

<main class="main">

    <!-- HEADER -->

    <div class="page-head">

        <h3>Rekap Pelanggaran</h3>

        <p>
            Ringkasan pelanggaran siswa pada kelas yang menjadi tanggung jawab Anda.
        </p>

        <div class="year-label">
            Tahun Ajaran: <?= e($nama_tahun_ajaran) ?>
        </div>

    </div>

    <!-- PESAN -->

    <?php if ($tahun_ajaran_id <= 0): ?>

        <div class="alert alert-warning">
            Tahun ajaran aktif belum tersedia. Periksa kembali data tahun ajaran.
        </div>

    <?php elseif (count($daftar_kelas) === 0): ?>

        <div class="alert alert-warning">
            Belum ditemukan kelas yang ditugaskan kepada akun Wali Kelas ini
            pada tahun ajaran <?= e($nama_tahun_ajaran) ?>.
        </div>

    <?php endif; ?>

    <?php if ($pesan_filter !== ''): ?>

        <div class="alert alert-warning">
            <?= e($pesan_filter) ?>
        </div>

    <?php endif; ?>

    <!-- RINGKASAN -->

    <section class="summary-grid">

        <div class="summary-card">

            <div class="summary-label">
                Jumlah Siswa dengan Pelanggaran
            </div>

            <div class="summary-value">
                <?= number_format($total_siswa, 0, ',', '.') ?>
            </div>

        </div>

        <div class="summary-card">

            <div class="summary-label">
                Total Pelanggaran
            </div>

            <div class="summary-value">
                <?= number_format($total_pelanggaran, 0, ',', '.') ?>
            </div>

        </div>

        <div class="summary-card">

            <div class="summary-label">
                Total Poin Pelanggaran
            </div>

            <div class="summary-value">
                <?= number_format($total_poin, 0, ',', '.') ?>
            </div>

        </div>

    </section>

    <!-- FILTER REKAP -->

    <section class="panel filter-panel">

        <div class="panel-heading">
            <h4 class="panel-title">Filter Rekap</h4>
        </div>

        <form method="GET" class="filter-form">

            <div class="form-group">

                <label for="kelas_id" class="form-label">
                    Pilih Kelas
                </label>

                <select
                    name="kelas_id"
                    id="kelas_id"
                    class="form-select"
                >

                    <option value="0">
                        Semua Kelas Wali Kelas
                    </option>

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

            <a
                href="rekap_pelanggaran.php"
                class="button button-light"
            >
                Reset
            </a>

        </form>

    </section>

    <!-- TABEL REKAP -->

    <section class="panel">

        <div class="panel-heading">

            <h4 class="panel-title">
                Data Rekap Siswa
            </h4>

            <div class="action-group">

                <a
                    href="rekap_pelanggaran.php?<?= e(http_build_query(array_merge($parameter_filter, ['export' => 'excel']))) ?>"
                    class="button button-green"
                >
                    Ekspor Excel
                </a>

                <button
                    type="button"
                    onclick="cetakRekap()"
                    class="button button-dark"
                >
                    Cetak Rekap
                </button>

            </div>

        </div>

        <div class="table-responsive">

            <table
                id="tabelRekap"
                class="table table-hover align-middle w-100"
            >

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

                        <?php foreach ($data_rekap as $i => $data): ?>

                            <tr>

                                <td><?= $i + 1 ?></td>

                                <td><?= e($data['nis']) ?></td>

                                <td><?= e($data['nisn']) ?></td>

                                <td>
                                    <span class="student-name">
                                        <?= e($data['nama_siswa']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e($data['nama_kelas']) ?>
                                </td>

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

                                <td>
                                    <div class="violation-list">
                                        <?= $data['daftar_pelanggaran']
                                            ? nl2br(e($data['daftar_pelanggaran']))
                                            : '-' ?>
                                    </div>
                                </td>

                                <td>
                                    <?php
                                    $tanggal = $data['tanggal_terakhir'] ?? '';

                                    echo $tanggal && $tanggal !== '0000-00-00'
                                        ? e(date('d-m-Y', strtotime($tanggal)))
                                        : '-';
                                    ?>
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

    <!-- FOOTER -->

    <div class="footer">
        Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
    </div>

</main>

<!-- JAVASCRIPT -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
let tabelRekap = null;
let panjangHalamanSebelumnya = 10;

$(document).ready(function () {

    const jumlahData = <?= count($data_rekap) ?>;

    if (jumlahData > 0) {

        tabelRekap = $('#tabelRekap').DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [],

            language: {

                search: "Cari:",

                searchPlaceholder: "Cari nama, NIS, atau pelanggaran...",

                lengthMenu: "Tampilkan _MENU_ data",

                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",

                infoEmpty: "Tidak ada data rekap",

                infoFiltered: "(disaring dari _MAX_ data)",

                zeroRecords: "Data rekap tidak ditemukan",

                emptyTable: "Belum ada data pelanggaran",

                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }

            }

        });

    }

});

/* =========================================
   CETAK SELURUH DATA
========================================= */

function cetakRekap() {

    if (tabelRekap) {

        panjangHalamanSebelumnya = tabelRekap.page.len();

        // Tampilkan semua data, bukan hanya halaman aktif.
        tabelRekap.page.len(-1).draw();

        setTimeout(function () {
            window.print();
        }, 300);

    } else {

        window.print();

    }

}

/* =========================================
   KEMBALIKAN PAGINATION SETELAH CETAK
========================================= */

window.addEventListener('afterprint', function () {

    if (tabelRekap) {

        tabelRekap.page.len(panjangHalamanSebelumnya).draw();

    }

});
</script>

</body>
</html>
