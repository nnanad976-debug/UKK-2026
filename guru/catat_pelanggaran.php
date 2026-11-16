
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

$pesan = '';
$jenis_pesan = '';
$user_id = (int)($_SESSION['user_id'] ?? 0);

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function labelKelas($nama, $tingkat, $jurusan) {
    $nama = trim((string)$nama);
    $tingkat = trim((string)$tingkat);
    $jurusan = trim((string)$jurusan);

    if ($nama !== '' && stripos($nama, $tingkat) !== false && stripos($nama, $jurusan) !== false) {
        return $nama;
    }

    if ($nama !== '' && ctype_digit($nama)) {
        return trim("$tingkat $jurusan $nama");
    }

    if ($nama === '' || strcasecmp($nama, $jurusan) === 0) {
        return trim("$tingkat $jurusan");
    }

    return trim("$tingkat $jurusan $nama");
}

/* =========================================
   DATA GURU
========================================= */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id = ?
     LIMIT 1"
);

if (!$stmt) {
    exit("Gagal menyiapkan data guru.");
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

mysqli_stmt_close($stmt);

$guru_id = (int)($data_guru['id'] ?? 0);
$nama_guru = $data_guru['nama'] ?? ($_SESSION['nama'] ?? 'Guru');

if ($guru_id <= 0) {
    $pesan = 'Data guru tidak ditemukan. Hubungi administrator.';
    $jenis_pesan = 'danger';
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$tahun_ajaran_id = 0;
$tahun_ajaran_label = '';

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

$data_tahun = $query_tahun ? mysqli_fetch_assoc($query_tahun) : null;

if ($data_tahun) {
    $tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

    $kolom_tahun = [
        'nama_tahun_ajaran',
        'tahun_ajaran',
        'nama',
        'tahun'
    ];

    foreach ($kolom_tahun as $kolom) {
        if (!empty($data_tahun[$kolom])) {
            $tahun_ajaran_label = $data_tahun[$kolom];
            break;
        }
    }

    if ($tahun_ajaran_label === '') {
        $mulai = $data_tahun['tahun_mulai'] ?? '';
        $selesai = $data_tahun['tahun_selesai'] ?? '';

        if ($mulai !== '' && $selesai !== '') {
            $tahun_ajaran_label = $mulai . '/' . $selesai;
        } else {
            $tahun_ajaran_label = 'Tahun Ajaran Aktif';
        }
    }
}

/* =========================================
   DAFTAR SISWA DAN PENEMPATAN KELAS
========================================= */

$daftar_siswa = [];

if ($tahun_ajaran_id > 0) {
    $stmt_siswa = mysqli_prepare(
        $koneksi,
        "SELECT
            s.id,
            s.nis,
            s.nisn,
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
         INNER JOIN t_kelas k
            ON k.id = ks.kelas_id
         WHERE s.status_aktif = 1
           AND ks.status_aktif = 1
           AND ks.tahun_ajaran_id = ?
           AND k.status_aktif = 1
         ORDER BY s.nama ASC"
    );

    if ($stmt_siswa) {
        mysqli_stmt_bind_param($stmt_siswa, "i", $tahun_ajaran_id);
        mysqli_stmt_execute($stmt_siswa);

        $hasil_siswa = mysqli_stmt_get_result($stmt_siswa);

        while ($siswa = mysqli_fetch_assoc($hasil_siswa)) {
            $siswa['label_kelas'] = labelKelas(
                $siswa['nama_kelas'],
                $siswa['tingkat'],
                $siswa['jurusan']
            );

            $daftar_siswa[] = $siswa;
        }

        mysqli_stmt_close($stmt_siswa);
    }
}

/* =========================================
   JENIS PELANGGARAN
========================================= */

$daftar_jenis = [];

$query_jenis = mysqli_query(
    $koneksi,
    "SELECT id, kode, nama, poin, tingkat, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY id ASC"
);

if ($query_jenis) {
    while ($p = mysqli_fetch_assoc($query_jenis)) {
        $daftar_jenis[] = $p;
    }
}

/* =========================================
   PROSES SIMPAN PELANGGARAN
========================================= */

if (isset($_POST['simpan'])) {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $tanggal_obj = DateTime::createFromFormat('!Y-m-d', $tanggal);

    $tanggal_valid = $tanggal_obj !== false &&
        $tanggal_obj->format('Y-m-d') === $tanggal;

    if ($guru_id <= 0) {
        $pesan = 'Data akun guru tidak ditemukan.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id <= 0) {
        $pesan = 'Tahun ajaran aktif belum tersedia.';
        $jenis_pesan = 'danger';

    } elseif (
        $siswa_id <= 0 ||
        $pelanggaran_id <= 0 ||
        !$tanggal_valid ||
        $keterangan === ''
    ) {
        $pesan = 'Lengkapi semua data sebelum menyimpan pelanggaran.';
        $jenis_pesan = 'danger';

    } else {

        /* AMBIL KELAS SISWA DARI DATABASE */
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                s.id,
                s.nama,
                ks.kelas_id,
                k.nama AS nama_kelas,
                k.tingkat,
                k.jurusan
             FROM t_siswa s
             INNER JOIN t_kelas_siswa ks
                ON ks.siswa_id = s.id
             INNER JOIN t_kelas k
                ON k.id = ks.kelas_id
             WHERE s.id = ?
               AND s.status_aktif = 1
               AND ks.status_aktif = 1
               AND ks.tahun_ajaran_id = ?
               AND k.status_aktif = 1
             LIMIT 1"
        );

        if (!$stmt) {
            $pesan = 'Gagal memeriksa data siswa.';
            $jenis_pesan = 'danger';

        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $siswa_id,
                $tahun_ajaran_id
            );

            mysqli_stmt_execute($stmt);

            $data_siswa = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt)
            );

            mysqli_stmt_close($stmt);

            if (!$data_siswa) {
                $pesan = 'Siswa tidak ditemukan pada tahun ajaran aktif.';
                $jenis_pesan = 'danger';

            } else {

                /* VALIDASI JENIS PELANGGARAN */
                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id, nama, poin, pelanggaran_kategori_id
                     FROM t_pelanggaran
                     WHERE id = ?
                       AND status_aktif = 1
                     LIMIT 1"
                );

                $data_pelanggaran = null;

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
                    mysqli_stmt_execute($stmt);

                    $data_pelanggaran = mysqli_fetch_assoc(
                        mysqli_stmt_get_result($stmt)
                    );

                    mysqli_stmt_close($stmt);
                }

                if (!$data_pelanggaran) {
                    $pesan = 'Jenis pelanggaran tidak ditemukan atau tidak aktif.';
                    $jenis_pesan = 'danger';

                } else {

                    $nama_siswa = $data_siswa['nama'];
                    $kelas_id = (int)$data_siswa['kelas_id'];

                    $nama_kelas = labelKelas(
                        $data_siswa['nama_kelas'],
                        $data_siswa['tingkat'],
                        $data_siswa['jurusan']
                    );

                    $nama_pelanggaran = $data_pelanggaran['nama'];
                    $kategori_id = (int)($data_pelanggaran['pelanggaran_kategori_id'] ?? 0);
                    $poin = (int)$data_pelanggaran['poin'];

                    $nama_guru_simpan = $nama_guru;
                    $tindakan = '';
                    $status = 'Diproses';

                    /* INSERT KE TABEL PELANGGARAN SISWA */
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_pelanggaran_siswa (
                            tahun_ajaran_id,
                            siswa_id,
                            nama_siswa,
                            pelanggaran_id,
                            nama_pelanggaran,
                            pelanggaran_kategori_id,
                            guru_id,
                            nama_guru,
                            tanggal,
                            keterangan,
                            poin,
                            tindakan,
                            status,
                            kelas_id,
                            nama_kelas
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );

                    if ($stmt) {
                        mysqli_stmt_bind_param(
                            $stmt,
                            "iisisiisssissis",
                            $tahun_ajaran_id,
                            $siswa_id,
                            $nama_siswa,
                            $pelanggaran_id,
                            $nama_pelanggaran,
                            $kategori_id,
                            $guru_id,
                            $nama_guru_simpan,
                            $tanggal,
                            $keterangan,
                            $poin,
                            $tindakan,
                            $status,
                            $kelas_id,
                            $nama_kelas
                        );

                        $berhasil_simpan = mysqli_stmt_execute($stmt);

                        mysqli_stmt_close($stmt);

                        if ($berhasil_simpan) {
                            header("Location: catat_pelanggaran.php?berhasil=1");
                            exit;
                        }
                    }

                    $pesan = 'Gagal menyimpan pelanggaran. Periksa struktur tabel database.';
                    $jenis_pesan = 'danger';
                }
            }
        }
    }
}

