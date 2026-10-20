<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    exit("Anda tidak memiliki akses");
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

    if ($nama !== '' &&
        stripos($nama, $tingkat) !== false &&
        stripos($nama, $jurusan) !== false) {
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

/* =========================
   DATA GURU
========================= */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
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

/* =========================
   TAHUN AJARAN AKTIF
========================= */
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

$data_tahun = $query_tahun
    ? mysqli_fetch_assoc($query_tahun)
    : null;

$tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

/* =========================
   DAFTAR KELAS
========================= */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY FIELD(tingkat,'X','XI','XII'),
              FIELD(jurusan,'RPL','TKJ','BD','TKR','TSM'),
              nama ASC"
);

/* =========================
   DAFTAR SISWA
========================= */
$query_siswa = false;

if ($tahun_ajaran_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT s.id, s.nis, s.nama, ks.kelas_id
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
         INNER JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE s.status_aktif = 1
           AND ks.status_aktif = 1
           AND ks.tahun_ajaran_id = ?
           AND k.status_aktif = 1
         ORDER BY s.nama ASC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
        mysqli_stmt_execute($stmt);
        $query_siswa = mysqli_stmt_get_result($stmt);
    }
}

/* =========================
   JENIS PELANGGARAN
========================= */
$query_jenis = mysqli_query(
    $koneksi,
    "SELECT id, kode, nama, poin, tingkat, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY id ASC"
);

/* =========================
   PROSES SIMPAN
========================= */
if (isset($_POST['simpan'])) {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $tanggal_obj = DateTime::createFromFormat('!Y-m-d', $tanggal);
    $tanggal_valid = $tanggal_obj !== false
        && $tanggal_obj->format('Y-m-d') === $tanggal;

    if ($guru_id <= 0) {
        $pesan = 'Data akun guru tidak ditemukan pada data guru.';
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
        $pesan = 'Lengkapi semua data sebelum menyimpan pelanggaran.';
        $jenis_pesan = 'danger';
    } else {
        /* Validasi siswa dan kelas */
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT s.id, s.nama, ks.kelas_id,
                    k.nama AS nama_kelas, k.tingkat, k.jurusan
             FROM t_siswa s
             INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
             INNER JOIN t_kelas k ON k.id = ks.kelas_id
             WHERE s.id = ?
               AND ks.kelas_id = ?
               AND ks.tahun_ajaran_id = ?
               AND s.status_aktif = 1
               AND ks.status_aktif = 1
               AND k.status_aktif = 1
             LIMIT 1"
        );

        if (!$stmt) {
            $pesan = 'Gagal memeriksa data siswa.';
            $jenis_pesan = 'danger';
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $siswa_id,
                $kelas_id,
                $tahun_ajaran_id
            );

            mysqli_stmt_execute($stmt);
            $data_siswa = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt)
            );
            mysqli_stmt_close($stmt);

            if (!$data_siswa) {
                $pesan = 'Siswa tidak ditemukan pada kelas dan tahun ajaran aktif.';
                $jenis_pesan = 'danger';
            } else {
                /* Validasi pelanggaran */
                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id, nama, poin, pelanggaran_kategori_id
                     FROM t_pelanggaran
                     WHERE id = ? AND status_aktif = 1
                     LIMIT 1"
                );

                if (!$stmt) {
                    $data_pelanggaran = null;
                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "i",
                        $pelanggaran_id
                    );

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
                    $nama_pelanggaran = $data_pelanggaran['nama'];
                    $kategori_id = (int)(
                        $data_pelanggaran['pelanggaran_kategori_id'] ?? 0
                    );
                    $poin = (int)$data_pelanggaran['poin'];

                    $nama_kelas = labelKelas(
                        $data_siswa['nama_kelas'],
                        $data_siswa['tingkat'],
                        $data_siswa['jurusan']
                    );

                    $tindakan = '';
                    $status = 'Diproses';
                    $nama_guru_simpan = $nama_guru;

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

