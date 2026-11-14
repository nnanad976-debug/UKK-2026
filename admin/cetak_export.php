
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_set_charset($koneksi, 'utf8mb4');

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/* =========================
   INISIALISASI DATA
========================= */

$siswa_id = filter_input(INPUT_GET, 'siswa_id', FILTER_VALIDATE_INT);

if (!$siswa_id || $siswa_id < 1) {
    $siswa_id = '';
}

$data_siswa = null;
$kelas = '-';
$pelanggaran_data = [];
$total_poin = 0;
$error = '';

/* =========================
   AMBIL DAFTAR SISWA
========================= */

try {
    $query_siswa = mysqli_query(
        $koneksi,
        "SELECT id, nis, nama
         FROM t_siswa
         WHERE status_aktif = 1
         ORDER BY nama ASC"
    );
} catch (Throwable $exception) {
    $query_siswa = false;
    $error = "Daftar siswa gagal dimuat.";
}

/* =========================
   AMBIL IDENTITAS SISWA
========================= */

if ($siswa_id !== '' && !$error) {
    try {
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT *
             FROM t_siswa
             WHERE id = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "i", $siswa_id);
        mysqli_stmt_execute($stmt);

        $data_siswa = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        );

        mysqli_stmt_close($stmt);

        if (!$data_siswa) {
            $error = "Data siswa tidak ditemukan.";
        }
    } catch (Throwable $exception) {
        $error = "Data siswa gagal dimuat.";
    }
}

/* =========================
   AMBIL KELAS SISWA
========================= */

if ($data_siswa && !$error) {
    try {
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                k.nama AS nama_kelas,
                k.tingkat,
                k.jurusan
             FROM t_kelas_siswa ks
             INNER JOIN t_kelas k
                ON ks.kelas_id = k.id
             WHERE ks.siswa_id = ?
               AND ks.status_aktif = 1
             ORDER BY
                ks.tanggal_mulai DESC,
                ks.id DESC
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "i", $siswa_id);
        mysqli_stmt_execute($stmt);

        $data_kelas = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        );

        if ($data_kelas) {
            $kelas = $data_kelas['nama_kelas']
                ?: trim(
                    ($data_kelas['tingkat'] ?? '') . ' ' .
                    ($data_kelas['jurusan'] ?? '')
                );
        }

        mysqli_stmt_close($stmt);

    } catch (Throwable $exception) {
        $error = "Data kelas siswa gagal dimuat.";
    }
}

/* =========================
   AMBIL RIWAYAT PELANGGARAN
========================= */

