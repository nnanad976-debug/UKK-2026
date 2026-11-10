<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

$pesan = '';
$jenis_pesan = '';

$guru_id = (int)($_SESSION['guru_id'] ?? 0);
$user_id = (int)($_SESSION['user_id'] ?? 0);

/* =========================================
   FUNGSI BANTU
========================================= */

function e($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function labelKelas($nama, $tingkat, $jurusan)
{
    $nama = trim((string)$nama);
    $tingkat = trim((string)$tingkat);
    $jurusan = trim((string)$jurusan);

    if (
        $nama !== '' &&
        stripos($nama, $tingkat) !== false &&
        stripos($nama, $jurusan) !== false
    ) {
        return $nama;
    }

    if ($nama !== '' && ctype_digit($nama)) {
        return trim("$tingkat $jurusan $nama");
    }

    if (
        $nama === '' ||
        strcasecmp($nama, $jurusan) === 0
    ) {
        return trim("$tingkat $jurusan");
    }

    return trim("$tingkat $jurusan $nama");
}

/* =========================================
   DATA GURU
========================================= */

$data_guru = null;

if ($guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $guru_id);
        mysqli_stmt_execute($stmt);

        $data_guru = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        );

        mysqli_stmt_close($stmt);
    }
}

/* FALLBACK USER ID */

if (!$data_guru && $user_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $data_guru = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        );

        mysqli_stmt_close($stmt);
    }
}

$guru_id = (int)($data_guru['id'] ?? 0);

$nama_guru = $data_guru['nama']
    ?? ($_SESSION['nama'] ?? 'Wali Kelas');