if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Pelanggaran berhasil dicatat!';
    $jenis_pesan = 'success';
}

/* =========================
   RIWAYAT PELANGGARAN GURU
========================= */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT ps.*,
            k.nama AS nama_kelas_asli,
            k.tingkat,
            k.jurusan
     FROM t_pelanggaran_siswa ps
     LEFT JOIN t_kelas k ON k.id = ps.kelas_id
     WHERE ps.guru_id = ?
     ORDER BY ps.tanggal DESC, ps.id DESC"
);

$query_riwayat = false;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $guru_id);
    mysqli_stmt_execute($stmt);
    $query_riwayat = mysqli_stmt_get_result($stmt);
}

/* Simpan hasil query ke array */
$daftar_siswa = [];

if ($query_siswa) {
    while ($s = mysqli_fetch_assoc($query_siswa)) {
        $daftar_siswa[] = $s;
    }
}

if (isset($stmt) && $stmt) {
    mysqli_stmt_close($stmt);
}

$daftar_kelas = [];

if ($query_kelas) {
    while ($k = mysqli_fetch_assoc($query_kelas)) {
        $daftar_kelas[] = $k;
    }
}

$daftar_jenis = [];

if ($query_jenis) {
    while ($p = mysqli_fetch_assoc($query_jenis)) {
        $daftar_jenis[] = $p;
    }
}

$daftar_riwayat = [];

