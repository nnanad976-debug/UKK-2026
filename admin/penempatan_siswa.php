
<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../config/koneksi.php';

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_set_charset($koneksi, 'utf8mb4');

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function tanggalValid(string $tanggal): bool {
    $date = DateTime::createFromFormat('!Y-m-d', $tanggal);
    $errors = DateTime::getLastErrors();

    return $date instanceof DateTime
        && $date->format('Y-m-d') === $tanggal
        && (
            $errors === false ||
            ($errors['warning_count'] === 0 && $errors['error_count'] === 0)
        );
}

function redirectStatus(string $status): void {
    header('Location: penempatan_siswa.php?status=' . urlencode($status));
    exit;
}

function ambilTahunAktif(mysqli $koneksi): ?array {
    $result = mysqli_query(
        $koneksi,
        "SELECT id, nama, tanggal_mulai, tanggal_selesai
         FROM t_tahun_ajaran
         WHERE status_aktif = 1
         ORDER BY tanggal_mulai DESC, id DESC
         LIMIT 1"
    );

    $data = mysqli_fetch_assoc($result);
    return $data ?: null;
}

$pesan = '';
$jenis_pesan = '';
$mode_edit = false;
$data_edit = null;

$tahun_aktif = ambilTahunAktif($koneksi);

if (!$tahun_aktif) {
    http_response_code(500);
    exit('Tahun ajaran aktif belum diatur. Aktifkan satu tahun ajaran terlebih dahulu pada menu Tahun Ajaran.');
}

$id_tahun_aktif = (int)$tahun_aktif['id'];

$tanggal_mulai_default = !empty($tahun_aktif['tanggal_mulai'])
    ? $tahun_aktif['tanggal_mulai']
    : date('Y-m-d');

$tanggal_selesai_default = !empty($tahun_aktif['tanggal_selesai'])
    ? $tahun_aktif['tanggal_selesai']
    : '';