if ($guru_id <= 0) {
    $pesan = 'Data guru tidak ditemukan.';
    $jenis_pesan = 'danger';
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$tahun_ajaran_id = 0;

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, tahun_ajaran
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

$data_tahun = $query_tahun
    ? mysqli_fetch_assoc($query_tahun)
    : null;

$tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

$nama_tahun_ajaran = $data_tahun['tahun_ajaran'] ?? '-';

if ($tahun_ajaran_id <= 0 && $pesan === '') {
    $pesan = 'Tahun ajaran aktif belum tersedia.';
    $jenis_pesan = 'warning';
}

/* =========================================
   KELAS TANGGUNG JAWAB WALI KELAS
========================================= */

$daftar_kelas = [];

if ($guru_id > 0 && $tahun_ajaran_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT DISTINCT
            k.id,
            k.nama,
            k.tingkat,
            k.jurusan
         FROM t_wali_kelas wk
         INNER JOIN t_kelas k
            ON k.id = wk.kelas_id
         WHERE wk.guru_id = ?
           AND wk.tahun_ajaran_id = ?
           AND wk.status_aktif = 1
           AND wk.tanggal_mulai <= CURDATE()
           AND (
                wk.tanggal_selesai IS NULL
                OR wk.tanggal_selesai >= CURDATE()
           )
           AND k.status_aktif = 1
         ORDER BY
            FIELD(k.tingkat, 'X', 'XI', 'XII'),
            k.jurusan ASC,
            k.nama ASC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $guru_id,
            $tahun_ajaran_id
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $daftar_kelas[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}

/* =========================================
   REKAP PELANGGARAN SISWA

   WALI KELAS MELIHAT SEMUA PELANGGARAN
   DI KELAS TANGGUNG JAWABNYA.

   TERMASUK PELANGGARAN DARI GURU LAIN.

   NIS DIAMBIL DARI TABEL T_SISWA.
========================================= */

$daftar_rekap = [];
$total_pelanggaran = 0;
$total_poin = 0;
$total_siswa = 0;

if (
    $guru_id > 0 &&
    $tahun_ajaran_id > 0 &&
    count($daftar_kelas) > 0
) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            ps.id,
            ps.siswa_id,
            s.nis AS nis_master,
            s.nama AS nama_siswa_master,
            ps.nama_siswa,
            ps.nama_pelanggaran,
            ps.tanggal,
            ps.keterangan,
            ps.poin,
            ps.status,
            ps.guru_id,
            ps.nama_guru,
            ps.kelas_id,
            ps.nama_kelas,
            k.nama AS nama_kelas_asli,
            k.tingkat,
            k.jurusan
         FROM t_pelanggaran_siswa ps

         LEFT JOIN t_siswa s
            ON s.id = ps.siswa_id

         LEFT JOIN t_kelas k
            ON k.id = ps.kelas_id

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

         ORDER BY
            ps.tanggal DESC,
            ps.id DESC"
    );

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "iiii",
            $tahun_ajaran_id,
            $guru_id,
            $tahun_ajaran_id,
            $tahun_ajaran_id
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {

            $siswa_id = (int)$row['siswa_id'];
            $kelas_id = (int)$row['kelas_id'];

            $kunci = $siswa_id . '_' . $kelas_id;

            $nis = trim((string)($row['nis_master'] ?? ''));

            $nama_siswa = trim(
                (string)($row['nama_siswa_master'] ?? '')
            );

            if ($nama_siswa === '') {
                $nama_siswa = trim(
                    (string)($row['nama_siswa'] ?? '')
                );
            }

            $kelas_tampil = trim(
                (string)($row['nama_kelas'] ?? '-')
            );

            if (!empty($row['nama_kelas_asli'])) {
                $kelas_tampil = labelKelas(
                    $row['nama_kelas_asli'],
                    $row['tingkat'] ?? '',
                    $row['jurusan'] ?? ''
                );
            }

            $poin = (int)($row['poin'] ?? 0);

            if (!isset($daftar_rekap[$kunci])) {

                $daftar_rekap[$kunci] = [
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $kelas_id,
                    'nis' => $nis !== '' ? $nis : '-',
                    'nama_siswa' => $nama_siswa !== ''
                        ? $nama_siswa
                        : '-',
                    'nama_kelas' => $kelas_tampil,
                    'jumlah' => 0,
                    'total_poin' => 0,
                    'detail' => []
                ];

            }

            $daftar_rekap[$kunci]['jumlah']++;
            $daftar_rekap[$kunci]['total_poin'] += $poin;

            $daftar_rekap[$kunci]['detail'][] = [
                'tanggal' => $row['tanggal'],
                'nama_pelanggaran' => $row['nama_pelanggaran'],
                'keterangan' => $row['keterangan'],
                'poin' => $poin,
                'status' => $row['status'],
                'nama_guru' => $row['nama_guru']
            ];

            $total_pelanggaran++;
            $total_poin += $poin;
        }

        mysqli_stmt_close($stmt);

    } else {

        $pesan = 'Query rekap pelanggaran gagal disiapkan.';
        $jenis_pesan = 'danger';
    }
}

$total_siswa = count($daftar_rekap);

/* =========================================
   URUTKAN REKAP BERDASARKAN NAMA
========================================= */

usort(
    $daftar_rekap,
    function ($a, $b) {
        return strcasecmp(
            $a['nama_siswa'],
            $b['nama_siswa']
        );
    }
);

/* =========================================
   EKSPOR CSV
========================================= */

if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    if ($tahun_ajaran_id <= 0 || $guru_id <= 0) {
        exit('Data rekap tidak tersedia.');
    }

    $nama_file = 'rekap-pelanggaran-wali-kelas-'
        . date('Ymd-His')
        . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');

    header(
        'Content-Disposition: attachment; filename="' .
        $nama_file .
        '"'
    );

    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // BOM agar karakter CSV terbaca dengan baik di Excel.
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv(
        $output,
        [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jumlah Pelanggaran',
            'Total Poin',
            'Tanggal Pelanggaran',
            'Jenis Pelanggaran',
            'Keterangan',
            'Poin',
            'Guru yang Mencatat',
            'Status'
        ],
        ';'
    );

    $no = 1;

    foreach ($daftar_rekap as $data) {

        foreach ($data['detail'] as $detail) {

            fputcsv(
                $output,
                [
                    $no,
                    $data['nis'],
                    $data['nama_siswa'],
                    $data['nama_kelas'],
                    $data['jumlah'],
                    $data['total_poin'],
                    $detail['tanggal'],
                    $detail['nama_pelanggaran'],
                    $detail['keterangan'],
                    $detail['poin'],
                    $detail['nama_guru'],
                    $detail['status']
                ],
                ';'
            );

            $no++;
        }
    }

    fclose($output);
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

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
    background: var(--page);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
}

