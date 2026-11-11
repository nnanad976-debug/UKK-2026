
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   PEMERIKSAAN AKSES
========================================= */

$role = strtolower(trim($_SESSION['role'] ?? ''));
$isWaliKelas = !empty($_SESSION['is_wali_kelas']);

$guruId = (int)($_SESSION['guru_id'] ?? 0);
$userId = (int)($_SESSION['user_id'] ?? 0);

if ($role !== 'guru' || !$isWaliKelas) {
    http_response_code(403);
    exit("Anda tidak memiliki akses ke halaman ini.");
}

/* =========================================
   FUNGSI BANTU
========================================= */

function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function tanggalValid($tanggal): bool
{
    if ($tanggal === '') {
        return true;
    }

    $bagian = explode('-', $tanggal);

    if (count($bagian) !== 3) {
        return false;
    }

    return checkdate(
        (int)$bagian[1],
        (int)$bagian[2],
        (int)$bagian[0]
    );
}

function bindParameterDinamis($stmt, $types, &$params): bool
{
    if ($types === '' || empty($params)) {
        return true;
    }

    $bind = [];
    $bind[] = &$types;

    foreach ($params as $key => &$value) {
        $bind[] = &$value;
    }

    return call_user_func_array(
        [$stmt, 'bind_param'],
        $bind
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

$filterSiswaId = filter_input(
    INPUT_GET,
    'siswa_id',
    FILTER_VALIDATE_INT
);

$filterSiswaId = ($filterSiswaId && $filterSiswaId > 0)
    ? (int)$filterSiswaId
    : 0;

$tanggalMulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggalSelesai = trim($_GET['tanggal_selesai'] ?? '');

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
        SELECT id, nama
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

    if ($dataGuru) {
        $namaWaliKelas = $dataGuru['nama'];
    }

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
        $guruId = (int)$dataGuru['id'];
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

    $kelasId = (int)$penugasan['kelas_id'];
    $tahunAjaranId = (int)$penugasan['tahun_ajaran_id'];

    $namaKelas = $penugasan['nama_kelas'] ?? '';

    $namaTahunAjaran = $penugasan['nama_tahun_ajaran']
        ?: 'Tahun Ajaran ID ' . $tahunAjaranId;

    /* =====================================
       DAFTAR SISWA KELAS
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

    $stmt->bind_param("ii", $kelasId, $tahunAjaranId);
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
            if ((int)$siswa['id'] === $filterSiswaId) {
                $siswaDitemukan = true;
                break;
            }
        }

        if (!$siswaDitemukan) {
            $filterSiswaId = 0;
            $pesan = "Siswa yang dipilih tidak ditemukan dalam kelas Anda.";
        }
    }

    /* =====================================
       AMBIL DATA REKAP
    ===================================== */

    if ($pesan === '') {

        $koneksi->query("
            SET SESSION group_concat_max_len = 100000
        ");

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

        if (!bindParameterDinamis(
            $stmt,
            $tipeParameter,
            $parameter
        )) {
            die("Parameter laporan gagal diproses.");
        }

        $stmt->execute();

        $hasil = $stmt->get_result();

        while ($row = $hasil->fetch_assoc()) {

            $row['jumlah_pelanggaran'] =
                (int)$row['jumlah_pelanggaran'];

            $row['total_poin'] =
                (int)$row['total_poin'];

            $daftarSiswa[] = $row;

            $totalSiswa++;
            $totalPelanggaran += $row['jumlah_pelanggaran'];
            $totalPoin += $row['total_poin'];
        }

        $stmt->close();
    }
}

/* =========================================
   JUDUL DAN PERIODE LAPORAN
========================================= */

$judulMode = $filterSiswaId > 0
    ? 'LAPORAN PELANGGARAN SISWA'
    : 'REKAPITULASI PELANGGARAN SELURUH SISWA';

$periode = 'Semua Tanggal';

if ($tanggalMulai !== '' || $tanggalSelesai !== '') {
    $periode =
        ($tanggalMulai !== '' ? $tanggalMulai : 'Awal')
        . ' s.d. ' .
        ($tanggalSelesai !== '' ? $tanggalSelesai : 'Sekarang');
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan - Sistem Pelanggaran</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --page: #f6f9fd;
    --white: #fff;
    --text: #263f63;
    --muted: #7890ad;
    --line: #dce9f8;
    --shadow: 0 8px 22px rgba(32,62,104,.07);
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

/* =========================================
   SIDEBAR - SAMA DENGAN CATAT PELANGGARAN
========================================= */

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
    font-weight: 500;
    transition: .2s;
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

/* =========================================
   KONTEN UTAMA
========================================= */

.main {
    width: calc(100% - 282px);
    min-height: 100vh;
    margin-left: 282px;
    padding: 24px 28px 40px;
}

.page-head {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 11px;
    padding: 24px 25px;
    margin-bottom: 22px;
    box-shadow: 0 3px 12px rgba(32,62,104,.04);
}

.page-head h3 {
    color: var(--blue-dark);
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 7px;
}

.page-head p {
    color: var(--muted);
    font-size: 13px;
    margin: 0;
}

/* =========================================
   PANEL
========================================= */

.panel {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 22px;
    box-shadow: var(--shadow);
}

.panel-header {
    margin-bottom: 20px;
}

.panel-header h4 {
    color: var(--blue-dark);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
}

.panel-header p {
    color: var(--muted);
    font-size: 12px;
    margin: 0;
}

/* =========================================
   FORM
========================================= */

.form-label {
    color: #405873;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 7px;
}

.form-control,
.form-select {
    width: 100%;
    min-height: 42px;
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
    box-shadow: 0 0 0 3px rgba(40,120,207,.10);
}

.btn {
    border-radius: 8px;
    padding: 10px 17px;
    font-size: 13px;
    font-weight: 600;
    transition: .2s;
}

.btn-primary {
    background: var(--blue);
    border: 1px solid var(--blue);
    color: #fff;
}

.btn-primary:hover {
    background: var(--blue-dark);
    border-color: var(--blue-dark);
    color: #fff;
}

.btn-secondary {
    background: #f1f6fc;
    border: 1px solid var(--line);
    color: #426584;
}

.btn-secondary:hover {
    background: #e5effa;
    border-color: #cbddeb;
    color: var(--blue-dark);
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

/* =========================================
   ALERT
========================================= */

.alert {
    border-radius: 10px;
    font-size: 13px;
    padding: 13px 16px;
}

/* =========================================
   HEADER LAPORAN
========================================= */

.kartu-header {
    background: var(--blue-dark);
    color: #fff;
    text-align: center;
    padding: 25px 20px;
    border-radius: 11px 11px 0 0;
}

.kartu-header h2 {
    font-size: 23px;
    font-weight: 700;
    margin: 0 0 6px;
}

.kartu-header strong {
    font-size: 13px;
    letter-spacing: .3px;
}

/* =========================================
   INFORMASI LAPORAN
========================================= */

.info-laporan {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}

.info-laporan td {
    padding: 10px 12px;
    border: 1px solid #e1eaf4;
    font-size: 13px;
}

.info-label {
    width: 18%;
    background: var(--blue-soft);
    color: #315b82;
    font-weight: 600;
}

.info-value {
    color: #344b63;
}

/* =========================================
   TABEL
========================================= */

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    color: #344b63;
    vertical-align: middle;
    margin-bottom: 0;
    font-size: 13px;
}

.table thead th {
    background: #f5f8fc;
    color: #7890ad;
    border-color: var(--line);
    font-size: 12px;
    font-weight: 600;
    padding: 14px;
    white-space: nowrap;
    text-align: center;
}

.table tbody td {
    border-color: #e5edf5;
    padding: 13px 14px;
}

.table tbody tr:hover td {
    background: #fafcff;
}

.table tfoot td {
    background: var(--blue-soft);
    color: #234e78;
    font-weight: 700;
    padding: 12px;
    border-color: var(--line);
}

.text-center {
    text-align: center !important;
}

.rincian {
    min-width: 300px;
    line-height: 1.6;
}

.total-poin {
    font-weight: 700;
    color: #234e78;
}

.subjudul-tabel {
    color: var(--blue-dark);
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 15px;
}

/* =========================================
   TANDA TANGAN
========================================= */

.tanda-tangan {
    margin-top: 42px;
    display: flex;
    justify-content: space-between;
    gap: 20px;
    text-align: center;
    color: #344b63;
    font-size: 13px;
}

.tanda-tangan > div {
    width: 45%;
}

/* =========================================
   RESPONSIVE
========================================= */

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

    .page-head {
        padding: 19px;
    }

    .page-head h3 {
        font-size: 23px;
    }

    .panel {
        padding: 17px;
    }

    .kartu-header h2 {
        font-size: 19px;
    }

    .tanda-tangan {
        font-size: 11px;
    }

    .info-laporan td {
        padding: 8px;
        font-size: 11px;
    }
}

/* =========================================
   CETAK
========================================= */

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
        font-family: Arial, sans-serif !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .sidebar,
    .no-print,
    .page-head {
        display: none !important;
    }

    .main {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .panel {
        width: 100% !important;
        border: 1px solid #999 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
    }

    .kartu-header {
        background: #203e68 !important;
        color: #fff !important;
        border-radius: 0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .kartu-header h2 {
        color: #fff !important;
        font-size: 17pt !important;
    }

    .panel-body-laporan {
        padding: 14px !important;
    }

    .info-laporan {
        margin-bottom: 18px !important;
    }

    .info-laporan td {
        font-size: 9pt !important;
        padding: 7px !important;
        border: 1px solid #999 !important;
        color: #000 !important;
    }

    .info-label {
        background: #eaf3ff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .table {
        width: 100% !important;
        font-size: 9pt !important;
        border-collapse: collapse !important;
    }

    .table thead {
        display: table-header-group;
    }

    .table thead th {
        background: #eaf2fa !important;
        color: #000 !important;
        border: 1px solid #777 !important;
        padding: 8px 6px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table tbody td,
    .table tfoot td {
        border: 1px solid #777 !important;
        color: #000 !important;
        padding: 7px 6px !important;
        font-size: 9pt !important;
        overflow-wrap: break-word;
    }

    .table tfoot td {
        background: #eaf3ff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .rincian {
        min-width: 0 !important;
    }

    .tanda-tangan {
        color: #000 !important;
        break-inside: avoid;
        page-break-inside: avoid;
        margin-top: 35px !important;
    }

    .tanda-tangan p {
        color: #000 !important;
    }
}
</style>
</head>

<body>

<!-- =========================================
     SIDEBAR
========================================= -->

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

        <a href="rekap_pelanggaran.php" class="menu-link">
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php" class="menu-link active" aria-current="page">
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

<!-- =========================================
     KONTEN UTAMA
========================================= -->

<main class="main">

    <div class="page-head">
        <h3>Laporan</h3>
        <p>Cetak rekap pelanggaran seluruh siswa atau laporan pelanggaran secara individu.</p>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-warning mb-4">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($penugasan && $pesan === ''): ?>

        <!-- =================================
             FILTER LAPORAN
        ================================== -->

        <section class="panel no-print">

            <div class="panel-header">
                <h4>Filter Laporan</h4>
                <p>Pilih semua siswa untuk melihat rekap satu kelas sekaligus, atau pilih satu siswa untuk laporan individu.</p>
            </div>

            <form method="GET" action="cetak_rekap.php">

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="siswa_id" class="form-label">
                            Pilih Siswa
                        </label>

                        <select
                            name="siswa_id"
                            id="siswa_id"
                            class="form-select"
                        >
                            <option value="0"
                                <?= $filterSiswaId === 0 ? 'selected' : '' ?>>
                                Semua Siswa
                            </option>

                            <?php foreach ($daftarPilihanSiswa as $siswa): ?>
                                <option
                                    value="<?= (int)$siswa['id'] ?>"
                                    <?= $filterSiswaId === (int)$siswa['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($siswa['nisn'] ?: '-') ?> -
                                    <?= e($siswa['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label">
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
                        <label for="tanggal_selesai" class="form-label">
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

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary">
                            Tampilkan
                        </button>
                    </div>

                </div>

                <div class="d-flex gap-2 flex-wrap mt-3">
                    <a href="cetak_rekap.php" class="btn btn-secondary">
                        Reset Filter
                    </a>
                </div>

            </form>

        </section>

        <!-- =================================
             HASIL LAPORAN
        ================================== -->

        <section class="panel p-0">

            <div class="kartu-header">
                <h2><?= e($judulMode) ?></h2>
                <strong>SMK MUHAMMADIYAH KOTA TASIKMALAYA</strong>
            </div>

            <div class="panel-body-laporan p-4">

                <h4 class="subjudul-tabel">Informasi Rekap</h4>

                <div class="table-responsive mb-4">

                    <table class="info-laporan">
                        <tbody>

                            <tr>
                                <td class="info-label">Kelas</td>
                                <td class="info-value">
                                    <?= e($namaKelas) ?>
                                </td>

                                <td class="info-label">Tahun Ajaran</td>
                                <td class="info-value">
                                    <?= e($namaTahunAjaran) ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="info-label">Periode</td>
                                <td class="info-value">
                                    <?= e($periode) ?>
                                </td>

                                <td class="info-label">Jumlah Siswa</td>
                                <td class="info-value">
                                    <?= number_format($totalSiswa) ?> siswa
                                </td>
                            </tr>

                            <tr>
                                <td class="info-label">Wali Kelas</td>
                                <td class="info-value" colspan="3">
                                    <?= e($namaWaliKelas ?: '-') ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="info-label">Jenis Laporan</td>
                                <td class="info-value" colspan="3">
                                    <?= $filterSiswaId > 0
                                        ? 'Laporan individu siswa'
                                        : 'Rekap seluruh siswa dalam satu kelas' ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </div>

                <h4 class="subjudul-tabel">
                    Rekapitulasi Pelanggaran Siswa
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:14%">NISN</th>
                                <th style="width:20%">Nama Siswa</th>
                                <th>Rincian Pelanggaran</th>
                                <th style="width:10%">Jumlah</th>
                                <th style="width:12%">Total Poin</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($daftarSiswa)): ?>

                            <?php foreach ($daftarSiswa as $index => $siswa): ?>
                                <tr>

                                    <td class="text-center">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <?= e($siswa['nisn'] ?: '-') ?>
                                    </td>

                                    <td>
                                        <?= e($siswa['nama']) ?>
                                    </td>

                                    <td class="rincian">
                                        <?= e(
                                            $siswa['rincian_pelanggaran']
                                            ?: 'Tidak ada pelanggaran'
                                        ) ?>
                                    </td>

                                    <td class="text-center">
                                        <?= number_format($siswa['jumlah_pelanggaran']) ?>
                                    </td>

                                    <td class="text-center total-poin">
                                        <?= number_format($siswa['total_poin']) ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Tidak ada data siswa pada kelas ini.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                        <?php if (!empty($daftarSiswa)): ?>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        TOTAL KESELURUHAN
                                    </td>

                                    <td class="text-center">
                                        <?= number_format($totalPelanggaran) ?>
                                    </td>

                                    <td class="text-center">
                                        <?= number_format($totalPoin) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>

                    </table>

                </div>

                <!-- TANDA TANGAN -->

                <div class="tanda-tangan">

                    <div>
                        Mengetahui,<br>
                        Guru Pencatat Pelanggaran

                        <br><br><br><br>

                        (________________________)
                    </div>

                    <div>
                        Tasikmalaya, <?= date('d-m-Y') ?><br>
                        Wali Kelas

                        <br><br><br><br>

                        <strong>
                            (<?= e($namaWaliKelas ?: '________________________') ?>)
                        </strong>
                    </div>

                </div>

                <!-- TOMBOL CETAK -->

                <div class="d-flex justify-content-end gap-2 flex-wrap mt-4 no-print">

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

        </section>

    <?php endif; ?>

    <footer class="text-center mt-4 mb-2" style="color:#91a5bd;font-size:11px;">
        © 2026 Sistem Pelanggaran - SMK Muhammadiyah
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
function cetakLaporan() {
    window.print();
}

function exportPDF() {
    window.print();
}
</script>

</body>
</html>