/* =========================
   PROSES HAPUS PENEMPATAN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {

    $id_hapus = (int)($_POST['id'] ?? 0);

    if ($id_hapus <= 0) {
        redirectStatus('hapus_gagal');
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_kelas_siswa WHERE id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, 'i', $id_hapus);
    mysqli_stmt_execute($stmt);

    $terhapus = mysqli_stmt_affected_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    redirectStatus($terhapus ? 'hapus_sukses' : 'hapus_gagal');
}

/* =========================
   PROSES SIMPAN / EDIT
   HANYA TAHUN AJARAN AKTIF
========================= */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    in_array($_POST['aksi'] ?? '', ['simpan', 'edit'], true)
) {

    $aksi = $_POST['aksi'];

    $id = (int)($_POST['id'] ?? 0);
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);

    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    $mode_edit = ($aksi === 'edit');

    $data_edit = [
        'id' => $id,
        'siswa_id' => $siswa_id,
        'tahun_ajaran_id' => $id_tahun_aktif,
        'kelas_id' => $kelas_id,
        'tanggal_mulai' => $tanggal_mulai,
        'tanggal_selesai' => $tanggal_selesai
    ];

    if (
        ($mode_edit && $id <= 0) ||
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $tanggal_mulai === ''
    ) {

        $pesan = 'Semua data wajib diisi kecuali tanggal selesai.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id !== $id_tahun_aktif) {

        $pesan = 'Penempatan hanya dapat dilakukan pada tahun ajaran aktif ' . $tahun_aktif['nama'] . '.';
        $jenis_pesan = 'danger';

        $mode_edit = false;
        $data_edit = null;

    } elseif (
        !tanggalValid($tanggal_mulai) ||
        ($tanggal_selesai !== '' && !tanggalValid($tanggal_selesai))
    ) {

        $pesan = 'Tanggal yang dimasukkan tidak valid.';
        $jenis_pesan = 'danger';

    } elseif (
        $tanggal_selesai !== '' &&
        $tanggal_selesai < $tanggal_mulai
    ) {

        $pesan = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        $jenis_pesan = 'danger';

    } elseif (
        $tanggal_mulai < $tahun_aktif['tanggal_mulai'] ||
        (
            $tanggal_selesai !== '' &&
            !empty($tahun_aktif['tanggal_selesai']) &&
            $tanggal_selesai > $tahun_aktif['tanggal_selesai']
        )
    ) {

        $pesan = 'Tanggal penempatan harus berada dalam periode tahun ajaran aktif (' .
            date('d-m-Y', strtotime($tahun_aktif['tanggal_mulai'])) .
            ' sampai ' .
            (
                !empty($tahun_aktif['tanggal_selesai'])
                    ? date('d-m-Y', strtotime($tahun_aktif['tanggal_selesai']))
                    : '-'
            ) .
            ').';

        $jenis_pesan = 'danger';

    } else {

        /*
         * VALIDASI SISWA
         * SISWA HARUS BERSTATUS AKTIF
         */
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_siswa
             WHERE id = ?
             AND status_aktif = 1
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, 'i', $siswa_id);
        mysqli_stmt_execute($stmt);

        $siswa_valid = mysqli_stmt_get_result($stmt)->num_rows > 0;

        mysqli_stmt_close($stmt);

        /*
         * VALIDASI KELAS
         */
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_kelas
             WHERE id = ?
             AND status_aktif = 1
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, 'i', $kelas_id);
        mysqli_stmt_execute($stmt);

        $kelas_valid = mysqli_stmt_get_result($stmt)->num_rows > 0;

        mysqli_stmt_close($stmt);

        if (!$siswa_valid) {

            $pesan = 'Siswa tidak ditemukan atau sudah Tidak Aktif. Siswa Tidak Aktif tidak dapat ditempatkan.';
            $jenis_pesan = 'danger';

        } elseif (!$kelas_valid) {

            $pesan = 'Kelas tidak ditemukan atau tidak aktif.';
            $jenis_pesan = 'danger';

        } else {

            /*
             * CEGAH PENEMPATAN GANDA
             */
            if ($mode_edit) {

                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id
                     FROM t_kelas_siswa
                     WHERE siswa_id = ?
                     AND tahun_ajaran_id = ?
                     AND status_aktif = 1
                     AND id <> ?
                     LIMIT 1"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'iii',
                    $siswa_id,
                    $id_tahun_aktif,
                    $id
                );

            } else {

                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id
                     FROM t_kelas_siswa
                     WHERE siswa_id = ?
                     AND tahun_ajaran_id = ?
                     AND status_aktif = 1
                     LIMIT 1"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'ii',
                    $siswa_id,
                    $id_tahun_aktif
                );
            }

            mysqli_stmt_execute($stmt);

            $sudah_ada = mysqli_stmt_get_result($stmt)->num_rows > 0;

            mysqli_stmt_close($stmt);

            if ($sudah_ada) {

                $pesan = 'Siswa sudah memiliki penempatan aktif pada tahun ajaran ' .
                    $tahun_aktif['nama'] .
                    '. Periksa data penempatan terlebih dahulu.';

                $jenis_pesan = 'warning';

            } else {

                $selesai_db = ($tanggal_selesai === '')
                    ? null
                    : $tanggal_selesai;

                if ($mode_edit) {

                    /*
                     * PASTIKAN PENEMPATAN BERASAL DARI TAHUN AKTIF
                     */
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "SELECT id
                         FROM t_kelas_siswa
                         WHERE id = ?
                         AND tahun_ajaran_id = ?
                         LIMIT 1"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        'ii',
                        $id,
                        $id_tahun_aktif
                    );

                    mysqli_stmt_execute($stmt);

                    $boleh_edit = mysqli_stmt_get_result($stmt)->num_rows > 0;

                    mysqli_stmt_close($stmt);

                    if (!$boleh_edit) {

                        $pesan = 'Data tahun ajaran sebelumnya tidak dapat diedit melalui form penempatan tahun aktif.';
                        $jenis_pesan = 'warning';

                        $mode_edit = false;
                        $data_edit = null;

                    } else {

                        $stmt = mysqli_prepare(
                            $koneksi,
                            "UPDATE t_kelas_siswa
                             SET siswa_id = ?,
                                 tahun_ajaran_id = ?,
                                 kelas_id = ?,
                                 tanggal_mulai = ?,
                                 tanggal_selesai = ?
                             WHERE id = ?
                             LIMIT 1"
                        );

                        mysqli_stmt_bind_param(
                            $stmt,
                            'iiissi',
                            $siswa_id,
                            $id_tahun_aktif,
                            $kelas_id,
                            $tanggal_mulai,
                            $selesai_db,
                            $id
                        );

                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        redirectStatus('edit_sukses');
                    }

                } else {

                    $stmt = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_kelas_siswa
                        (
                            siswa_id,
                            tahun_ajaran_id,
                            kelas_id,
                            tanggal_mulai,
                            tanggal_selesai,
                            status_aktif
                        )
                        VALUES (?, ?, ?, ?, ?, 1)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        'iiiss',
                        $siswa_id,
                        $id_tahun_aktif,
                        $kelas_id,
                        $tanggal_mulai,
                        $selesai_db
                    );

                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    redirectStatus('simpan_sukses');
                }
            }
        }
    }
}

