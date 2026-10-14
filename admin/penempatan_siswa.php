
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit("Anda tidak memiliki akses");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$pesan = '';
$jenis_pesan = '';

/* =========================
   PROSES SIMPAN
========================= */
if (isset($_POST['simpan'])) {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    if (!$siswa_id || !$tahun_ajaran_id || !$kelas_id || !$tanggal_mulai) {
        $pesan = "Semua data wajib diisi kecuali tanggal selesai.";
        $jenis_pesan = "danger";
    } elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai) ||
        ($tanggal_selesai !== '' &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai))
    ) {
        $pesan = "Format tanggal tidak valid.";
        $jenis_pesan = "danger";
    } elseif ($tanggal_selesai !== '' && $tanggal_selesai < $tanggal_mulai) {
        $pesan = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
        $jenis_pesan = "danger";
    } else {
        /* Periksa siswa */
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_siswa WHERE id = ? AND status_aktif = 1 LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "i", $siswa_id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $siswa_valid = mysqli_num_rows($hasil) > 0;
        mysqli_stmt_close($stmt);

        if (!$siswa_valid) {
            $pesan = "Siswa tidak ditemukan atau tidak aktif.";
            $jenis_pesan = "danger";
        } else {
            /* Periksa tahun ajaran */
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_tahun_ajaran WHERE id = ? LIMIT 1"
            );

            mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
            mysqli_stmt_execute($stmt);
            $hasil = mysqli_stmt_get_result($stmt);
            $tahun_valid = mysqli_num_rows($hasil) > 0;
            mysqli_stmt_close($stmt);

            if (!$tahun_valid) {
                $pesan = "Tahun ajaran tidak ditemukan.";
                $jenis_pesan = "danger";
            } else {
                /* Periksa kelas */
                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id FROM t_kelas WHERE id = ? AND status_aktif = 1 LIMIT 1"
                );

                mysqli_stmt_bind_param($stmt, "i", $kelas_id);
                mysqli_stmt_execute($stmt);
                $hasil = mysqli_stmt_get_result($stmt);
                $kelas_valid = mysqli_num_rows($hasil) > 0;
                mysqli_stmt_close($stmt);

                if (!$kelas_valid) {
                    $pesan = "Kelas tidak ditemukan atau tidak aktif.";
                    $jenis_pesan = "danger";
                } else {
                    /* Cegah penempatan aktif yang sama */
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "SELECT id FROM t_kelas_siswa
                         WHERE siswa_id = ?
                         AND tahun_ajaran_id = ?
                         AND kelas_id = ?
                         AND status_aktif = 1
                         LIMIT 1"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iii",
                        $siswa_id,
                        $tahun_ajaran_id,
                        $kelas_id
                    );

                    mysqli_stmt_execute($stmt);
                    $hasil = mysqli_stmt_get_result($stmt);
                    $duplikat = mysqli_num_rows($hasil) > 0;
                    mysqli_stmt_close($stmt);

                    if ($duplikat) {
                        $pesan = "Penempatan siswa tersebut sudah terdaftar.";
                        $jenis_pesan = "warning";
                    } else {
                        $selesai_db = $tanggal_selesai !== ''
                            ? $tanggal_selesai
                            : null;

                        $stmt = mysqli_prepare(
                            $koneksi,
                            "INSERT INTO t_kelas_siswa
                            (siswa_id, tahun_ajaran_id, kelas_id,
                             tanggal_mulai, tanggal_selesai, status_aktif)
                            VALUES (?, ?, ?, ?, ?, 1)"
                        );

                        if (!$stmt) {
                            $pesan = "Gagal menyiapkan penyimpanan data.";
                            $jenis_pesan = "danger";
                        } else {
                            mysqli_stmt_bind_param(
                                $stmt,
                                "iiiss",
                                $siswa_id,
                                $tahun_ajaran_id,
                                $kelas_id,
                                $tanggal_mulai,
                                $selesai_db
                            );

                            if (mysqli_stmt_execute($stmt)) {
                                mysqli_stmt_close($stmt);
                                header("Location: penempatan_siswa.php?status=sukses");
                                exit;
                            } else {
                                $pesan = "Penempatan gagal disimpan. Periksa kembali data database.";
                                $jenis_pesan = "danger";
                                mysqli_stmt_close($stmt);
                            }
                        }
                    }
                }
            }
        }
    }
}

if (($_GET['status'] ?? '') === 'sukses') {
    $pesan = "Data penempatan siswa berhasil disimpan.";
    $jenis_pesan = "success";
}

/* =========================
   DATA DROPDOWN
========================= */
$data_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

$data_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     ORDER BY CAST(LEFT(nama, 4) AS UNSIGNED) DESC"
);

$data_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY FIELD(tingkat, 'X', 'XI', 'XII'), nama ASC"
);

