
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

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function labelKelas($nama, $tingkat, $jurusan) {
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

    if ($nama === '' || strcasecmp($nama, $jurusan) === 0) {
        return trim("$tingkat $jurusan");
    }

    return trim("$tingkat $jurusan $nama");
}

/* =========================================
   DATA GURU / WALI KELAS
========================================= */

$data_guru = null;

if ($guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama FROM t_guru WHERE id = ? LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $guru_id);
        mysqli_stmt_execute($stmt);
        $data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }
}

if (!$data_guru && $user_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }
}

$guru_id = (int)($data_guru['id'] ?? 0);
$nama_guru = $data_guru['nama'] ?? ($_SESSION['nama'] ?? 'Wali Kelas');

if ($guru_id <= 0) {
    $pesan = 'Data guru tidak ditemukan. Silakan hubungi administrator.';
    $jenis_pesan = 'danger';
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

$data_tahun = $query_tahun ? mysqli_fetch_assoc($query_tahun) : null;
$tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

/* =========================================
   PROSES SIMPAN TINDAKAN
========================================= */

if (isset($_POST['simpan_tindakan'])) {

    $pelanggaran_siswa_id = (int)($_POST['pelanggaran_siswa_id'] ?? 0);
    $tindakan_baru = trim($_POST['tindakan_baru'] ?? '');
    $status_baru = trim($_POST['status_baru'] ?? '');

    $status_diizinkan = [
        'Diproses',
        'Ditindaklanjuti',
        'Selesai'
    ];

    if ($guru_id <= 0 || $tahun_ajaran_id <= 0) {

        $pesan = 'Data akun atau tahun ajaran tidak valid.';
        $jenis_pesan = 'danger';

    } elseif (
        $pelanggaran_siswa_id <= 0 ||
        $tindakan_baru === '' ||
        !in_array($status_baru, $status_diizinkan, true)
    ) {

        $pesan = 'Lengkapi tindakan dan pilih status yang tersedia.';
        $jenis_pesan = 'danger';

    } else {

        /*
         * Hanya catatan milik Wali Kelas yang sedang login
         * dan belum berstatus Selesai yang boleh diubah.
         */

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_siswa
             SET tindakan = ?, status = ?
             WHERE id = ?
               AND guru_id = ?
               AND tahun_ajaran_id = ?
               AND COALESCE(status, '') <> 'Selesai'"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssiii",
                $tindakan_baru,
                $status_baru,
                $pelanggaran_siswa_id,
                $guru_id,
                $tahun_ajaran_id
            );

            $berhasil = mysqli_stmt_execute($stmt);
            $jumlah_diubah = mysqli_stmt_affected_rows($stmt);

            mysqli_stmt_close($stmt);

            if ($berhasil && $jumlah_diubah > 0) {
                header("Location: catat_pelanggaran.php?tindakan_berhasil=1");
                exit;
            } else {
                $pesan = 'Catatan tidak dapat diperbarui. Pastikan catatan masih bisa diubah.';
                $jenis_pesan = 'warning';
            }

        } else {
            $pesan = 'Terjadi kesalahan saat menyiapkan perubahan tindakan.';
            $jenis_pesan = 'danger';
        }
    }
}

/* =========================================
   VALIDASI TAHUN AJARAN DARI DATA SISWA
========================================= */

$tahun_get = (int)($_GET['tahun_ajaran_id'] ?? 0);

if ($tahun_get > 0 && $tahun_get !== $tahun_ajaran_id) {
    $pesan = 'Tahun ajaran pada data siswa sudah tidak aktif. Silakan pilih data siswa kembali.';
    $jenis_pesan = 'warning';
}

/* =========================================
   SELURUH KELAS AKTIF
========================================= */

$daftar_kelas = [];

if ($tahun_ajaran_id > 0) {
    $query_kelas = mysqli_query(
        $koneksi,
        "SELECT id, nama, tingkat, jurusan
         FROM t_kelas
         WHERE status_aktif = 1
         ORDER BY FIELD(tingkat, 'X', 'XI', 'XII'), jurusan ASC, nama ASC"
    );

    if ($query_kelas) {
        while ($row = mysqli_fetch_assoc($query_kelas)) {
            $daftar_kelas[] = $row;
        }
    }
}