/* =========================
   PESAN HASIL PROSES
========================= */
$status = $_GET['status'] ?? '';

$pesan_status = [
    'simpan_sukses' => [
        'Data penempatan siswa berhasil disimpan.',
        'success'
    ],
    'edit_sukses' => [
        'Data penempatan siswa berhasil diperbarui.',
        'success'
    ],
    'hapus_sukses' => [
        'Data penempatan siswa berhasil dihapus.',
        'success'
    ],
    'hapus_gagal' => [
        'Data penempatan tidak ditemukan atau gagal dihapus.',
        'danger'
    ]
];

if (isset($pesan_status[$status])) {
    [$pesan, $jenis_pesan] = $pesan_status[$status];
}

/* =========================
   DATA SISWA UNTUK FORM
   HANYA SISWA AKTIF
========================= */
$data_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/* =========================
   DATA KELAS UNTUK FORM
========================= */
$data_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY FIELD(tingkat, 'X', 'XI', 'XII'),
              jurusan ASC,
              nama ASC"
);

/* =========================
   DATA UNTUK MODE EDIT
   HANYA PENEMPATAN TAHUN AKTIF
   DAN SISWA YANG MASIH AKTIF
========================= */
if (isset($_GET['edit'])) {

    $id_edit = (int)$_GET['edit'];

    if ($id_edit > 0) {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                ks.id,
                ks.siswa_id,
                ks.tahun_ajaran_id,
                ks.kelas_id,
                ks.tanggal_mulai,
                ks.tanggal_selesai
             FROM t_kelas_siswa ks
             INNER JOIN t_siswa s ON ks.siswa_id = s.id
             WHERE ks.id = ?
             AND ks.tahun_ajaran_id = ?
             AND s.status_aktif = 1
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            'ii',
            $id_edit,
            $id_tahun_aktif
        );

        mysqli_stmt_execute($stmt);

        $data_edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        mysqli_stmt_close($stmt);

        if ($data_edit) {

            $mode_edit = true;

        } else {

            $pesan = 'Data penempatan tidak ditemukan, siswa sudah Tidak Aktif, atau bukan berasal dari tahun ajaran aktif.';
            $jenis_pesan = 'warning';
        }
    }
}

/* =========================
   DATA PENEMPATAN
   SATU BARIS TERBARU PER SISWA/TAHUN AJARAN

   PERBAIKAN:
   STATUS TAMPILAN MENGIKUTI STATUS SISWA
   DAN STATUS PENEMPATAN.
========================= */
$data_penempatan = mysqli_query(
    $koneksi,
    "
    SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        s.status_aktif AS status_siswa,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif AS status_penempatan,

        CASE
            WHEN s.status_aktif = 1 AND ks.status_aktif = 1
            THEN 1
            ELSE 0
        END AS status_tampilan,

        ks.tahun_ajaran_id

    FROM t_kelas_siswa ks

    INNER JOIN (
        SELECT
            siswa_id,
            tahun_ajaran_id,
            MAX(id) AS id
        FROM t_kelas_siswa
        GROUP BY siswa_id, tahun_ajaran_id
    ) data_terbaru ON ks.id = data_terbaru.id

    INNER JOIN t_siswa s
        ON ks.siswa_id = s.id

    INNER JOIN t_tahun_ajaran ta
        ON ks.tahun_ajaran_id = ta.id

    INNER JOIN t_kelas k
        ON ks.kelas_id = k.id

    ORDER BY ks.id DESC
    "
);