/* =========================================
   NOTIFIKASI
========================================= */

if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Pelanggaran berhasil dicatat!';
    $jenis_pesan = 'success';
}

/* =========================================
   RIWAYAT PELANGGARAN GURU
========================================= */

$daftar_riwayat = [];

$stmt_riwayat = mysqli_prepare(
    $koneksi,
    "SELECT
        ps.*,
        k.nama AS nama_kelas_asli,
        k.tingkat,
        k.jurusan
     FROM t_pelanggaran_siswa ps
     LEFT JOIN t_kelas k ON k.id = ps.kelas_id
     WHERE ps.guru_id = ?
     ORDER BY ps.tanggal DESC, ps.id DESC"
);

if ($stmt_riwayat) {
    mysqli_stmt_bind_param($stmt_riwayat, "i", $guru_id);
    mysqli_stmt_execute($stmt_riwayat);

    $hasil_riwayat = mysqli_stmt_get_result($stmt_riwayat);

    while ($data = mysqli_fetch_assoc($hasil_riwayat)) {
        $daftar_riwayat[] = $data;
    }

    mysqli_stmt_close($stmt_riwayat);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Catat Pelanggaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --page: #f3f7fc;
    --text: #263f63;
    --muted: #7890ad;
    --line: #dce9f8;
}