if ($data_siswa && !$error) {
    try {
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                ps.id,
                ps.tanggal,
                ps.nama_pelanggaran,
                ps.keterangan,
                ps.poin,
                COALESCE(
                    kat.nama,
                    'Kategori tidak tersedia'
                ) AS nama_kategori
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_pelanggaran_kategori kat
                ON ps.pelanggaran_kategori_id = kat.id
             WHERE ps.siswa_id = ?
             ORDER BY
                ps.tanggal DESC,
                ps.id DESC"
        );

        mysqli_stmt_bind_param($stmt, "i", $siswa_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($p = mysqli_fetch_assoc($result)) {
            $pelanggaran_data[] = $p;
            $total_poin += (int)($p['poin'] ?? 0);
        }

        mysqli_stmt_close($stmt);

    } catch (Throwable $exception) {
        $error = "Riwayat pelanggaran gagal dimuat.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --text: #2f435a;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 282px;
            min-width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            background: #fff;
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 1000;
        }

        .brand {
            padding: 0 15px 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 22px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sidebar .menu-link {
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
            transition: background-color .15s ease, color .15s ease;
        }

        .sidebar .menu-link:hover,
        .sidebar .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .sidebar .menu-link.active {
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .sidebar .logout .menu-link {
            color: #d9534f;
        }

        .sidebar .logout .menu-link:hover {
            background: #fff0ef;
            color: #c03935;
        }

        /* KONTEN */

        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .main .container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .judul,
        .main h4,
        .main h5 {
            color: #234e78;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
            overflow: hidden;
        }

        .card-body {
            padding: 24px;
        }

        /* FORM */

        .form-label {
            color: #405873;
            font-size: 16px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
            color: #344b63;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        /* TOMBOL */

        .btn {
            border-radius: 7px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            padding: 8px 15px;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
        }

        .btn-secondary {
            --bs-btn-bg: #e8f0f8;
            --bs-btn-border-color: #d8e5f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
        }

        .btn-export {
            background: #fff;
            color: #315b82;
            border: 1px solid #cbddeb;
        }

        .btn-export:hover {
            background: var(--blue-soft);
            color: var(--blue-dark);
            border-color: #b6d2ee;
        }

        /* TABEL */

        .table-responsive {
            width: 100%;
        }

        .table {
            width: 100%;
            color: #344b63;
            vertical-align: middle;
            margin-bottom: 0;
            font-size: 14px;
        }

        .table thead th {
            background: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
        }

        .table tbody td {
            border-color: #e5edf5;
            padding: 11px 12px;
        }

        .table-hover tbody tr:hover > * {
            --bs-table-accent-bg: #f4f8fc;
        }

        .identitas {
            background: var(--blue-soft) !important;
            font-weight: 600;
            color: #315b82;
        }

        .total-poin td {
            background: #eaf3ff !important;
            font-weight: 700;
        }

        .badge-kategori {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: var(--blue-soft);
            color: #315b82;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* KEPALA LAPORAN */

        .kartu-header {
            background: var(--blue-dark);
            color: #fff;
        }

        .kartu-header h2 {
            font-size: 25px;
            font-weight: 700;
        }

        .kartu-header strong {
            font-size: 15px;
        }

        /* TOMBOL LAPORAN */

        .tombol-laporan {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
                height: auto;
                min-height: auto;
                max-height: none;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .logout {
                margin-top: 14px;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 17px;
            }

            .kartu-header h2 {
                font-size: 20px;
            }

            .kartu-header strong {
                font-size: 13px;
            }

            .tombol-laporan {
                justify-content: stretch;
            }

            .tombol-laporan .btn {
                flex: 1 1 auto;
            }
        }

        /* CETAK DAN EXPORT PDF */

        @media print {
            html,
            body {
                width: 100%;
                min-height: auto;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000;
                font-size: 11px;
            }

            .sidebar,
            .no-print {
                display: none !important;
            }

            .main {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .main .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                border: 1px solid #999 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
            }

            .card-body {
                padding: 14px !important;
            }

            .kartu-header {
                background: #203e68 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .identitas,
            .total-poin td,
            .table thead th,
            .badge-kategori {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table {
                width: 100% !important;
                font-size: 10px !important;
            }

            .table th,
            .table td {
                padding: 7px !important;
            }

            .badge-kategori {
                border: 1px solid #dce9f8;
            }

            .row {
                display: flex !important;
                flex-wrap: nowrap !important;
            }

            .row > .col-6 {
                width: 50% !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            h5 {
                break-after: avoid;
            }

            tr {
                break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar no-print">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru.php" class="menu-link">Kelola Guru</a>
        <a href="siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link active">Laporan</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>

</aside>

<!-- KONTEN UTAMA -->
<main class="main">
    <div class="container">

        <!-- TOMBOL KEMBALI -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 no-print">

            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

        </div>

        <!-- FORM PILIH SISWA -->
        <div class="card mb-4 no-print">
            <div class="card-body">

                <h2 class="judul mb-1">Laporan Pelanggaran Siswa</h2>

                <p class="text-muted mb-4">
                    Pilih siswa untuk melihat laporan dan mencetak atau menyimpannya sebagai PDF.
                </p>

                <?php if ($query_siswa): ?>

                    <form method="GET" action="cetak_export.php">

                        <div class="row align-items-end g-3">

                            <div class="col-md-9">

                                <label for="siswa_id" class="form-label">
                                    Pilih Siswa
                                </label>

                                <select
                                    id="siswa_id"
                                    name="siswa_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">-- Pilih Siswa --</option>

                                    <?php
                                    mysqli_data_seek($query_siswa, 0);

                                    while ($s = mysqli_fetch_assoc($query_siswa)):
                                    ?>

                                        <option
                                            value="<?= (int)$s['id']; ?>"
                                            <?= (string)$siswa_id === (string)$s['id'] ? 'selected' : ''; ?>
                                        >
                                            <?= e($s['nis']); ?> - <?= e($s['nama']); ?>
                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100"
                                >
                                    Tampilkan Laporan
                                </button>

                            </div>

                        </div>

                    </form>

                <?php endif; ?>

            </div>
        </div>

        <!-- NOTIFIKASI ERROR -->
        <?php if ($error !== ''): ?>

            <div class="alert alert-danger" role="alert">
                <?= e($error); ?>
            </div>

        <?php endif; ?>

        <!-- HASIL LAPORAN -->
        <?php if ($data_siswa && $error === ''): ?>

            <div class="card mb-4" id="areaLaporan">

                <div class="card-header kartu-header text-center py-4">

                    <h2 class="mb-1">LAPORAN PELANGGARAN SISWA</h2>

                    <strong>SMK MUHAMMADIYAH KOTA TASIKMALAYA</strong>

                </div>

                <div class="card-body p-4">

                    <!-- IDENTITAS SISWA -->
                    <h5 class="mb-3">Identitas Siswa</h5>

                    <div class="table-responsive mb-4">

                        <table class="table table-bordered">

                            <tbody>

                                <tr>

                                    <td class="identitas" style="width:15%">
                                        NIS
                                    </td>

                                    <td style="width:35%">
                                        <?= e($data_siswa['nis'] ?? '-'); ?>
                                    </td>

                                    <td class="identitas" style="width:15%">
                                        NISN
                                    </td>

                                    <td style="width:35%">
                                        <?= e($data_siswa['nisn'] ?? '-'); ?>
                                    </td>

                                </tr>

                                <tr>

                                    <td class="identitas">
                                        Nama
                                    </td>

                                    <td colspan="3">
                                        <?= e($data_siswa['nama'] ?? '-'); ?>
                                    </td>

                                </tr>

                                <tr>

                                    <td class="identitas">
                                        Jenis Kelamin
                                    </td>

                                    <td>
                                        <?php
                                        $jk = $data_siswa['jenis_kelamin'] ?? '';

                                        echo $jk === 'L'
                                            ? 'Laki-laki'
                                            : ($jk === 'P' ? 'Perempuan' : '-');
                                        ?>
                                    </td>

                                    <td class="identitas">
                                        Kelas
                                    </td>

                                    <td>
                                        <?= e($kelas); ?>
                                    </td>

                                </tr>

                                <tr>

                                    <td class="identitas">
                                        Alamat
                                    </td>

                                    <td colspan="3">
                                        <?= e($data_siswa['alamat'] ?? '-'); ?>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <!-- RIWAYAT PELANGGARAN -->
                    <h5 class="mb-3">Riwayat Pelanggaran</h5>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>
                                <tr>
                                    <th style="width:7%" class="text-center">
                                        No
                                    </th>

                                    <th style="width:16%">
                                        Tanggal
                                    </th>

                                    <th>
                                        Pelanggaran
                                    </th>

                                    <th style="width:20%">
                                        Kategori
                                    </th>

                                    <th style="width:12%" class="text-center">
                                        Poin
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (count($pelanggaran_data) > 0): ?>

                                    <?php $no = 1; ?>

                                    <?php foreach ($pelanggaran_data as $p): ?>

                                        <tr>

                                            <td class="text-center">
                                                <?= $no++; ?>
                                            </td>

                                            <td>
                                                <?= e($p['tanggal'] ?? '-'); ?>
                                            </td>

                                            <td>

                                                <?= e($p['nama_pelanggaran'] ?? '-'); ?>

                                                <?php if (!empty($p['keterangan'])): ?>

                                                    <br>

                                                    <small class="text-muted">
                                                        <?= e($p['keterangan']); ?>
                                                    </small>

                                                <?php endif; ?>

                                            </td>

                                            <td>
                                                <span class="badge-kategori">
                                                    <?= e($p['nama_kategori']); ?>
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <?= (int)($p['poin'] ?? 0); ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada riwayat pelanggaran untuk siswa ini.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                                <tr class="total-poin">

                                    <td colspan="4" class="text-end">
                                        TOTAL POIN
                                    </td>

                                    <td class="text-center">
                                        <?= $total_poin; ?>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <!-- TANDA TANGAN -->
                    <div class="row mt-5">

                        <div class="col-6 text-center">

                            Mengetahui,<br>
                            Wali Kelas

                            <br><br><br><br>

                            (________________________)

                        </div>

                        <div class="col-6 text-center">

                            Guru Pencatat Pelanggaran

                            <br><br><br><br>

                            (________________________)

                        </div>

                    </div>

                    <!-- TOMBOL CETAK DAN EXPORT -->
                    <div class="tombol-laporan mt-4 no-print">

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="cetakLaporan()"
                        >
                            Cetak Laporan
                        </button>

                        <button
                            type="button"
                            class="btn btn-export"
                            onclick="exportPDF()"
                        >
                            Export PDF
                        </button>

                    </div>

                </div>

            </div>

        <?php elseif ($siswa_id !== '' && $error === ''): ?>

            <div class="alert alert-warning">
                Data siswa tidak ditemukan.
            </div>

        <?php endif; ?>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* =========================
   CETAK LAPORAN
========================= */

function cetakLaporan() {

    window.print();

}

/* =========================
   EXPORT PDF
   PILIH SAVE AS PDF
   PADA DIALOG CETAK
========================= */

function exportPDF() {

    window.print();

}
</script>

</body>
</html>
