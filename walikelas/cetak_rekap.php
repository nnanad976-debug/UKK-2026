<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   PEMERIKSAAN AKSES
========================================= */

$role = strtolower(trim($_SESSION['role'] ?? ''));
$isWaliKelas = !empty($_SESSION['is_wali_kelas']);

$guruId = (int) ($_SESSION['guru_id'] ?? 0);
$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($role !== 'guru' || !$isWaliKelas) {
    http_response_code(403);
    exit("Anda tidak memiliki akses ke halaman ini.");
}

/* =========================================
   FUNGSI BANTU
========================================= */

function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function tanggalValid($tanggal)
{
    if ($tanggal === '') {
        return true;
    }

    $bagian = explode('-', $tanggal);

    if (count($bagian) !== 3) {
        return false;
    }

    return checkdate(
        (int) $bagian[1],
        (int) $bagian[2],
        (int) $bagian[0]
    );
}

/* =========================================
   INISIALISASI
========================================= */

$pesan = '';

$kelasId = 0;
$tahunAjaranId = 0;

$namaKelas = '';
$namaTahunAjaran = '';
$namaWaliKelas = '';

$daftarSiswa = [];
$daftarPilihanSiswa = [];

$totalSiswa = 0;
$totalPelanggaran = 0;
$totalPoin = 0;

$filterSiswaId = (int) ($_GET['siswa_id'] ?? 0);
$tanggalMulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggalSelesai = trim($_GET['tanggal_selesai'] ?? '');

$modeCetak = $filterSiswaId > 0 ? 'individu' : 'semua';

$penugasan = null;

/* =========================================
   VALIDASI TANGGAL
========================================= */

if (
    !tanggalValid($tanggalMulai) ||
    !tanggalValid($tanggalSelesai)
) {
    $pesan = "Format tanggal tidak valid.";
}

if (
    $tanggalMulai !== '' &&
    $tanggalSelesai !== '' &&
    $tanggalMulai > $tanggalSelesai
) {
    $pesan = "Tanggal mulai tidak boleh melebihi tanggal akhir.";
}

/* =========================================
   AMBIL DATA GURU
========================================= */