* {
    box-sizing: border-box;
}

html {
    min-height: 100%;
    overflow-y: scroll;
    scrollbar-gutter: stable;
}

body {
    width: 100%;
    min-height: 100vh;
    margin: 0;
    overflow-x: hidden;
    background: var(--page);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 15px;
}

a {
    text-decoration: none;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    width: 282px;
    height: 100vh;
    padding: 25px 14px 16px;
    overflow-y: auto;
    background: #fff;
    border-right: 1px solid var(--line);
}

.brand {
    padding: 0 14px 20px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--line);
}

.brand-title {
    margin-bottom: 5px;
    color: var(--blue-dark);
    font-size: 19px;
    font-weight: 700;
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
    transition: .15s ease;
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

/* KONTEN */
.main {
    width: calc(100% - 282px);
    min-height: 100vh;
    margin-left: 282px;
    padding: 24px 28px 40px;
}

.page-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
}

.judul {
    margin: 0;
    color: #234e78;
    font-size: 30px;
    font-weight: 650;
}

.subjudul {
    color: var(--muted);
    font-size: 14px;
}

.section-title {
    margin: 0 0 5px;
    color: #234e78;
    font-size: 21px;
    font-weight: 650;
}

.section-description {
    margin-bottom: 22px;
    color: var(--muted);
    font-size: 13px;
}

/* CARD */
.card {
    overflow: hidden;
    border: 1px solid var(--line) !important;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(35, 78, 120, .045) !important;
}

.card-body {
    min-width: 0;
    padding: 22px;
}

/* FORM */
.form-label {
    display: block;
    margin-bottom: 7px;
    color: #405c7b;
    font-size: 13px;
    font-weight: 600;
}

.form-control,
.form-select {
    width: 100%;
    min-height: 42px;
    padding: 9px 12px;
    border: 1px solid #d7e3ef;
    border-radius: 7px;
    background-color: #fff;
    color: #344b63;
    font-family: inherit;
    font-size: 14px;
}

.form-control:focus,
.form-select:focus {
    border-color: #8eb5d8;
    box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
}

.form-control[readonly] {
    background: #f1f5fa;
    color: #526b85;
    font-weight: 600;
}

textarea.form-control {
    min-height: 110px;
    resize: vertical;
}

.form-text {
    margin-top: 6px;
    color: var(--muted);
    font-size: 12px;
}

/* SELECT2 */
.select2-container {
    width: 100% !important;
    font-family: inherit;
    font-size: 14px;
}

.select2-container--default .select2-selection--single {
    display: flex;
    align-items: center;
    height: 42px;
    border: 1px solid #d7e3ef;
    border-radius: 7px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 12px;
    color: #344b63;
    line-height: 40px;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #8799ad;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 7px;
}