a {
    text-decoration: none;
}

/* =========================================
   SIDEBAR
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

.container {
    width: 100%;
    max-width: none !important;
    margin: 0 auto;
    padding: 0 !important;
}

.judul,
h4 {
    color: var(--blue-dark);
}

.card {
    background: var(--white);
    border: 1px solid var(--line) !important;
    border-radius: 16px;
    box-shadow: var(--shadow) !important;
}

.card-body {
    padding: 24px;
}

.keterangan {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

/* =========================================
   RINGKASAN
========================================= */

.summary-card {
    height: 100%;
    padding: 20px;
}

.summary-label {
    color: var(--muted);
    font-size: 12px;
    margin-bottom: 9px;
}

.summary-number {
    color: var(--blue-dark);
    font-size: 27px;
    font-weight: 700;
    line-height: 1.2;
}

.summary-note {
    color: #8ba0b9;
    font-size: 11px;
    margin-top: 7px;
}

/* =========================================
   TABEL
========================================= */

.table {
    color: #344b63;
    vertical-align: middle;
    margin-bottom: 0;
}

.table thead th {
    background: #f5f8fc;
    color: #526b89;
    border-color: var(--line);
    font-size: 12px;
    font-weight: 700;
    padding: 13px 12px;
    white-space: nowrap;
}

.table tbody td {
    border-color: #edf2f8;
    padding: 12px;
    font-size: 13px;
}

.table tbody tr:hover {
    background: #fafcff;
}

.badge-poin {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 7px;
    background: var(--blue-soft);
    color: var(--blue);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.detail-pelanggaran {
    min-width: 260px;
    line-height: 1.6;
}

.detail-item {
    padding: 9px 0;
    border-bottom: 1px solid #edf2f8;
}

.detail-item:first-child {
    padding-top: 0;
}

.detail-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.detail-nama {
    color: var(--blue-dark);
    font-weight: 600;
}

.detail-meta {
    color: var(--muted);
    font-size: 11px;
}

.detail-keterangan {
    color: #61758d;
    font-size: 12px;
    margin-top: 3px;
    overflow-wrap: anywhere;
}

/* =========================================
   TOMBOL
========================================= */

.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: #2878cf;
    --bs-btn-border-color: #2878cf;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #203e68;
    --bs-btn-hover-border-color: #203e68;
    border-radius: 8px;
    padding: 9px 16px;
    font-size: 13px;
}

.btn-secondary {
    --bs-btn-bg: #eaf3ff;
    --bs-btn-border-color: #dce9f8;
    --bs-btn-color: #426584;
    --bs-btn-hover-bg: #dceaf6;
    --bs-btn-hover-border-color: #cbddeb;
    --bs-btn-hover-color: #234e78;
    border-radius: 8px;
    padding: 9px 15px;
    font-size: 13px;
}

.btn-success {
    border-radius: 8px;
    padding: 9px 16px;
    font-size: 13px;
}

/* =========================================
   DATATABLES
========================================= */

.dt-container .dt-layout-row {
    margin: 12px 0;
}

.dt-container .dt-layout-row:first-child {
    margin-top: 22px;
    margin-bottom: 8px;
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
    font-size: 13px;
    white-space: nowrap;
}

.dt-container .dt-length select {
    width: 55px !important;
    min-width: 55px;
    height: 38px;
    padding: 6px 5px;
    border: 1px solid #d7e3ef;
    border-radius: 7px;
    background: #fff;
    color: #344b63;
}

.dt-container .dt-search input {
    width: 190px;
    height: 38px;
    padding: 7px 12px;
    border: 1px solid #d7e3ef;
    border-radius: 7px;
    margin-left: 0 !important;
}