if (!$data_siswa || !$data_kelas || !$data_penempatan) {
    die('Gagal mengambil data. Periksa struktur tabel database.');
}

/* =========================
   STATUS FORM
========================= */
$form_sedang_terbuka = (
    $mode_edit ||
    $jenis_pesan === 'danger' ||
    $jenis_pesan === 'warning'
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Penempatan Siswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
:root{
    --blue:#2878cf;
    --blue-dark:#203e68;
    --blue-soft:#eaf3ff;
    --page:#f6f9fd;
    --muted:#7890ad;
    --line:#dce9f8
}

*{box-sizing:border-box}

body{
    margin:0;
    background:var(--page);
    color:#344b63;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px
}

a{text-decoration:none}

.layout{min-height:100vh}

.sidebar{
    position:fixed;
    top:0;
    left:0;
    z-index:1000;
    display:flex;
    flex-direction:column;
    width:282px;
    height:100vh;
    padding:25px 14px 16px;
    overflow-y:auto;
    background:#fff;
    border-right:1px solid var(--line)
}

.brand{
    padding:0 15px 22px;
    margin-bottom:22px;
    border-bottom:1px solid var(--line)
}

.brand-title{
    margin-bottom:5px;
    color:var(--blue-dark);
    font-size:18px;
    font-weight:700
}

.brand-subtitle{
    color:var(--muted);
    font-size:12px
}

.menu-title{
    margin:0 15px 10px;
    color:#9aabc0;
    font-size:10px;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase
}

.menu{
    display:flex;
    flex-direction:column;
    gap:3px
}

.menu-link{
    display:flex;
    align-items:center;
    min-height:43px;
    padding:10px 15px;
    border-radius:10px;
    color:#5e7695;
    font-size:13px;
    transition:.2s
}

.menu-link:hover{
    background:var(--blue-soft);
    color:var(--blue)
}

.menu-link.active{
    background:var(--blue-soft);
    color:var(--blue);
    font-weight:600;
    box-shadow:inset 4px 0 0 var(--blue)
}

.logout{
    padding-top:14px;
    margin-top:auto;
    border-top:1px solid var(--line)
}

.logout .menu-link{color:#d9534f}

.logout .menu-link:hover{
    background:#fff0ef;
    color:#c03935
}

.main{
    min-height:100vh;
    padding:24px 28px 40px;
    margin-left:282px
}

.container-fluid-custom{
    width:100%;
    max-width:none;
    padding:0;
    margin:0
}

.judul{
    color:#234e78;
    font-size:24px;
    font-weight:700
}

.subjudul,.table-description{
    color:var(--muted);
    font-size:16px
}

.card{
    overflow:hidden;
    background:#fff;
    border:1px solid #e1eaf4!important;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(35,78,120,.05)
}

.card-body{padding:24px}

.form-label{
    color:#405873;
    font-size:16px;
    font-weight:600
}

.form-control,.form-select{
    padding:10px 12px;
    color:#344b63;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px;
    border-color:#d7e3ef;
    border-radius:7px
}

.form-control:focus,.form-select:focus{
    border-color:#8eb5d8;
    box-shadow:0 0 0 .2rem rgba(87,143,190,.14)
}

.btn{
    border-radius:7px;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px;
    font-weight:400;
    line-height:1.5;
    padding:8px 15px;
    transition:background-color .15s ease,border-color .15s ease
}

.btn-primary{
    --bs-btn-color:#fff;
    --bs-btn-bg:#5f91ba;
    --bs-btn-border-color:#5f91ba;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#4b7fa9;
    --bs-btn-hover-border-color:#4b7fa9
}

.btn-secondary{
    --bs-btn-bg:#e8f0f8;
    --bs-btn-border-color:#d8e5f1;
    --bs-btn-color:#426584;
    --bs-btn-hover-bg:#dceaf6;
    --bs-btn-hover-border-color:#cbddeb;
    --bs-btn-hover-color:#234e78
}

.btn-danger{
    --bs-btn-color:#fff;
    --bs-btn-bg:#cf8888;
    --bs-btn-border-color:#cf8888;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#bd7777;
    --bs-btn-hover-border-color:#bd7777
}

.btn-sm{padding:5px 11px}

.aksi .btn{
    padding:6px 10px;
    font-size:12px;
    border-radius:6px
}

.table-title{
    margin-bottom:4px;
    color:#234e78;
    font-size:24px;
    font-weight:600
}

.table-description{margin-bottom:18px}

.table-responsive{width:100%}

.table{
    width:100%;
    margin-bottom:0;
    color:#344b63;
    vertical-align:middle;
    font-size:14px
}

.table thead th{
    padding:14px 12px;
    color:#315b82;
    background:#fff;
    border-color:#dce7f1;
    font-weight:600;
    white-space:nowrap
}

.table tbody td{
    padding:13px 12px;
    background:#fff;
    border-color:#dce7f1
}

.table-hover tbody tr:hover>*{
    --bs-table-bg:#f4f8fc
}

.status-aktif,.status-nonaktif{
    display:inline-block;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap
}

.status-aktif{
    color:#34734b;
    background:#e4f3e9
}

.status-nonaktif{
    color:#a34f4f;
    background:#fce9e9
}

.keterangan{
    color:#71849a;
    font-size:16px
}

.aksi{
    display:flex;
    align-items:center;
    gap:8px;
    white-space:nowrap
}

div.dt-container{
    width:100%;
    color:#344b63;
    font-size:14px
}

div.dt-container .dt-layout-row{margin:12px 0}

div.dt-container .dt-layout-row:first-child{margin-top:18px}

div.dt-container .dt-length label,
div.dt-container .dt-search label{
    display:flex;
    align-items:center;
    gap:8px;
    color:#526a84;
    white-space:nowrap
}

.dt-container .dt-input{
    padding:8px 10px;
    color:#344b63;
    background:#fff;
    border:1px solid #d7e3ef;
    border-radius:7px
}

.dt-container .dt-length select{
    width:45px!important;
    min-width:45px;
    height:38px;
    padding:6px 5px
}

.dt-container .dt-search input{
    width:175px;
    height:38px;
    padding:7px 12px;
    margin-left:0!important
}

.dt-container .dt-input:focus{
    border-color:#8eb5d8;
    outline:none;
    box-shadow:0 0 0 .15rem rgba(87,143,190,.12)
}

.dt-container .dt-info{
    padding-top:15px;
    color:#71849a
}

.dt-container .dt-layout-row:first-child{
    margin-top:22px;
    margin-bottom:8px
}

.dt-container .dt-layout-cell{
    display:flex;
    align-items:center
}

.dt-container .dt-length,
.dt-container .dt-search{
    display:flex;
    align-items:center;
    gap:8px
}

.dt-container .dt-length label,
.dt-container .dt-search label{
    margin:0;
    font-size:13px
}

.dt-container .dt-layout-table{margin-top:8px}

@media(max-width:900px){
    .sidebar{
        position:relative;
        width:100%;
        height:auto;
        min-height:auto
    }

    .main{
        margin-left:0;
        padding:18px 14px 30px
    }

    .logout{margin-top:14px}
}

@media(max-width:576px){
    .main{padding:14px 10px 25px}
    .card-body{padding:17px}
    .judul,.table-title{font-size:21px}
    div.dt-container .dt-search input{width:140px}
    div.dt-container .dt-paging{overflow-x:auto}
}
</style>
</head>

<body>

<div class="layout">

<aside class="sidebar">

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
        <a href="penempatan_siswa.php" class="menu-link active">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Laporan</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link" onclick="return confirm('Yakin ingin keluar dari sistem?')">
            Logout
        </a>
    </div>

</aside>

<main class="main">

<div class="container-fluid-custom">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <a href="../dashboard.php" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>

        <?php if (!$form_sedang_terbuka): ?>
            <button
                type="button"
                class="btn btn-primary"
                id="btnTambah"
                aria-expanded="false"
            >
                + Tambah Penempatan
            </button>
        <?php endif; ?>

    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Penempatan Siswa</h2>
            <p class="subjudul mb-0">
                Kelola penempatan siswa berdasarkan tahun ajaran dan kelas.
            </p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan) ?> alert-dismissible fade show" role="alert">
            <?= e($pesan) ?>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup"
            ></button>
        </div>
    <?php endif; ?>

    <!-- FORM PENEMPATAN -->
    <div
        class="card mb-4 <?= $form_sedang_terbuka ? '' : 'd-none' ?>"
        id="formPenempatan"
    >

        <div class="card-body">

            <h4 class="mb-2">
                <?= $mode_edit ? 'Edit Penempatan Siswa' : 'Tambah Penempatan Siswa' ?>
            </h4>

            <p class="keterangan mb-4">
                Lengkapi data siswa, tahun ajaran, kelas, dan tanggal penempatan.
            </p>

            <form
                method="POST"
                action="penempatan_siswa.php<?= $mode_edit ? '?edit=' . (int)$data_edit['id'] : '' ?>"
            >

                <?php if ($mode_edit): ?>

                    <input type="hidden" name="id" value="<?= (int)$data_edit['id'] ?>">
                    <input type="hidden" name="aksi" value="edit">

                <?php else: ?>

                    <input type="hidden" name="aksi" value="simpan">

                <?php endif; ?>

                <input
                    type="hidden"
                    name="tahun_ajaran_id"
                    value="<?= $id_tahun_aktif ?>"
                >

                <div class="row g-3">

                    <div class="col-md-6">

                        <label for="siswa_id" class="form-label">
                            Nama Siswa
                        </label>

                        <select
                            name="siswa_id"
                            id="siswa_id"
                            class="form-select"
                            required
                        >

                            <option value="">-- Pilih Siswa --</option>

                            <?php while ($s = mysqli_fetch_assoc($data_siswa)): ?>

                                <option
                                    value="<?= (int)$s['id'] ?>"
                                    <?= (int)($data_edit['siswa_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($s['nis']) ?> - <?= e($s['nama']) ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Tahun Ajaran Aktif
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= e($tahun_aktif['nama']) ?>"
                            readonly
                        >

                        <small class="keterangan">
                            Penempatan baru hanya dapat dibuat pada tahun ajaran yang aktif.
                        </small>

                    </div>

                    <div class="col-md-6">

                        <label for="kelas_id" class="form-label">
                            Kelas
                        </label>

                        <select
                            name="kelas_id"
                            id="kelas_id"
                            class="form-select"
                            required
                        >

                            <option value="">-- Pilih Kelas --</option>

                            <?php while ($k = mysqli_fetch_assoc($data_kelas)): ?>

                                <option
                                    value="<?= (int)$k['id'] ?>"
                                    <?= (int)($data_edit['kelas_id'] ?? 0) === (int)$k['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($k['nama']) ?>
                                    (<?= e($k['tingkat']) ?> - <?= e($k['jurusan']) ?>)
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label for="tanggal_mulai" class="form-label">
                            Tanggal Mulai
                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            id="tanggal_mulai"
                            class="form-control"
                            min="<?= e($tahun_aktif['tanggal_mulai']) ?>"
                            max="<?= e($tahun_aktif['tanggal_selesai']) ?>"
                            value="<?= e($data_edit['tanggal_mulai'] ?? $tanggal_mulai_default) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-6">

                        <label for="tanggal_selesai" class="form-label">
                            Tanggal Selesai
                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            id="tanggal_selesai"
                            class="form-control"
                            min="<?= e($tahun_aktif['tanggal_mulai']) ?>"
                            max="<?= e($tahun_aktif['tanggal_selesai']) ?>"
                            value="<?= e($data_edit['tanggal_selesai'] ?? $tanggal_selesai_default) ?>"
                        >

                        <small class="keterangan">
                            Otomatis mengikuti akhir tahun ajaran aktif. Kosongkan jika penempatan masih berjalan.
                        </small>

                    </div>

                    <div class="col-12 d-flex gap-2 mt-4">

                        <button type="submit" class="btn btn-primary">
                            <?= $mode_edit ? 'Simpan Perubahan' : 'Simpan Penempatan' ?>
                        </button>

                        <?php if ($mode_edit): ?>

                            <a href="penempatan_siswa.php" class="btn btn-secondary">
                                Batal
                            </a>

                        <?php else: ?>

                            <button
                                type="button"
                                id="btnBatal"
                                class="btn btn-secondary"
                            >
                                Batal
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- TABEL DATA PENEMPATAN -->
    <div class="card">

        <div class="card-body">

            <h2 class="table-title">Data Penempatan Siswa</h2>

            <p class="table-description">
                Daftar siswa berdasarkan tahun ajaran dan kelas.
            </p>

            <div class="table-responsive">

                <table
                    id="tabelPenempatan"
                    class="table table-bordered table-hover align-middle w-100"
                >

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Tahun Ajaran</th>
                            <th>Kelas</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($p = mysqli_fetch_assoc($data_penempatan)):
                        ?>

                            <tr>

                                <td><?= $no++ ?></td>

                                <td><?= e($p['nis']) ?></td>

                                <td><?= e($p['nama_siswa']) ?></td>

                                <td><?= e($p['tahun_ajaran']) ?></td>

                                <td>
                                    <?= e($p['nama_kelas']) ?>
                                    (<?= e($p['tingkat']) ?> - <?= e($p['jurusan']) ?>)
                                </td>

                                <td>
                                    <?= !empty($p['tanggal_mulai'])
                                        ? date('d-m-Y', strtotime($p['tanggal_mulai']))
                                        : '-' ?>
                                </td>

                                <td>
                                    <?= !empty($p['tanggal_selesai'])
                                        ? date('d-m-Y', strtotime($p['tanggal_selesai']))
                                        : '-' ?>
                                </td>

                                <td>

                                    <?php if ((int)$p['status_tampilan'] === 1): ?>

                                        <span class="status-aktif">Aktif</span>

                                    <?php else: ?>

                                        <span class="status-nonaktif">Tidak Aktif</span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="aksi">

                                        <?php if (
                                            (int)$p['tahun_ajaran_id'] === $id_tahun_aktif &&
                                            (int)$p['status_siswa'] === 1
                                        ): ?>

                                            <a
                                                href="penempatan_siswa.php?edit=<?= (int)$p['id'] ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Edit
                                            </a>

                                        <?php elseif ((int)$p['status_siswa'] !== 1): ?>

                                            <span class="keterangan">Siswa nonaktif</span>

                                        <?php else: ?>

                                            <span class="keterangan">Riwayat</span>

                                        <?php endif; ?>

                                        <form
                                            method="POST"
                                            action="penempatan_siswa.php"
                                            class="form-hapus m-0"
                                        >

                                            <input
                                                type="hidden"
                                                name="aksi"
                                                value="hapus"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$p['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tombolTambah = document.getElementById('btnTambah');
    const tombolBatal = document.getElementById('btnBatal');
    const form = document.getElementById('formPenempatan');

    /* BUKA FORM TAMBAH */
    if (form) {

        const formTerbuka = !form.classList.contains('d-none');

        if (formTerbuka && tombolTambah) {
            tombolTambah.classList.add('d-none');
            tombolTambah.setAttribute('aria-expanded', 'true');
        }

        if (tombolTambah) {

            tombolTambah.addEventListener('click', function () {

                form.classList.remove('d-none');

                tombolTambah.classList.add('d-none');

                tombolTambah.setAttribute('aria-expanded', 'true');

                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

            });

        }

        /* TOMBOL BATAL */
        if (tombolBatal) {

            tombolBatal.addEventListener('click', function () {

                form.classList.add('d-none');

                if (tombolTambah) {
                    tombolTambah.classList.remove('d-none');
                    tombolTambah.setAttribute('aria-expanded', 'false');
                }

            });

        }

    }

    /* KONFIRMASI HAPUS */
    document.querySelectorAll('.form-hapus').forEach(function (formHapus) {

        formHapus.addEventListener('submit', function (event) {

            if (!confirm('Yakin ingin menghapus data penempatan siswa ini?')) {
                event.preventDefault();
            }

        });

    });

    /* DATATABLES */
    if (window.jQuery && $.fn.DataTable) {

        $('#tabelPenempatan').DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            ordering: true,

            searching: true,

            autoWidth: false,

            language: {

                lengthMenu: 'Tampilkan _MENU_ data',

                search: 'Cari:',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',

                infoFiltered: '(disaring dari _MAX_ total data)',

                zeroRecords: 'Data tidak ditemukan',

                emptyTable: 'Belum ada data penempatan siswa',

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