.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #8eb5d8;
    box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
}

.select2-dropdown {
    z-index: 2000;
    overflow: hidden;
    border: 1px solid #d7e3ef;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(35, 78, 120, .10);
}

.select2-container--default .select2-search--dropdown {
    padding: 9px;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    height: 38px;
    padding: 7px 10px;
    border: 1px solid #d7e3ef;
    border-radius: 6px;
    outline: none;
    font-family: inherit;
}

.select2-container--default .select2-results__option {
    padding: 9px 12px;
    color: #405c7b;
    font-size: 13px;
}

.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background: var(--blue);
    color: #fff;
}

.select2-container--default .select2-results__option--selected {
    background: var(--blue-soft);
    color: var(--blue);
}

/* TOMBOL */
.btn {
    padding: 8px 15px;
    border-radius: 7px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
}

.btn-primary {
    --bs-btn-bg: #2878cf;
    --bs-btn-border-color: #2878cf;
    --bs-btn-hover-bg: #2168b4;
    --bs-btn-hover-border-color: #2168b4;
}

/* TABEL */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    border-radius: 8px;
}

#tabelRiwayat {
    width: 100% !important;
    min-width: 1100px;
    margin-bottom: 0 !important;
    color: #344b63;
    font-size: 14px;
    vertical-align: middle;
}

#tabelRiwayat thead th {
    padding: 14px 12px;
    background: #edf4fb;
    color: #315b82;
    border-color: #dce7f1;
    font-weight: 600;
    white-space: nowrap;
}

#tabelRiwayat tbody td {
    padding: 13px 11px;
    border-color: #e2eaf3;
    vertical-align: middle;
    line-height: 1.45;
}

#tabelRiwayat tbody tr:hover > * {
    background: #f5f9fe;
}

.status-badge {
    display: inline-block;
    padding: 7px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-diproses {
    background: #fff2d8;
    color: #966b16;
}

.status-selesai {
    background: #e4f3e9;
    color: #34734b;
}

.status-lain {
    background: #eaf0f7;
    color: #526b85;
}

/* DATATABLES */
.dt-container {
    width: 100%;
    color: var(--text);
    font-family: inherit;
    font-size: 13px;
}

.dt-container .dt-layout-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 15px 0;
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
    color: #526b86;
    font-size: 13px;
    white-space: nowrap;
}

.dt-container .dt-length select,
.dt-container .dt-search input {
    height: 38px;
    border: 1px solid #d7e3ef;
    border-radius: 7px;
    background: #fff;
    color: var(--text);
    font-family: inherit;
    font-size: 13px;
    outline: none;
}

.dt-container .dt-length select {
    width: 70px;
    padding: 5px 8px;
}

.dt-container .dt-search input {
    width: 205px;
    padding: 7px 10px;
}

.dt-container .dt-paging .dt-paging-button {
    border-radius: 5px !important;
    font-size: 13px;
}

.dt-container .dt-paging .dt-paging-button.current {
    background: var(--blue) !important;
    border-color: var(--blue) !important;
    color: #fff !important;
}

.dt-container .dt-info {
    color: #526b86;
    font-size: 13px;
}

.alert {
    border-radius: 9px;
    font-size: 13px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .logout {
        margin-top: 14px;
    }

    .main {
        width: 100%;
        margin-left: 0;
        padding: 20px 18px 30px;
    }
}