.dt-container .dt-length select:focus,
.dt-container .dt-search input:focus {
    outline: none;
    border-color: #8eb5d8;
    box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
}

.alert {
    border-radius: 9px;
    font-size: 13px;
}

/* =========================================
   RESPONSIVE
========================================= */

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
}

@media (max-width: 576px) {
    .card-body {
        padding: 17px;
    }

    .summary-number {
        font-size: 23px;
    }

    .dt-container .dt-search input {
        width: 140px;
    }
}

/* =========================================
   CETAK
========================================= */

@media print {

    body {
        background: #fff !important;
    }

    .sidebar,
    .no-print,
    .dt-container .dt-layout-row,
    .btn {
        display: none !important;
    }

    .main {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .card-body {
        padding: 10px 0;
    }

    .summary-card {
        border: 1px solid #ddd !important;
    }

    .table thead th,
    .table tbody td {
        color: #000 !important;
        background: #fff !important;
        font-size: 10px;
        border: 1px solid #bbb !important;
    }

    .detail-nama,
    .detail-meta,
    .detail-keterangan {
        color: #000 !important;
    }

    .detail-item {
        border-color: #ddd !important;
    }

    .badge-poin {
        color: #000 !important;
        background: #fff !important;
        border: 1px solid #aaa;
    }

    .card {
        break-inside: avoid;
    }
}
</style>

</head>

<body>

<!-- =========================================
     SIDEBAR WALI KELAS
========================================= -->

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

        <a href="../dashboard.php" class="menu-link">
            Dashboard
        </a>

        <a href="data_siswa.php" class="menu-link">
            Data Siswa
        </a>

        <a href="catat_pelanggaran.php" class="menu-link">
            Catat Pelanggaran
        </a>

        <a
            href="rekap_pelanggaran.php"
            class="menu-link active"
        >
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php" class="menu-link">
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

<!-- =========================================
     KONTEN UTAMA
========================================= -->

<main class="main">

<div class="container">

    <!-- TOMBOL KEMBALI -->

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 no-print">

        <a href="../dashboard.php" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>

    </div>

    <!-- JUDUL -->

    <div class="card mb-4">

        <div class="card-body">

            <h2 class="judul mb-1">
                Rekap Pelanggaran
            </h2>

            <p class="text-muted mb-0">
                Rekap seluruh pelanggaran siswa pada kelas yang menjadi tanggung jawab Anda.
            </p>

            <p class="keterangan mt-2 mb-0">
                Tahun Ajaran: <strong><?= e($nama_tahun_ajaran); ?></strong>
            </p>

        </div>

    </div>

    <!-- PESAN -->

    <?php if ($pesan !== ''): ?>

        <div class="alert alert-<?= e($jenis_pesan); ?> mb-4">
            <?= e($pesan); ?>
        </div>

    <?php endif; ?>

    <!-- KELAS TANGGUNG JAWAB -->

    <div class="card mb-4">

        <div class="card-body">

            <h4 class="mb-1">
                Kelas Tanggung Jawab
            </h4>

            <p class="keterangan mb-3">
                Rekap hanya menampilkan pelanggaran siswa dari kelas yang Anda ampu.
            </p>

            <?php if (count($daftar_kelas) > 0): ?>

                <div class="d-flex flex-wrap gap-2">

                    <?php foreach ($daftar_kelas as $kelas): ?>

                        <span class="badge rounded-pill text-bg-light border px-3 py-2">
                            <?= e(
                                labelKelas(
                                    $kelas['nama'],
                                    $kelas['tingkat'],
                                    $kelas['jurusan']
                                )
                            ); ?>
                        </span>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="text-muted mb-0">
                    Belum ada kelas yang ditugaskan kepada Anda pada tahun ajaran aktif.
                </p>

            <?php endif; ?>

        </div>

    </div>

    <!-- RINGKASAN -->

    <div class="row g-3 mb-4">

        <div class="col-md-4">

            <div class="card summary-card">

                <div class="summary-label">
                    Siswa dengan Pelanggaran
                </div>

                <div class="summary-number">
                    <?= number_format($total_siswa, 0, ',', '.'); ?>
                </div>

                <div class="summary-note">
                    Jumlah siswa yang tercatat melakukan pelanggaran
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card summary-card">

                <div class="summary-label">
                    Jumlah Pelanggaran
                </div>

                <div class="summary-number">
                    <?= number_format($total_pelanggaran, 0, ',', '.'); ?>
                </div>

                <div class="summary-note">
                    Total seluruh pelanggaran pada kelas tanggung jawab
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card summary-card">

                <div class="summary-label">
                    Total Poin Pelanggaran
                </div>

                <div class="summary-number">
                    <?= number_format($total_poin, 0, ',', '.'); ?>
                </div>

                <div class="summary-note">
                    Akumulasi poin seluruh pelanggaran siswa
                </div>

            </div>

        </div>

    </div>

    <!-- TABEL REKAP -->

    <div class="card">

        <div class="card-body">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">

                <div>
                    <h4 class="mb-1">
                        Data Rekap Siswa
                    </h4>

                    <p class="keterangan mb-0">
                        Rincian pelanggaran seluruh siswa di kelas tanggung jawab Anda.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2 no-print">

                    <a
                        href="rekap_pelanggaran.php?export=csv"
                        class="btn btn-success"
                    >
                        Ekspor CSV
                    </a>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="window.print()"
                    >
                        Cetak Rekap
                    </button>

                </div>

            </div>

            <div class="table-responsive">

                <table
                    id="tabelRekap"
                    class="table table-bordered table-hover align-middle w-100"
                >

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jumlah Pelanggaran</th>
                            <th>Total Poin</th>
                            <th>Rincian Pelanggaran</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($daftar_rekap as $i => $data): ?>

                        <tr>

                            <td>
                                <?= $i + 1; ?>
                            </td>

                            <td>
                                <?= e($data['nis']); ?>
                            </td>

                            <td>
                                <?= e($data['nama_siswa']); ?>
                            </td>

                            <td>
                                <?= e($data['nama_kelas']); ?>
                            </td>

                            <td class="text-center">
                                <?= (int)$data['jumlah']; ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-poin">
                                    <?= (int)$data['total_poin']; ?> poin
                                </span>
                            </td>

                            <td>

                                <div class="detail-pelanggaran">

                                    <?php foreach ($data['detail'] as $detail): ?>

                                        <div class="detail-item">

                                            <div class="detail-nama">
                                                <?= e($detail['nama_pelanggaran']); ?>
                                            </div>

                                            <div class="detail-meta">
                                                Tanggal:
                                                <?= e($detail['tanggal']); ?>

                                                &nbsp;|&nbsp;

                                                Poin:
                                                <?= (int)$detail['poin']; ?>

                                            </div>

                                            <?php if (trim((string)$detail['keterangan']) !== ''): ?>

                                                <div class="detail-keterangan">
                                                    Keterangan:
                                                    <?= e($detail['keterangan']); ?>
                                                </div>

                                            <?php endif; ?>

                                            <div class="detail-meta">
                                                Dicatat oleh:
                                                <?= e($detail['nama_guru'] ?: '-'); ?>
                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php if (count($daftar_rekap) === 0): ?>

                <div class="text-center text-muted py-4">
                    Belum ada data pelanggaran untuk kelas tanggung jawab Anda.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

$(document).ready(function() {

    const jumlahData = <?= count($daftar_rekap); ?>;

    if (jumlahData > 0) {

        $('#tabelRekap').DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [],

            scrollX: true,

            language: {

                search: "Cari:",

                searchPlaceholder: "Cari siswa...",

                lengthMenu: "Tampilkan _MENU_ data",

                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",

                infoEmpty: "Tidak ada data rekap",

                infoFiltered: "(disaring dari _MAX_ data)",

                zeroRecords: "Data tidak ditemukan",

                emptyTable: "Belum ada data pelanggaran",

                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }

            }

        });

    }

});

</script>

</body>
</html>