/* =========================
   DATA TABEL
========================= */
$data_penempatan = mysqli_query(
    $koneksi,
    "SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif
     FROM t_kelas_siswa ks
     INNER JOIN t_siswa s ON ks.siswa_id = s.id
     INNER JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
     INNER JOIN t_kelas k ON ks.kelas_id = k.id
     ORDER BY ks.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penempatan Siswa - Sistem Pelanggaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

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
    --shadow:0 8px 22px rgba(32,62,104,.07);
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

.judul, h4 {
    color:#234e78;
}

.judul {
    font-size:24px;
    font-weight:700;
}

.subjudul {
    color:var(--muted);
    font-size:13px;
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

.form-control, .form-select {
    border-color:#d7e3ef;
    border-radius:7px;
    padding:10px 12px;
}

.form-control:focus, .form-select:focus {
    border-color:#8eb5d8;
    box-shadow:0 0 0 .2rem rgba(87,143,190,.14);
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
}

.btn-sm {
    padding:5px 11px;
}

.table {
    color:#344b63;
    vertical-align:middle;
    margin-bottom:0;
}

.table thead th {
    background-color:#eaf2fa;
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

.status-aktif {
    display:inline-block;
    background:#e4f3e9;
    color:#34734b;
    padding:5px 10px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.status-nonaktif {
    display:inline-block;
    background:#fce9e9;
    color:#a34f4f;
    padding:5px 10px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.keterangan {
    color:#71849a;
    font-size:14px;
}

@media (max-width:900px) {
    .sidebar {
        position:static;
        width:100%;
        height:auto;
    }

    .main {
        margin-left:0;
        padding:18px 14px 30px;
    }
}

@media (max-width:576px) {
    .container {
        padding-left:14px;
        padding-right:14px;
    }

    .card-body {
        padding:17px;
    }
}
</style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">Panel Administrator</div>
        </div>

        <div class="menu-title">Menu Utama</div>

        <nav class="menu">
            <a href="dashboard.php" class="menu-link">Dashboard</a>
            <a href="siswa.php" class="menu-link">Data Siswa</a>
            <a href="guru.php" class="menu-link">Kelola Guru</a>
            <a href="kelas.php" class="menu-link">Data Kelas</a>
            <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
            <a href="penempatan_siswa.php" class="menu-link active">Penempatan Siswa</a>
            <a href="kategori.php" class="menu-link">Kategori Pelanggaran</a>
            <a href="pelanggaran.php" class="menu-link">Data Pelanggaran</a>
            <a href="laporan.php" class="menu-link">Laporan</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="menu-link"
               onclick="return confirm('Yakin ingin keluar dari sistem?')">
                Keluar
            </a>
        </div>
    </aside>

    <main class="main">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="judul mb-1">Penempatan Siswa</h2>
                    <p class="subjudul mb-0">
                        Kelola penempatan siswa berdasarkan tahun ajaran dan kelas.
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>

                    <button type="button" class="btn btn-primary"
                            data-bs-toggle="collapse"
                            data-bs-target="#formPenempatan">
                        + Tambah Penempatan
                    </button>
                </div>
            </div>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-<?= e($jenis_pesan) ?> alert-dismissible fade show">
                    <?= e($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM TAMBAH -->
            <div class="collapse mb-4" id="formPenempatan">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">Tambah Penempatan Siswa</h4>
                        <p class="keterangan mb-4">
                            Lengkapi data untuk menempatkan siswa ke kelas.
                        </p>

                        <form method="POST">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="siswa_id" class="form-label">Nama Siswa</label>
                                    <select name="siswa_id" id="siswa_id" class="form-select" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        <?php if ($data_siswa): ?>
                                            <?php while ($s = mysqli_fetch_assoc($data_siswa)): ?>
                                                <option value="<?= e($s['id']) ?>">
                                                    <?= e($s['nis']) ?> - <?= e($s['nama']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select" required>
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        <?php if ($data_tahun): ?>
                                            <?php while ($ta = mysqli_fetch_assoc($data_tahun)): ?>
                                                <option value="<?= e($ta['id']) ?>">
                                                    <?= e($ta['nama']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-select" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php if ($data_kelas): ?>
                                            <?php while ($k = mysqli_fetch_assoc($data_kelas)): ?>
                                                <option value="<?= e($k['id']) ?>">
                                                    <?= e($k['nama']) ?>
                                                    (<?= e($k['tingkat']) ?> - <?= e($k['jurusan']) ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai"
                                           id="tanggal_mulai" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai"
                                           id="tanggal_selesai" class="form-control">
                                    <small class="keterangan">
                                        Kosongkan jika penempatan masih berjalan.
                                    </small>
                                </div>

                                <div class="col-12 d-flex gap-2 mt-4">
                                    <button type="submit" name="simpan" class="btn btn-primary">
                                        Simpan Penempatan
                                    </button>
                                    <button type="button" class="btn btn-secondary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#formPenempatan">
                                        Batal
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TABEL DATA -->
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="mb-1">Data Penempatan Siswa</h4>
                        <p class="keterangan mb-0">
                            Daftar siswa berdasarkan tahun ajaran dan kelas.
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($data_penempatan && mysqli_num_rows($data_penempatan) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while ($p = mysqli_fetch_assoc($data_penempatan)): ?>
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
                                                <?= $p['tanggal_mulai']
                                                    ? date('d-m-Y', strtotime($p['tanggal_mulai']))
                                                    : '-' ?>
                                            </td>
                                            <td>
                                                <?= $p['tanggal_selesai']
                                                    ? date('d-m-Y', strtotime($p['tanggal_selesai']))
                                                    : '-' ?>
                                            </td>
                                            <td>
                                                <?php if ((int)$p['status_aktif'] === 1): ?>
                                                    <span class="status-aktif">Aktif</span>
                                                <?php else: ?>
                                                    <span class="status-nonaktif">Tidak Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <span class="keterangan">
                                                Belum ada data penempatan siswa.
                                            </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