if ($query_riwayat) {
    while ($data = mysqli_fetch_assoc($query_riwayat)) {
        $daftar_riwayat[] = $data;
    }
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

.layout {
    min-height:100vh;
}

/* SIDEBAR DISAMAKAN DENGAN KELOLA GURU */
.sidebar {
    position:fixed;
    left:0;
    top:0;
    width:282px;
    height:100vh;
    background:var(--white);
    border-right:1px solid var(--line);
    padding:25px 14px 16px;
    display:flex;
    flex-direction:column;
    z-index:1000;
}

.brand {
    padding:0 15px 22px;
    border-bottom:1px solid var(--line);
    margin-bottom:22px;
}

.brand-title {
    font-size:18px;
    font-weight:700;
    color:var(--blue-dark);
    margin-bottom:5px;
}

.brand-subtitle {
    font-size:12px;
    color:var(--muted);
}

.menu-title {
    font-size:10px;
    font-weight:700;
    color:#9aabc0;
    letter-spacing:1px;
    text-transform:uppercase;
    margin:0 15px 10px;
}

.menu {
    display:flex;
    flex-direction:column;
    gap:3px;
}

.menu-link {
    min-height:43px;
    display:flex;
    align-items:center;
    padding:10px 15px;
    border-radius:10px;
    color:#5e7695;
    font-size:13px;
    transition:.2s ease;
}

.menu-link:hover {
    background:var(--blue-soft);
    color:var(--blue);
}

.menu-link.active {
    background:var(--blue-soft);
    color:var(--blue);
    font-weight:600;
    box-shadow:inset 4px 0 0 var(--blue);
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
    color:#c03935;
}

/* AREA KONTEN */
.main {
    margin-left:282px;
    min-height:100vh;
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

.table-hover tbody tr:hover > * {
    --bs-table-accent-bg:#f4f8fc;
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
    background-color:#fff;
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

.dt-container .dt-layout-table {
    margin-top:8px;
}

.dt-paging .dt-paging-button {
    border-radius:6px !important;
}

.alert {
    border-radius:7px;
}

/* RESPONSIF TANPA MENGECILKAN SIDEBAR DESKTOP */
@media(max-width:900px) {
    .sidebar {
        position:relative;
        width:100%;
        height:auto;
        min-height:auto;
    }

    .main {
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
<div class="layout">

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
<div class="container py-4">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <a href="../dashboard.php" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Catat Pelanggaran</h2>
            <p class="text-muted mb-0">
                Catat dan kelola pelanggaran siswa.
            </p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?> mb-4">
            <?= e($pesan); ?>
        </div>
    <?php endif; ?>

    <?php if ($tahun_ajaran_id <= 0): ?>
        <div class="alert alert-warning">
            Tahun ajaran aktif belum ditemukan. Periksa menu Tahun Ajaran.
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">Tambah Pelanggaran</h4>
            <p class="keterangan mb-4">
                Masukkan data pelanggaran siswa dengan lengkap.
            </p>

            <form method="POST" action="catat_pelanggaran.php">
                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Kelas</label>

                    <select id="kelas_id" name="kelas_id"
                            class="form-select" required>
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
                                <?= (int)($_POST['kelas_id'] ?? 0) === (int)$k['id'] ? 'selected' : ''; ?>>
                                <?= e($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="siswa_id" class="form-label">Nama Siswa</label>

                    <select id="siswa_id" name="siswa_id"
                            class="form-select" required disabled>
                        <option value="">-- Pilih kelas terlebih dahulu --</option>

                        <?php foreach ($daftar_siswa as $s): ?>
                            <option
                                value="<?= (int)$s['id']; ?>"
                                data-kelas="<?= (int)$s['kelas_id']; ?>">
                                <?= e($s['nis'] . ' - ' . $s['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pelanggaran_id" class="form-label">
                        Jenis Pelanggaran
                    </label>

                    <select id="pelanggaran_id" name="pelanggaran_id"
                            class="form-select" required>
                        <option value="">-- Pilih Pelanggaran --</option>

                        <?php foreach ($daftar_jenis as $p): ?>
                            <option
                                value="<?= (int)$p['id']; ?>"
                                <?= (int)($_POST['pelanggaran_id'] ?? 0) === (int)$p['id'] ? 'selected' : ''; ?>>
                                <?= e($p['kode'] . ' - ' . $p['nama']); ?>
                                (<?= e($p['tingkat']); ?>, <?= (int)$p['poin']; ?> poin)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>

                    <input type="date" id="tanggal" name="tanggal"
                           class="form-control"
                           value="<?= e($_POST['tanggal'] ?? date('Y-m-d')); ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="form-label">Keterangan</label>

                    <textarea id="keterangan" name="keterangan"
                              class="form-control" rows="4"
                              placeholder="Masukkan keterangan pelanggaran"
                              required><?= e($_POST['keterangan'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Pelanggaran
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-1">Riwayat Pelanggaran</h4>
            <p class="keterangan mb-3">
                Daftar pelanggaran yang telah dicatat.
            </p>

            <div class="table-responsive">
                <table id="tabelRiwayat"
                       class="table table-bordered table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
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

            <?php if (count($daftar_riwayat) === 0): ?>
                <p class="text-center text-muted py-3 mb-0">
                    Belum ada riwayat pelanggaran.
                </p>
            <?php endif; ?>
        </div>
    </div>

</div>
</main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
/* FILTER SISWA BERDASARKAN KELAS */
const pilihKelas = document.getElementById('kelas_id');
const pilihSiswa = document.getElementById('siswa_id');

const semuaPilihanSiswa = Array.from(pilihSiswa.options).slice(1);
const siswaAwal = <?= (int)($_POST['siswa_id'] ?? 0); ?>;

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

/* TABEL RIWAYAT: PENCARIAN DAN PAGINATION */
$(document).ready(function() {
    const jumlahRiwayat = <?= count($daftar_riwayat); ?>;

    if (jumlahRiwayat > 0) {
        $('#tabelRiwayat').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            scrollX: true,
            language: {
                search: "Cari:",
                searchPlaceholder: "Cari riwayat...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat",
                infoEmpty: "Tidak ada riwayat pelanggaran",
                infoFiltered: "(disaring dari _MAX_ data)",
                zeroRecords: "Riwayat tidak ditemukan",
                emptyTable: "Belum ada riwayat pelanggaran",
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