@media (max-width: 650px) {
    .main {
        padding: 16px 12px 25px;
    }

    .card-body {
        padding: 16px;
    }

    .judul {
        font-size: 25px;
    }

    .dt-container .dt-layout-row {
        flex-wrap: wrap;
    }

    .dt-container .dt-layout-cell {
        width: 100%;
    }

    .dt-container .dt-search {
        justify-content: flex-start;
    }

    .dt-container .dt-search input {
        width: 150px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="catat_pelanggaran.php" class="menu-link active">Catat Pelanggaran</a>
        <a href="tindakan.php" class="menu-link">Tindakan</a>
        <a href="riwayat.php" class="menu-link">Riwayat</a>
        <a href="rekap_poin.php" class="menu-link">Rekap Poin</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">
    <div class="page-container">

        <section class="card mb-4">
            <div class="card-body">
                <h1 class="judul mb-1">Catat Pelanggaran</h1>
                <p class="subjudul mb-0">
                    Catat dan kelola pelanggaran siswa.
                </p>
            </div>
        </section>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-<?= e($jenis_pesan); ?> mb-4" role="alert">
                <?= e($pesan); ?>
            </div>
        <?php endif; ?>

        <?php if ($tahun_ajaran_id <= 0): ?>
            <div class="alert alert-warning mb-4">
                Tahun ajaran aktif belum ditemukan. Periksa menu Tahun Ajaran.
            </div>
        <?php endif; ?>

        <section class="card mb-4">
            <div class="card-body">

                <h2 class="section-title">Tambah Pelanggaran</h2>
                <p class="section-description">
                    Cari nama siswa terlebih dahulu. Kelas dan tahun ajaran akan terisi otomatis.
                </p>

                <form method="POST" action="catat_pelanggaran.php">

                    <div class="mb-3">
                        <label for="nama_guru" class="form-label">
                            Guru yang Mencatat
                        </label>

                        <input
                            type="text"
                            id="nama_guru"
                            class="form-control"
                            value="<?= e($nama_guru); ?>"
                            readonly
                        >

                        <div class="form-text">
                            Nama guru diambil otomatis dari akun yang sedang login.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="siswa_id" class="form-label">
                            Cari Nama Siswa
                        </label>

                        <select
                            id="siswa_id"
                            name="siswa_id"
                            class="form-select"
                            required
                        >
                            <option value="">-- Ketik nama atau NIS siswa --</option>

                            <?php foreach ($daftar_siswa as $s): ?>
                                <option
                                    value="<?= (int)$s['id']; ?>"
                                    data-kelas="<?= e($s['label_kelas']); ?>"
                                    data-tahun="<?= e($tahun_ajaran_label); ?>"
                                    <?= (int)($_POST['siswa_id'] ?? 0) === (int)$s['id'] ? 'selected' : ''; ?>
                                >
                                    <?= e($s['nama']); ?>
                                    (NIS: <?= e($s['nis']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="form-text">
                            Ketik nama atau NIS, kemudian pilih siswa yang sesuai.
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="kelas_otomatis" class="form-label">
                                Kelas
                            </label>

                            <input
                                type="text"
                                id="kelas_otomatis"
                                class="form-control"
                                placeholder="Kelas akan muncul otomatis"
                                readonly
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="tahun_otomatis" class="form-label">
                                Tahun Ajaran
                            </label>

                            <input
                                type="text"
                                id="tahun_otomatis"
                                class="form-control"
                                placeholder="Tahun ajaran akan muncul otomatis"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pelanggaran_id" class="form-label">
                            Jenis Pelanggaran
                        </label>

                        <select
                            id="pelanggaran_id"
                            name="pelanggaran_id"
                            class="form-select"
                            required
                        >
                            <option value="">-- Cari atau Pilih Pelanggaran --</option>

                            <?php foreach ($daftar_jenis as $p): ?>
                                <option
                                    value="<?= (int)$p['id']; ?>"
                                    <?= (int)($_POST['pelanggaran_id'] ?? 0) === (int)$p['id'] ? 'selected' : ''; ?>
                                >
                                    <?= e($p['kode'] . ' - ' . $p['nama']); ?>
                                    (<?= e($p['tingkat']); ?>, <?= (int)$p['poin']; ?> poin)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="tanggal"
                            name="tanggal"
                            class="form-control"
                            value="<?= e($_POST['tanggal'] ?? date('Y-m-d')); ?>"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            id="keterangan"
                            name="keterangan"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan keterangan pelanggaran"
                            required
                        ><?= e($_POST['keterangan'] ?? ''); ?></textarea>
                    </div>

                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-primary"
                        <?= ($tahun_ajaran_id <= 0 || empty($daftar_siswa) || $guru_id <= 0) ? 'disabled' : ''; ?>
                    >
                        Simpan Pelanggaran
                    </button>

                </form>
            </div>
        </section>

        <section class="card">
            <div class="card-body">

                <h2 class="section-title">Riwayat Pelanggaran</h2>
                <p class="section-description">
                    Daftar pelanggaran yang telah dicatat oleh akun guru ini.
                </p>

                <?php if (!empty($daftar_riwayat)): ?>

                    <div class="table-responsive">
                        <table id="tabelRiwayat" class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Guru yang Mencatat</th>
                                    <th>Keterangan</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($daftar_riwayat as $i => $data): ?>
                                    <?php
                                    $kelas_tampil = $data['nama_kelas'] ?? '-';

                                    if (!empty($data['nama_kelas_asli'])) {
                                        $kelas_tampil = labelKelas(
                                            $data['nama_kelas_asli'],
                                            $data['tingkat'],
                                            $data['jurusan']
                                        );
                                    }

                                    $status = trim((string)($data['status'] ?? 'Diproses'));
                                    $status_class = 'status-lain';

                                    if (strcasecmp($status, 'Diproses') === 0) {
                                        $status_class = 'status-diproses';
                                    } elseif (
                                        strcasecmp($status, 'Selesai') === 0 ||
                                        strcasecmp($status, 'Ditindaklanjuti') === 0
                                    ) {
                                        $status_class = 'status-selesai';
                                    }
                                    ?>

                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td><?= e($data['tanggal']); ?></td>
                                        <td><?= e($data['nama_siswa']); ?></td>
                                        <td><?= e($kelas_tampil); ?></td>
                                        <td><?= e($data['nama_pelanggaran']); ?></td>
                                        <td><?= e($data['nama_guru'] ?? '-'); ?></td>
                                        <td><?= e($data['keterangan']); ?></td>
                                        <td><?= (int)$data['poin']; ?></td>
                                        <td>
                                            <span class="status-badge <?= e($status_class); ?>">
                                                <?= e($status); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>

                    <div class="text-center py-5">
                        <h5 class="mb-2" style="color:#234e78;">
                            Belum Ada Riwayat Pelanggaran
                        </h5>

                        <p class="text-muted mb-0" style="font-size:13px;">
                            Pelanggaran yang dicatat akan tampil pada tabel ini.
                        </p>
                    </div>

                <?php endif; ?>

            </div>
        </section>

    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {

    const bahasaSelect2 = {
        noResults: function() {
            return 'Data tidak ditemukan';
        },
        searching: function() {
            return 'Sedang mencari...';
        },
        inputTooShort: function() {
            return 'Ketik untuk mencari data';
        }
    };

    /* PENCARIAN NAMA SISWA */
    $('#siswa_id').select2({
        width: '100%',
        placeholder: '-- Ketik nama atau NIS siswa --',
        allowClear: true,
        language: bahasaSelect2
    });

    /* PENCARIAN JENIS PELANGGARAN */
    $('#pelanggaran_id').select2({
        width: '100%',
        placeholder: '-- Cari Jenis Pelanggaran --',
        allowClear: true,
        language: bahasaSelect2
    });

    /* ELEMEN KELAS DAN TAHUN AJARAN */
    const kelasOtomatis = document.getElementById('kelas_otomatis');
    const tahunOtomatis = document.getElementById('tahun_otomatis');

    /* ISI KELAS DAN TAHUN AJARAN OTOMATIS */
    function tampilkanDataSiswa() {
        const siswaTerpilih = document.querySelector(
            '#siswa_id option:checked'
        );

        if (!siswaTerpilih || siswaTerpilih.value === '') {
            kelasOtomatis.value = '';
            tahunOtomatis.value = '';
            return;
        }

        kelasOtomatis.value = siswaTerpilih.dataset.kelas || '';
        tahunOtomatis.value = siswaTerpilih.dataset.tahun || '';
    }

    /* SAAT NAMA SISWA DIPILIH */
    $('#siswa_id').on('change', function() {
        tampilkanDataSiswa();
    });

    /* TAMPILKAN KEMBALI DATA JIKA FORM GAGAL DIKIRIM */
    tampilkanDataSiswa();

    /* DATATABLES */
    const tabel = $('#tabelRiwayat');

    if (tabel.length) {
        tabel.DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            autoWidth: false,
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari riwayat...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat',
                infoEmpty: 'Tidak ada riwayat pelanggaran',
                infoFiltered: '(disaring dari _MAX_ data)',
                zeroRecords: 'Riwayat tidak ditemukan',
                emptyTable: 'Belum ada riwayat pelanggaran',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }
            }
        });
    }

});
</script>

</body>
</html>