if ($guruId > 0) {

    $stmt = $koneksi->prepare("
        SELECT nama
        FROM t_guru
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        die("Query guru gagal: " . e($koneksi->error));
    }

    $stmt->bind_param("i", $guruId);
    $stmt->execute();

    $dataGuru = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    $namaWaliKelas = $dataGuru['nama'] ?? '';

} elseif ($userId > 0) {

    $stmt = $koneksi->prepare("
        SELECT id, nama
        FROM t_guru
        WHERE user_id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        die("Query guru gagal: " . e($koneksi->error));
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $dataGuru = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if ($dataGuru) {
        $guruId = (int) $dataGuru['id'];
        $namaWaliKelas = $dataGuru['nama'];
    }
}

if ($guruId <= 0) {
    http_response_code(403);
    exit("Data guru tidak ditemukan.");
}

/* =========================================
   AMBIL PENUGASAN WALI KELAS AKTIF
========================================= */

$stmt = $koneksi->prepare("
    SELECT
        wk.tahun_ajaran_id,
        wk.kelas_id,
        k.nama AS nama_kelas,
        ta.nama AS nama_tahun_ajaran

    FROM t_wali_kelas wk

    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id

    LEFT JOIN t_tahun_ajaran ta
        ON ta.id = wk.tahun_ajaran_id

    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND k.status_aktif = 1
      AND wk.tanggal_mulai <= CURDATE()
      AND (
          wk.tanggal_selesai IS NULL
          OR wk.tanggal_selesai >= CURDATE()
      )

    ORDER BY wk.id DESC
    LIMIT 1
");

if (!$stmt) {
    die("Query penugasan gagal: " . e($koneksi->error));
}

$stmt->bind_param("i", $guruId);
$stmt->execute();

$penugasan = $stmt->get_result()->fetch_assoc();

$stmt->close();

/* =========================================
   PROSES DATA LAPORAN
========================================= */

if (!$penugasan) {

    $pesan = "Penugasan wali kelas aktif tidak ditemukan.";

} else {

    $kelasId = (int) $penugasan['kelas_id'];
    $tahunAjaranId = (int) $penugasan['tahun_ajaran_id'];

    $namaKelas = $penugasan['nama_kelas'];

    $namaTahunAjaran = $penugasan['nama_tahun_ajaran']
        ?: 'Tahun Ajaran ID ' . $tahunAjaranId;

    /* =====================================
       AMBIL DAFTAR SISWA UNTUK FILTER
    ===================================== */

    $stmt = $koneksi->prepare("
        SELECT
            s.id,
            s.nisn,
            s.nama

        FROM t_kelas_siswa ks

        INNER JOIN t_siswa s
            ON s.id = ks.siswa_id

        WHERE ks.kelas_id = ?
          AND ks.tahun_ajaran_id = ?
          AND ks.status_aktif = 1
          AND s.status_aktif = 1
          AND ks.tanggal_mulai <= CURDATE()
          AND (
              ks.tanggal_selesai IS NULL
              OR ks.tanggal_selesai >= CURDATE()
          )

        ORDER BY s.nama ASC
    ");

    if (!$stmt) {
        die("Query daftar siswa gagal: " . e($koneksi->error));
    }

    $stmt->bind_param(
        "ii",
        $kelasId,
        $tahunAjaranId
    );

    $stmt->execute();

    $hasilSiswa = $stmt->get_result();

    while ($siswa = $hasilSiswa->fetch_assoc()) {
        $daftarPilihanSiswa[] = $siswa;
    }

    $stmt->close();

    /* =====================================
       VALIDASI PILIHAN SISWA
    ===================================== */

    if ($filterSiswaId > 0) {

        $siswaDitemukan = false;

        foreach ($daftarPilihanSiswa as $siswa) {

            if ((int) $siswa['id'] === $filterSiswaId) {
                $siswaDitemukan = true;
                break;
            }
        }

        if (!$siswaDitemukan) {
            $filterSiswaId = 0;
            $modeCetak = 'semua';

            $pesan = "Siswa yang dipilih tidak ditemukan dalam kelas Anda.";
        }
    }

    /* =====================================
       AMBIL REKAP PELANGGARAN
    ===================================== */

    if ($pesan === '') {

        $sql = "
            SELECT
                s.id AS siswa_id,
                s.nisn,
                s.nama,

                COUNT(ps.id) AS jumlah_pelanggaran,

                COALESCE(SUM(ps.poin), 0) AS total_poin,

                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(
                            NULLIF(TRIM(ps.nama_pelanggaran), ''),
                            'Pelanggaran'
                        ),
                        ' (',
                        COALESCE(ps.poin, 0),
                        ' poin)'
                    )
                    ORDER BY ps.tanggal DESC, ps.id DESC
                    SEPARATOR '; '
                ) AS rincian_pelanggaran

            FROM t_kelas_siswa ks

            INNER JOIN t_siswa s
                ON s.id = ks.siswa_id

            LEFT JOIN t_pelanggaran_siswa ps
                ON ps.siswa_id = ks.siswa_id
                AND ps.kelas_id = ks.kelas_id
                AND ps.tahun_ajaran_id = ks.tahun_ajaran_id
        ";

        /*
         * Susunan parameter mengikuti urutan placeholder SQL:
         * 1. Tanggal mulai
         * 2. Tanggal selesai
         * 3. Kelas
         * 4. Tahun ajaran
         * 5. ID siswa (jika dipilih)
         */

        $parameter = [];
        $tipeParameter = '';

        if ($tanggalMulai !== '') {
            $sql .= " AND ps.tanggal >= ? ";
            $parameter[] = $tanggalMulai;
            $tipeParameter .= 's';
        }

        if ($tanggalSelesai !== '') {
            $sql .= " AND ps.tanggal <= ? ";
            $parameter[] = $tanggalSelesai;
            $tipeParameter .= 's';
        }

        $sql .= "
            WHERE ks.kelas_id = ?
              AND ks.tahun_ajaran_id = ?
              AND ks.status_aktif = 1
              AND s.status_aktif = 1
              AND ks.tanggal_mulai <= CURDATE()
              AND (
                  ks.tanggal_selesai IS NULL
                  OR ks.tanggal_selesai >= CURDATE()
              )
        ";

        $parameter[] = $kelasId;
        $parameter[] = $tahunAjaranId;
        $tipeParameter .= 'ii';

        if ($filterSiswaId > 0) {
            $sql .= " AND s.id = ? ";
            $parameter[] = $filterSiswaId;
            $tipeParameter .= 'i';
        }

        $sql .= "
            GROUP BY
                s.id,
                s.nisn,
                s.nama

            ORDER BY s.nama ASC
        ";

        $stmt = $koneksi->prepare($sql);

        if (!$stmt) {
            die("Query rekap gagal: " . e($koneksi->error));
        }

        /*
         * PERBAIKAN:
         * Gunakan bind_param(), bukan execute($parameter).
         */

        if (!empty($parameter)) {
            $stmt->bind_param(
                $tipeParameter,
                ...$parameter
            );
        }

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
}

/* =========================================
   NAMA MODE LAPORAN
========================================= */

$judulMode = $filterSiswaId > 0
    ? 'REKAP PELANGGARAN PER SISWA'
    : 'REKAPITULASI PELANGGARAN SELURUH SISWA';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cetak Rekap - Sistem Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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
            background: #fff;
            border-right: 1px solid #dce9f8;
            z-index: 1000;
            overflow-y: auto;
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid #dce9f8;
        }

        .brand-title {
            color: #203e68;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            color: #7890ad;
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
            font-weight: 500;
            transition: .2s;
        }

        .menu-link:hover {
            background: #f2f7fd;
            color: #2878cf;
        }

        .menu-link.active {
            background: #eaf3ff;
            color: #2878cf;
            font-weight: 600;
            box-shadow: inset 3px 0 0 #2878cf;
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #dce9f8;
        }

        .logout .menu-link {
            color: #d9534f;
        }

        .logout .menu-link:hover {
            background: #fff0ef;
        }

        /* KONTEN */

        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-title {
            font-size: 23px;
            font-weight: 700;
            color: #203e68;
            margin: 4px 6px 5px;
        }

        .page-subtitle {
            font-size: 13px;
            color: #7890ad;
            margin: 0 6px 25px;
        }

        /* FILTER */

        .filter-card {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-title {
            color: #203e68;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .filter-card label {
            display: block;
            color: #526d8d;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 40px;
            border-color: #dce9f8;
            border-radius: 8px;
            font-size: 13px;
            color: #263f63;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #2878cf;
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 17px;
        }

        .btn-filter {
            background: #2878cf;
            border: none;
            color: #fff;
            padding: 9px 17px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-filter:hover {
            background: #203e68;
            color: #fff;
        }

        .btn-reset {
            background: #eef3f9;
            color: #526d8d;
            padding: 9px 17px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-reset:hover {
            background: #e0eaf5;
            color: #203e68;
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
            cursor: pointer;
            transition: .2s;
        }

        .print-button:hover {
            background: #203e68;
        }

        /* LAPORAN */

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

        .table-responsive {
            width: 100%;
            overflow-x: auto;
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
            vertical-align: middle;
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

        /* CETAK */

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {

            html,
            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
                font-family: Arial, sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sidebar,
            .page-title,
            .page-subtitle,
            .filter-card,
            .print-button,
            .no-print {
                display: none !important;
            }

            .main {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .content-card {
                width: 100% !important;
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .report-heading {
                margin-bottom: 15px !important;
                padding-bottom: 12px !important;
            }

            .report-heading h2 {
                color: #000 !important;
                font-size: 16pt !important;
            }

            .report-heading p,
            .report-info {
                color: #000 !important;
            }

            .report-info {
                font-size: 10pt !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .report-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            .report-table th,
            .report-table td {
                border: 1px solid #777 !important;
                padding: 8px 7px !important;
                font-size: 9pt !important;
                color: #000 !important;
                overflow-wrap: break-word;
            }

            .report-table th {
                background: #eaf3ff !important;
            }

            .report-table th:nth-child(1),
            .report-table td:nth-child(1) {
                width: 5%;
                text-align: center;
            }

            .report-table th:nth-child(2),
            .report-table td:nth-child(2) {
                width: 15%;
            }

            .report-table th:nth-child(3),
            .report-table td:nth-child(3) {
                width: 21%;
            }

            .report-table th:nth-child(4),
            .report-table td:nth-child(4) {
                width: 37%;
            }

            .report-table th:nth-child(5),
            .report-table td:nth-child(5) {
                width: 11%;
                text-align: center;
            }

            .report-table th:nth-child(6),
            .report-table td:nth-child(6) {
                width: 11%;
                text-align: center;
            }

            .report-table tr {
                break-inside: avoid;
            }

            .report-total {
                background: #f3f7fc !important;
            }

            .signature {
                color: #000 !important;
                font-size: 10pt !important;
                break-inside: avoid;
            }
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {

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
                padding: 22px 18px;
            }
        }

        @media (max-width: 650px) {

            .main {
                padding: 20px 14px;
            }

            .content-card,
            .filter-card {
                padding: 16px;
            }

            .report-table th,
            .report-table td {
                padding: 8px;
            }
        }

    </style>

</head>

<body>

<!-- SIDEBAR -->

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
        MENU UTAMA
    </div>

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

        <a href="rekap_pelanggaran.php" class="menu-link">
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php" class="menu-link active">
            Cetak Rekap
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

    <h1 class="page-title">
        Cetak Rekap
    </h1>

    <p class="page-subtitle">
        Cetak seluruh rekap pelanggaran atau laporan per siswa pada kelas yang Anda ampu.
    </p>

    <?php if ($pesan !== ''): ?>

        <div class="alert alert-warning">
            <?= e($pesan) ?>
        </div>

    <?php endif; ?>

    <?php if ($penugasan): ?>

        <!-- FILTER LAPORAN -->

        <section class="filter-card no-print">

            <h3 class="filter-title">
                Filter Laporan
            </h3>

            <form method="GET" action="cetak_rekap.php">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label for="siswa_id">
                            Pilih Siswa
                        </label>

                        <select
                            name="siswa_id"
                            id="siswa_id"
                            class="form-select"
                        >

                            <option value="0">
                                Semua Siswa
                            </option>

                            <?php foreach ($daftarPilihanSiswa as $siswa): ?>

                                <option
                                    value="<?= (int) $siswa['id'] ?>"
                                    <?= $filterSiswaId === (int) $siswa['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($siswa['nama']) ?>
                                    - <?= e($siswa['nisn'] ?: '-') ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label for="tanggal_mulai">
                            Tanggal Mulai
                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            id="tanggal_mulai"
                            class="form-control"
                            value="<?= e($tanggalMulai) ?>"
                        >

                    </div>

                    <div class="col-md-3">

                        <label for="tanggal_selesai">
                            Tanggal Akhir
                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            id="tanggal_selesai"
                            class="form-control"
                            value="<?= e($tanggalSelesai) ?>"
                        >

                    </div>

                </div>

                <div class="filter-actions">

                    <button type="submit" class="btn-filter">
                        Tampilkan Data
                    </button>

                    <a href="cetak_rekap.php" class="btn-reset">
                        Reset Filter
                    </a>

                </div>

            </form>

        </section>

        <?php if ($pesan === ''): ?>

            <!-- TOMBOL CETAK -->

            <button
                type="button"
                class="print-button no-print"
                onclick="window.print()"
            >
                Cetak / Simpan PDF
            </button>

            <!-- LAPORAN -->

            <section class="content-card">

                <div class="report-heading">

                    <h2>
                        <?= e($judulMode) ?>
                    </h2>

                    <p>
                        SMK Muhammadiyah Kota Tasikmalaya
                    </p>

                </div>

                <!-- INFORMASI LAPORAN -->

                <div class="report-info">

                    <div>
                        <strong>Kelas:</strong>
                        <?= e($namaKelas) ?>
                    </div>

                    <div>
                        <strong>Tahun Ajaran:</strong>
                        <?= e($namaTahunAjaran) ?>
                    </div>

                    <?php if ($filterSiswaId > 0): ?>

                        <div>
                            <strong>Nama Siswa:</strong>
                            <?= e($daftarSiswa[0]['nama'] ?? '-') ?>
                        </div>

                    <?php endif; ?>

                    <div>
                        <strong>Periode:</strong>

                        <?php if ($tanggalMulai !== '' || $tanggalSelesai !== ''): ?>

                            <?= e($tanggalMulai ?: 'Awal') ?>
                            s.d.
                            <?= e($tanggalSelesai ?: 'Sekarang') ?>

                        <?php else: ?>

                            Semua Tanggal

                        <?php endif; ?>

                    </div>

                    <div>
                        <strong>Jumlah Siswa:</strong>
                        <?= number_format($totalSiswa) ?>
                    </div>

                    <div>
                        <strong>Dicetak Oleh:</strong>
                        <?= e($namaWaliKelas ?: '-') ?>
                    </div>

                </div>

                <!-- TABEL REKAP -->

                <div class="table-responsive">

                    <table class="report-table">

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Rincian Pelanggaran</th>
                                <th>Jumlah</th>
                                <th>Total Poin</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php if (!empty($daftarSiswa)): ?>

                            <?php foreach ($daftarSiswa as $index => $siswa): ?>

                                <tr>

                                    <td class="center">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <?= e($siswa['nisn'] ?: '-') ?>
                                    </td>

                                    <td>
                                        <?= e($siswa['nama']) ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            $siswa['rincian_pelanggaran']
                                            ?: 'Tidak ada pelanggaran'
                                        ) ?>
                                    </td>

                                    <td class="center">
                                        <?= number_format(
                                            (int) $siswa['jumlah_pelanggaran']
                                        ) ?>
                                    </td>

                                    <td class="center">
                                        <?= number_format(
                                            (int) $siswa['total_poin']
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            <tr class="report-total">

                                <td colspan="4" class="center">
                                    TOTAL
                                </td>

                                <td class="center">
                                    <?= number_format($totalPelanggaran) ?>
                                </td>

                                <td class="center">
                                    <?= number_format($totalPoin) ?>
                                </td>

                            </tr>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="center">
                                    Tidak ada data pelanggaran atau siswa pada filter yang dipilih.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <!-- TANDA TANGAN -->

                <div class="signature">

                    <div>

                        <p>
                            Tasikmalaya, <?= date('d-m-Y') ?>
                        </p>

                        <p>
                            Wali Kelas,
                        </p>

                        <br><br><br>

                        <p>
                            <strong>
                                (<?= e($namaWaliKelas ?: '....................................') ?>)
                            </strong>
                        </p>

                    </div>

                </div>

            </section>

        <?php endif; ?>

    <?php endif; ?>

</main>

</body>
</html>