/* =========================================
   SELURUH SISWA AKTIF
========================================= */

$daftar_siswa = [];

if ($tahun_ajaran_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT DISTINCT
            s.id,
            s.nis,
            s.nama,
            ks.kelas_id
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
         INNER JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE ks.tahun_ajaran_id = ?
           AND ks.status_aktif = 1
           AND ks.tanggal_mulai <= CURDATE()
           AND (
                ks.tanggal_selesai IS NULL
                OR ks.tanggal_selesai >= CURDATE()
           )
           AND s.status_aktif = 1
           AND k.status_aktif = 1
         ORDER BY s.nama ASC, ks.kelas_id ASC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $daftar_siswa[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}

/* =========================================
   JENIS PELANGGARAN AKTIF
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
    while ($row = mysqli_fetch_assoc($query_jenis)) {
        $daftar_jenis[] = $row;
    }
}

/* =========================================
   DATA FORM AWAL
========================================= */

$kelas_awal = (int)($_POST['kelas_id'] ?? $_GET['kelas_id'] ?? 0);
$siswa_awal = (int)($_POST['siswa_id'] ?? $_GET['siswa_id'] ?? 0);
$jenis_awal = (int)($_POST['pelanggaran_id'] ?? 0);

/* =========================================
   PROSES SIMPAN PELANGGARAN
========================================= */

if (isset($_POST['simpan'])) {

    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);

    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $tanggal_obj = DateTime::createFromFormat('!Y-m-d', $tanggal);

    $tanggal_valid = (
        $tanggal_obj !== false &&
        $tanggal_obj->format('Y-m-d') === $tanggal
    );

    if ($guru_id <= 0) {

        $pesan = 'Data akun Wali Kelas tidak ditemukan.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id <= 0) {

        $pesan = 'Tahun ajaran aktif belum tersedia.';
        $jenis_pesan = 'danger';

    } elseif (
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $pelanggaran_id <= 0 ||
        !$tanggal_valid ||
        $keterangan === ''
    ) {

        $pesan = 'Lengkapi semua data pelanggaran terlebih dahulu.';
        $jenis_pesan = 'danger';

    } else {

        /* VALIDASI KELAS */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_kelas
             WHERE id = ? AND status_aktif = 1
             LIMIT 1"
        );

        $kelas_valid = false;

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $kelas_id);
            mysqli_stmt_execute($stmt);
            $kelas_valid = (bool)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }

        if (!$kelas_valid) {

            $pesan = 'Kelas tidak ditemukan atau tidak aktif.';
            $jenis_pesan = 'danger';

        } else {

            /* VALIDASI SISWA */

            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT
                    s.id,
                    s.nama,
                    s.nis,
                    ks.kelas_id,
                    k.nama AS nama_kelas,
                    k.tingkat,
                    k.jurusan
                 FROM t_siswa s
                 INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
                 INNER JOIN t_kelas k ON k.id = ks.kelas_id
                 WHERE s.id = ?
                   AND ks.kelas_id = ?
                   AND ks.tahun_ajaran_id = ?
                   AND s.status_aktif = 1
                   AND ks.status_aktif = 1
                   AND ks.tanggal_mulai <= CURDATE()
                   AND (
                        ks.tanggal_selesai IS NULL
                        OR ks.tanggal_selesai >= CURDATE()
                   )
                   AND k.status_aktif = 1
                 ORDER BY ks.id DESC
                 LIMIT 1"
            );

            $data_siswa = null;

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "iii",
                    $siswa_id,
                    $kelas_id,
                    $tahun_ajaran_id
                );

                mysqli_stmt_execute($stmt);
                $data_siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }

            if (!$data_siswa) {

                $pesan = 'Siswa tidak ditemukan pada kelas tersebut atau penempatannya sudah tidak aktif.';
                $jenis_pesan = 'danger';

            } else {

                /* VALIDASI JENIS PELANGGARAN */

                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id, nama, poin, pelanggaran_kategori_id
                     FROM t_pelanggaran
                     WHERE id = ? AND status_aktif = 1
                     LIMIT 1"
                );

                $data_pelanggaran = null;

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
                    mysqli_stmt_execute($stmt);
                    $data_pelanggaran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                    mysqli_stmt_close($stmt);
                }

                if (!$data_pelanggaran) {

                    $pesan = 'Jenis pelanggaran tidak ditemukan atau tidak aktif.';
                    $jenis_pesan = 'danger';

                } else {

                    $nama_siswa = $data_siswa['nama'];
                    $nama_pelanggaran = $data_pelanggaran['nama'];
                    $kategori_id = (int)($data_pelanggaran['pelanggaran_kategori_id'] ?? 0);
                    $poin = (int)$data_pelanggaran['poin'];

                    $nama_kelas = labelKelas(
                        $data_siswa['nama_kelas'],
                        $data_siswa['tingkat'],
                        $data_siswa['jurusan']
                    );

                    $nama_guru_simpan = $nama_guru;

                    // Tindakan akan diisi melalui tombol pada tabel.
                    $tindakan = '';
                    $status = 'Diproses';

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

                        $berhasil = mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        if ($berhasil) {
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
   PESAN BERHASIL
========================================= */

if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Pelanggaran berhasil dicatat! Silakan klik tombol Tindakan pada tabel untuk mengisi tindak lanjut.';
    $jenis_pesan = 'success';
}

if (($_GET['tindakan_berhasil'] ?? '') === '1') {
    $pesan = 'Tindakan dan status pelanggaran berhasil diperbarui!';
    $jenis_pesan = 'success';
}

/* =========================================
   RIWAYAT PENCATATAN WALI KELAS
========================================= */

$daftar_riwayat = [];

if ($guru_id > 0 && $tahun_ajaran_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            ps.*,
            k.nama AS nama_kelas_asli,
            k.tingkat,
            k.jurusan
         FROM t_pelanggaran_siswa ps
         LEFT JOIN t_kelas k ON k.id = ps.kelas_id
         WHERE ps.guru_id = ?
           AND ps.tahun_ajaran_id = ?
         ORDER BY ps.tanggal DESC, ps.id DESC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $guru_id, $tahun_ajaran_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $daftar_riwayat[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Catat Pelanggaran Wali Kelas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
:root {
    --blue:#2878cf;
    --blue-dark:#203e68;
    --blue-soft:#eaf3ff;
    --page:#f6f9fd;
    --white:#fff;
    --text:#263f63;
    --muted:#7890ad;
    --line:#dce9f8;
}

* {
    box-sizing:border-box;
}

body {
    margin:0;
    background:var(--page);
    color:var(--text);
    font-family:"Segoe UI",Arial,sans-serif;
}

a {
    text-decoration:none;
}

/* SIDEBAR */

.sidebar {
    position:fixed;
    top:0;
    left:0;
    width:282px;
    height:100vh;
    padding:25px 14px 16px;
    display:flex;
    flex-direction:column;
    background:var(--white);
    border-right:1px solid var(--line);
    z-index:1000;
    overflow-y:auto;
}

.brand {
    padding:0 14px 20px;
    margin-bottom:22px;
    border-bottom:1px solid var(--line);
}

.brand-title {
    color:var(--blue-dark);
    font-size:19px;
    font-weight:700;
    margin-bottom:5px;
}

.brand-subtitle {
    color:var(--muted);
    font-size:12px;
}

.menu-title {
    margin:0 14px 11px;
    color:#91a5bd;
    font-size:10px;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase;
}

.menu {
    display:flex;
    flex-direction:column;
    gap:5px;
}

.menu-link {
    display:flex;
    align-items:center;
    min-height:42px;
    padding:11px 14px;
    border-radius:9px;
    color:#5e7695;
    font-size:13px;
    transition:.2s;
    text-decoration:none;
}

.menu-link:hover {
    background:#f2f7fd;
    color:var(--blue);
}

.menu-link.active {
    background:var(--blue-soft);
    color:var(--blue);
    font-weight:600;
    box-shadow:inset 3px 0 0 var(--blue);
}

.logout {
    margin-top:auto;
    padding-top:14px;
    border-top:1px solid var(--line);
}

.logout .menu-link {
    color:#d9534f;
}

.logout .menu-link:hover {
    background:#fff0ef;
}

/* KONTEN UTAMA */

.main {
    width:calc(100% - 282px);
    min-height:100vh;
    margin-left:282px;
    padding:24px 28px 40px;
}

.container {
    width:100%;
    max-width:none !important;
    margin:0 auto;
    padding:0 !important;
}

.judul,
h4 {
    color:#234e78;
}

.card {
    border:1px solid #e1eaf4 !important;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(35,78,120,.05) !important;
}

.card-body {
    padding:24px;
}

.form-label {
    color:#405873;
    font-weight:600;
}

.form-control,
.form-select {
    border-color:#d7e3ef;
    border-radius:7px;
    padding:10px 12px;
    color:#344b63;
}

.form-control:focus,
.form-select:focus {
    border-color:#8eb5d8;
    box-shadow:0 0 0 .2rem rgba(87,143,190,.14);
}

textarea.form-control {
    min-height:110px;
    resize:vertical;
}

.form-control[readonly] {
    background:#f1f5fa;
    color:#526b85;
    font-weight:600;
}

.btn-primary {
    --bs-btn-color:#fff;
    --bs-btn-bg:#5f91ba;
    --bs-btn-border-color:#5f91ba;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#4b7fa9;
    --bs-btn-hover-border-color:#4b7fa9;
    border-radius:7px;
    padding:8px 15px;
}

.btn-secondary {
    --bs-btn-bg:#e8f0f8;
    --bs-btn-border-color:#d8e5f1;
    --bs-btn-color:#426584;
    --bs-btn-hover-bg:#dceaf6;
    --bs-btn-hover-border-color:#cbddeb;
    --bs-btn-hover-color:#234e78;
    border-radius:7px;
    padding:8px 15px;
}

.btn-tindakan {
    background:#eaf3ff;
    color:#2878cf;
    border:1px solid #d3e5f8;
    border-radius:7px;
    padding:7px 11px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}

.btn-tindakan:hover {
    background:#dcecff;
    color:#1e62a8;
}

.btn-terkunci {
    display:inline-block;
    background:#e4f3e9;
    color:#34734b;
    border-radius:7px;
    padding:7px 11px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}

.table {
    color:#344b63;
    vertical-align:middle;
    margin-bottom:0;
}

.table thead th {
    background:#eaf2fa;
    color:#315b82;
    border-color:#dce7f1;
    font-weight:600;
    padding:12px;
    white-space:nowrap;
}

.table tbody td {
    border-color:#e5edf5;
    padding:11px 12px;
}

.status-badge {
    display:inline-block;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}

.status-diproses {
    background:#fff3d9;
    color:#946b17;
}

.status-ditindaklanjuti {
    background:#eaf0ff;
    color:#4265a5;
}

.status-selesai {
    background:#e4f3e9;
    color:#34734b;
}

.status-lain {
    background:#eaf0f7;
    color:#526b85;
}

.keterangan {
    color:#71849a;
    font-size:14px;
}

.tindakan-text {
    min-width:150px;
    max-width:260px;
    white-space:normal;
    overflow-wrap:anywhere;
}

/* DATATABLES */

.dt-container .dt-layout-row {
    margin:12px 0;
}

.dt-container .dt-layout-row:first-child {
    margin-top:22px;
    margin-bottom:8px;
}

.dt-container .dt-layout-cell {
    display:flex;
    align-items:center;
}

.dt-container .dt-length,
.dt-container .dt-search {
    display:flex;
    align-items:center;
    gap:8px;
}

.dt-container .dt-length label,
.dt-container .dt-search label {
    display:flex;
    align-items:center;
    gap:8px;
    margin:0;
    font-size:13px;
    white-space:nowrap;
}

.dt-container .dt-length select {
    width:55px !important;
    min-width:55px;
    height:38px;
    padding:6px 5px;
    border:1px solid #d7e3ef;
    border-radius:7px;
    background:#fff;
    color:#344b63;
}

.dt-container .dt-search input {
    width:190px;
    height:38px;
    padding:7px 12px;
    border:1px solid #d7e3ef;
    border-radius:7px;
    margin-left:0 !important;
}

.dt-container .dt-length select:focus,
.dt-container .dt-search input:focus {
    outline:none;
    border-color:#8eb5d8;
    box-shadow:0 0 0 .15rem rgba(87,143,190,.14);
}

.alert {
    border-radius:7px;
}

.modal-content {
    border:1px solid #e1eaf4;
    border-radius:12px;
}

.modal-header {
    border-bottom:1px solid #e5edf5;
}

.modal-footer {
    border-top:1px solid #e5edf5;
}

/* RESPONSIVE */

@media(max-width:900px) {
    .sidebar {
        position:static;
        width:100%;
        height:auto;
        min-height:auto;
    }

    .main {
        width:100%;
        margin-left:0;
        padding:18px 14px 30px;
    }

    .logout {
        margin-top:14px;
    }
}

@media(max-width:576px) {
    .card-body {
        padding:17px;
    }

    .dt-container .dt-search input {
        width:140px;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR WALI KELAS -->

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

        <a href="catat_pelanggaran.php" class="menu-link active">
            Catat Pelanggaran
        </a>

        <a href="data_siswa.php" class="menu-link">
            Data Siswa
        </a>

        <a href="rekap_pelanggaran.php" class="menu-link">
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

<!-- KONTEN UTAMA -->

<main class="main">

<div class="container py-4">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <a href="../dashboard.php" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- JUDUL -->

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Catat Pelanggaran</h2>
            <p class="text-muted mb-0">
                Catat pelanggaran seluruh siswa di sekolah dengan mudah dan terstruktur.
            </p>
        </div>
    </div>

    <!-- PESAN -->

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?> mb-4">
            <?= e($pesan); ?>
        </div>
    <?php endif; ?>

    <?php if ($tahun_ajaran_id <= 0): ?>

        <div class="alert alert-warning">
            Tahun ajaran aktif belum ditemukan.
        </div>

    <?php elseif (count($daftar_kelas) === 0): ?>

        <div class="alert alert-warning">
            Belum ada kelas aktif yang tersedia. Silakan hubungi administrator.
        </div>

    <?php elseif (count($daftar_siswa) === 0): ?>

        <div class="alert alert-warning">
            Belum ada siswa dengan penempatan aktif pada tahun ajaran ini.
        </div>

    <?php endif; ?>

    <!-- FORM PELANGGARAN -->

    <div class="card mb-4">
        <div class="card-body">

            <h4 class="mb-1">Tambah Pelanggaran</h4>

            <p class="keterangan mb-4">
                Pilih kelas dan siswa, lalu masukkan jenis pelanggaran beserta keterangannya.
                Tindakan dapat ditambahkan melalui tombol pada tabel riwayat.
            </p>

            <form method="POST" action="catat_pelanggaran.php">

                <div class="mb-3">
                    <label for="nama_guru" class="form-label">
                        Wali Kelas yang Mencatat
                    </label>

                    <input
                        type="text"
                        id="nama_guru"
                        class="form-control"
                        value="<?= e($nama_guru); ?>"
                        readonly
                    >

                    <small class="text-muted">
                        Nama pencatat diambil otomatis dari akun yang sedang login.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Kelas</label>

                    <select
                        id="kelas_id"
                        name="kelas_id"
                        class="form-select"
                        required
                        <?= (
                            count($daftar_kelas) === 0 ||
                            $guru_id <= 0 ||
                            $tahun_ajaran_id <= 0
                        ) ? 'disabled' : ''; ?>
                    >
                        <option value="">-- Pilih Kelas --</option>

                        <?php foreach ($daftar_kelas as $k): ?>
                            <?php
                            $label = labelKelas(
                                $k['nama'],
                                $k['tingkat'],
                                $k['jurusan']
                            );
                            ?>

                            <option
                                value="<?= (int)$k['id']; ?>"
                                <?= $kelas_awal === (int)$k['id'] ? 'selected' : ''; ?>
                            >
                                <?= e($label); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label for="siswa_id" class="form-label">Nama Siswa</label>

                    <select
                        id="siswa_id"
                        name="siswa_id"
                        class="form-select"
                        required
                        disabled
                    >
                        <option value="">-- Pilih kelas terlebih dahulu --</option>

                        <?php foreach ($daftar_siswa as $s): ?>
                            <option
                                value="<?= (int)$s['id']; ?>"
                                data-kelas="<?= (int)$s['kelas_id']; ?>"
                            >
                                <?= e($s['nis'] . ' - ' . $s['nama']); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
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
                        <option value="">-- Pilih Pelanggaran --</option>

                        <?php foreach ($daftar_jenis as $p): ?>
                            <option
                                value="<?= (int)$p['id']; ?>"
                                <?= $jenis_awal === (int)$p['id'] ? 'selected' : ''; ?>
                            >
                                <?= e($p['kode'] . ' - ' . $p['nama']); ?>
                                (<?= e($p['tingkat']); ?>, <?= (int)$p['poin']; ?> poin)
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>

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
                    <label for="keterangan" class="form-label">Keterangan</label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        class="form-control"
                        rows="4"
                        placeholder="Contoh: Siswa membolos pada saat jam pelajaran."
                        required
                    ><?= e($_POST['keterangan'] ?? ''); ?></textarea>
                </div>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary"
                    <?= (
                        count($daftar_kelas) === 0 ||
                        count($daftar_siswa) === 0 ||
                        $guru_id <= 0 ||
                        $tahun_ajaran_id <= 0
                    ) ? 'disabled' : ''; ?>
                >
                    Simpan Pelanggaran
                </button>

            </form>

        </div>
    </div>

    <!-- RIWAYAT PELANGGARAN -->

    <div class="card">

        <div class="card-body">

            <h4 class="mb-1">Riwayat Pelanggaran</h4>

            <p class="keterangan mb-3">
                Daftar pelanggaran yang telah dicatat oleh Anda pada tahun ajaran aktif.
                Klik tombol Tindakan untuk mengisi atau memperbarui tindak lanjut.
            </p>

            <div class="table-responsive">

                <table
                    id="tabelRiwayat"
                    class="table table-bordered table-hover align-middle w-100"
                >

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Wali Kelas yang Mencatat</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                            <th>Poin</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                        } elseif (strcasecmp($status, 'Ditindaklanjuti') === 0) {
                            $status_class = 'status-ditindaklanjuti';
                        } elseif (strcasecmp($status, 'Selesai') === 0) {
                            $status_class = 'status-selesai';
                        }

                        $tindakan_tampil = trim((string)($data['tindakan'] ?? ''));
                        ?>

                        <tr>
                            <td><?= $i + 1; ?></td>

                            <td><?= e($data['tanggal']); ?></td>

                            <td><?= e($data['nama_siswa']); ?></td>

                            <td><?= e($kelas_tampil); ?></td>

                            <td><?= e($data['nama_pelanggaran']); ?></td>

                            <td><?= e($data['nama_guru'] ?? '-'); ?></td>

                            <td><?= e($data['keterangan']); ?></td>

                            <td>
                                <div class="tindakan-text">
                                    <?= $tindakan_tampil !== ''
                                        ? nl2br(e($tindakan_tampil))
                                        : '<span class="text-muted">Belum ada tindakan</span>'; ?>
                                </div>
                            </td>

                            <td><?= (int)$data['poin']; ?></td>

                            <td>
                                <span class="status-badge <?= e($status_class); ?>">
                                    <?= e($status); ?>
                                </span>
                            </td>

                            <td>
                                <?php if (strcasecmp($status, 'Selesai') === 0): ?>

                                    <span class="btn-terkunci">
                                        Selesai
                                    </span>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="btn-tindakan btn-buka-tindakan"
                                        data-id="<?= (int)$data['id']; ?>"
                                        data-siswa="<?= e($data['nama_siswa']); ?>"
                                        data-pelanggaran="<?= e($data['nama_pelanggaran']); ?>"
                                        data-tindakan="<?= e($tindakan_tampil); ?>"
                                        data-status="<?= e($status); ?>"
                                    >
                                        <?= $tindakan_tampil !== ''
                                            ? 'Ubah Tindakan'
                                            : 'Tambah Tindakan'; ?>
                                    </button>

                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php if (count($daftar_riwayat) === 0): ?>
                <p class="text-center text-muted py-3 mb-0">
                    Belum ada riwayat pelanggaran.
                </p>
            <?php endif; ?>

        </div>

    </div>

</div>

</main>

<!-- MODAL TINDAKAN -->

<div
    class="modal fade"
    id="modalTindakan"
    tabindex="-1"
    aria-labelledby="judulModalTindakan"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form method="POST" action="catat_pelanggaran.php">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="judulModalTindakan">
                            Tindakan Pelanggaran
                        </h5>

                        <small class="text-muted" id="infoPelanggaran">
                            Isi tindakan untuk siswa.
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="pelanggaran_siswa_id"
                        id="pelanggaran_siswa_id"
                    >

                    <div class="mb-3">
                        <label for="tindakan_baru" class="form-label">
                            Tindakan yang Diberikan
                        </label>

                        <textarea
                            name="tindakan_baru"
                            id="tindakan_baru"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Siswa diberikan teguran dan diminta membuat surat pernyataan."
                            required
                        ></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="status_baru" class="form-label">
                            Status Pelanggaran
                        </label>

                        <select
                            name="status_baru"
                            id="status_baru"
                            class="form-select"
                            required
                        >
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <small class="text-muted">
                        Jika status diubah menjadi Selesai, catatan tindakan tidak dapat diubah kembali.
                    </small>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        name="simpan_tindakan"
                        class="btn btn-primary"
                    >
                        Simpan Tindakan
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

/* =========================================
   FILTER SISWA BERDASARKAN KELAS
========================================= */

const pilihKelas = document.getElementById('kelas_id');
const pilihSiswa = document.getElementById('siswa_id');

const semuaPilihanSiswa = Array.from(
    pilihSiswa.options
).slice(1);

const siswaAwal = <?= (int)$siswa_awal; ?>;

function filterSiswa(idSiswa = 0) {

    const kelasDipilih = pilihKelas.value;

    pilihSiswa.innerHTML = '';

    const opsiAwal = document.createElement('option');

    opsiAwal.value = '';

    opsiAwal.textContent = kelasDipilih
        ? '-- Pilih Nama Siswa --'
        : '-- Pilih kelas terlebih dahulu --';

    pilihSiswa.appendChild(opsiAwal);

    let jumlahSiswa = 0;

    semuaPilihanSiswa.forEach(function(opsi) {

        if (
            kelasDipilih !== '' &&
            opsi.dataset.kelas === kelasDipilih
        ) {

            const salinan = opsi.cloneNode(true);

            if (Number(salinan.value) === Number(idSiswa)) {
                salinan.selected = true;
            }

            pilihSiswa.appendChild(salinan);
            jumlahSiswa++;
        }

    });

    pilihSiswa.disabled = kelasDipilih === '';

    if (kelasDipilih !== '' && jumlahSiswa === 0) {
        opsiAwal.textContent = '-- Belum ada siswa di kelas ini --';
    }
}

pilihKelas.addEventListener('change', function() {
    filterSiswa(0);
});

filterSiswa(siswaAwal);

/* =========================================
   MODAL TINDAKAN
========================================= */

const modalTindakanElement = document.getElementById('modalTindakan');

const modalTindakan = new bootstrap.Modal(modalTindakanElement);

const inputId = document.getElementById('pelanggaran_siswa_id');
const inputTindakan = document.getElementById('tindakan_baru');
const inputStatus = document.getElementById('status_baru');
const infoPelanggaran = document.getElementById('infoPelanggaran');

document.querySelectorAll('.btn-buka-tindakan').forEach(function(button) {

    button.addEventListener('click', function() {

        const id = this.dataset.id || '';
        const siswa = this.dataset.siswa || '';
        const pelanggaran = this.dataset.pelanggaran || '';
        const tindakan = this.dataset.tindakan || '';
        const status = this.dataset.status || 'Diproses';

        inputId.value = id;
        inputTindakan.value = tindakan;
        inputStatus.value = status;

        infoPelanggaran.textContent =
            siswa + ' - ' + pelanggaran;

        modalTindakan.show();
    });

});

/* =========================================
   DATATABLES
========================================= */

$(document).ready(function() {

    const jumlahRiwayat = <?= count($daftar_riwayat); ?>;

    if (jumlahRiwayat > 0) {

        $('#tabelRiwayat').DataTable({

            pageLength:10,

            lengthMenu:[10,25,50,100],

            order:[],

            scrollX:true,

            language: {

                search:"Cari:",

                searchPlaceholder:"Cari riwayat...",

                lengthMenu:"Tampilkan _MENU_ data",

                info:"Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat",

                infoEmpty:"Tidak ada riwayat pelanggaran",

                infoFiltered:"(disaring dari _MAX_ data)",

                zeroRecords:"Riwayat tidak ditemukan",

                emptyTable:"Belum ada riwayat pelanggaran",

                paginate:{
                    previous:"Sebelumnya",
                    next:"Berikutnya"
                }

            }

        });

    }

});

</script>

</body>
</html>
