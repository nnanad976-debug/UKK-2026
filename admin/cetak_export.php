
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$siswa_id = filter_input(INPUT_GET, 'siswa_id', FILTER_VALIDATE_INT);
if (!$siswa_id || $siswa_id < 1) {
    $siswa_id = '';
}

$data_siswa = null;
$kelas = '-';
$pelanggaran_data = [];
$total_poin = 0;
$error = '';

$query_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$query_siswa) {
    $error = "Daftar siswa gagal dimuat: " . mysqli_error($koneksi);
}

if ($siswa_id !== '') {
    // Ambil identitas siswa yang dipilih
    $stmt_siswa = mysqli_prepare(
        $koneksi,
        "SELECT *
         FROM t_siswa
         WHERE id = ?
         LIMIT 1"
    );

    if ($stmt_siswa) {
        mysqli_stmt_bind_param($stmt_siswa, "i", $siswa_id);
        mysqli_stmt_execute($stmt_siswa);
        $data_siswa = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt_siswa)
        );
        mysqli_stmt_close($stmt_siswa);
    }

    if (!$data_siswa) {
        $error = "Data siswa tidak ditemukan.";
    } else {
        /*
         * Ambil kelas terakhir dari catatan pelanggaran milik siswa ini.
         * Tidak mengambil catatan milik siswa lain.
         */
        $stmt_kelas = mysqli_prepare(
            $koneksi,
            "SELECT ps.nama_kelas, k.tingkat, k.jurusan
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_kelas k ON ps.kelas_id = k.id
             WHERE ps.siswa_id = ?
             ORDER BY ps.tanggal DESC, ps.id DESC
             LIMIT 1"
        );

        if ($stmt_kelas) {
            mysqli_stmt_bind_param($stmt_kelas, "i", $siswa_id);
            mysqli_stmt_execute($stmt_kelas);
            $data_kelas = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt_kelas)
            );

            if ($data_kelas) {
                if (!empty($data_kelas['tingkat']) &&
                    !empty($data_kelas['jurusan'])) {
                    $kelas = $data_kelas['tingkat'] . ' ' .
                             $data_kelas['jurusan'];
                } elseif (!empty($data_kelas['nama_kelas'])) {
                    $kelas = $data_kelas['nama_kelas'];
                }
            }

            mysqli_stmt_close($stmt_kelas);
        }

        /*
         * Ambil riwayat berdasarkan siswa_id.
         * Nama dan poin berasal dari catatan saat pelanggaran disimpan.
         * Kategori diambil melalui ID kategori.
         */
        $stmt_pelanggaran = mysqli_prepare(
            $koneksi,
            "SELECT
                ps.id,
                ps.tanggal,
                ps.nama_pelanggaran,
                ps.keterangan,
                ps.poin,
                ps.pelanggaran_id,
                ps.pelanggaran_kategori_id,
                COALESCE(kat.nama, 'Kategori tidak tersedia')
                    AS nama_kategori
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_pelanggaran_kategori kat
                ON ps.pelanggaran_kategori_id = kat.id
             WHERE ps.siswa_id = ?
             ORDER BY ps.tanggal DESC, ps.id DESC"
        );

        if ($stmt_pelanggaran) {
            mysqli_stmt_bind_param(
                $stmt_pelanggaran,
                "i",
                $siswa_id
            );
            mysqli_stmt_execute($stmt_pelanggaran);

            $result_pelanggaran = mysqli_stmt_get_result(
                $stmt_pelanggaran
            );

            while ($p = mysqli_fetch_assoc($result_pelanggaran)) {
                $pelanggaran_data[] = $p;
                $total_poin += (int)($p['poin'] ?? 0);
            }

            mysqli_stmt_close($stmt_pelanggaran);
        } else {
            $error = "Riwayat gagal dimuat: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cetak Kartu Pelanggaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

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
}

* { box-sizing: border-box; }

body {
    margin: 0;
    background: var(--page);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
}

a { text-decoration: none; }

.layout { min-height: 100vh; }

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 282px;
    height: 100vh;
    background: var(--white);
    border-right: 1px solid var(--line);
    padding: 25px 14px 16px;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    overflow-y: auto;
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

.menu-link {
    min-height: 43px;
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-radius: 10px;
    color: #5e7695;
    font-size: 13px;
    transition: .2s ease;
}

.menu-link:hover {
    background: var(--blue-soft);
    color: var(--blue);
}

.menu-link.active {
    background: var(--blue-soft);
    color: var(--blue);
    font-weight: 600;
    box-shadow: inset 4px 0 0 var(--blue);
}

.logout {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid var(--line);
}

.logout .menu-link { color: #d9534f; }

.logout .menu-link:hover {
    background: #fff0ef;
    color: #c03935;
}

.main {
    margin-left: 282px;
    min-height: 100vh;
    padding: 24px 28px 40px;
}

.container {
    width: 100%;
    max-width: none !important;
    margin: 0 auto;
    padding: 0 !important;
}

.judul, h4 { color: #234e78; }

.card {
    border: 1px solid #e1eaf4 !important;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(35,78,120,.05) !important;
}

.card-body { padding: 24px; }

.form-label {
    color: #405873;
    font-weight: 600;
}

.form-control, .form-select {
    border-color: #d7e3ef;
    border-radius: 7px;
    padding: 10px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #8eb5d8;
    box-shadow: 0 0 0 .2rem rgba(87,143,190,.14);
}

.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: #5f91ba;
    --bs-btn-border-color: #5f91ba;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #4b7fa9;
    --bs-btn-hover-border-color: #4b7fa9;
    border-radius: 7px;
    padding: 8px 15px;
}

.btn-secondary {
    --bs-btn-bg: #e8f0f8;
    --bs-btn-border-color: #d8e5f1;
    --bs-btn-color: #426584;
    --bs-btn-hover-bg: #dceaf6;
    --bs-btn-hover-border-color: #cbddeb;
    --bs-btn-hover-color: #234e78;
    border-radius: 7px;
}

.table {
    color: #344b63;
    vertical-align: middle;
    margin-bottom: 0;
}

.table thead th {
    background-color: #eaf2fa;
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

.kartu-header {
    background: var(--blue-dark);
    color: #fff;
}

.identitas {
    background: var(--blue-soft) !important;
    font-weight: 600;
}

.total-poin {
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
}

@media (max-width: 900px) {
    .sidebar {
        position: static;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 18px 14px 30px;
    }
}

@media (max-width: 576px) {
    .card-body { padding: 17px; }
}

@media print {
    body { background: #fff !important; }

    .sidebar, .no-print {
        display: none !important;
    }

    .main {
        margin: 0 !important;
        padding: 0 !important;
    }

    .container { padding: 0 !important; }

    .card {
        box-shadow: none !important;
        border: 1px solid #333 !important;
    }

    .kartu-header {
        background: #eaf3ff !important;
        color: #203e68 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .identitas, .total-poin, .table thead th, .badge-kategori {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @page {
        size: A4;
        margin: 12mm;
    }
}
</style>
</head>

<body>
<div class="layout">

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
        <a href="cetak_export.php" class="menu-link active">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">
<div class="container py-4">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 no-print">
        <a href="../dashboard.php" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="card mb-4 no-print">
        <div class="card-body">
            <h2 class="judul mb-1">Cetak Kartu Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">
                Pilih siswa untuk melihat dan mencetak kartu pelanggaran.
            </p>

            <form method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="siswa_id">Pilih Siswa</label>
                        <select id="siswa_id" name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>

                            <?php if ($query_siswa) {
                                mysqli_data_seek($query_siswa, 0);
                                while ($s = mysqli_fetch_assoc($query_siswa)) { ?>
                                    <option value="<?= e($s['id']); ?>"
                                        <?= (string)$siswa_id === (string)$s['id'] ? 'selected' : ''; ?>>
                                        <?= e($s['nis']); ?> - <?= e($s['nama']); ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($error !== '') { ?>
        <div class="alert alert-danger"><?= e($error); ?></div>
    <?php } ?>

    <?php if ($data_siswa) { ?>
    <div class="card mb-4">
        <div class="card-header kartu-header text-center py-4">
            <h2 class="mb-1">KARTU PELANGGARAN SISWA</h2>
            <strong>SISTEM INFORMASI PELANGGARAN SISWA</strong>
        </div>

        <div class="card-body p-4">
            <h5 class="mb-3">Identitas Siswa</h5>

            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <tr>
                        <td class="identitas" width="15%">NIS</td>
                        <td width="35%"><?= e($data_siswa['nis'] ?? '-'); ?></td>
                        <td class="identitas" width="15%">NISN</td>
                        <td width="35%"><?= e($data_siswa['nisn'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="identitas">Nama</td>
                        <td colspan="3"><?= e($data_siswa['nama'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="identitas">Jenis Kelamin</td>
                        <td>
                            <?= ($data_siswa['jenis_kelamin'] ?? '') === 'L'
                                ? 'Laki-laki'
                                : (($data_siswa['jenis_kelamin'] ?? '') === 'P'
                                    ? 'Perempuan' : '-'); ?>
                        </td>
                        <td class="identitas">Kelas</td>
                        <td><?= e($kelas); ?></td>
                    </tr>
                    <tr>
                        <td class="identitas">Alamat</td>
                        <td colspan="3"><?= e($data_siswa['alamat'] ?? '-'); ?></td>
                    </tr>
                </table>
            </div>

            <h5 class="mb-3">Riwayat Pelanggaran</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="7%" class="text-center">No</th>
                            <th width="16%">Tanggal</th>
                            <th>Pelanggaran</th>
                            <th width="20%">Kategori</th>
                            <th width="12%" class="text-center">Poin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($pelanggaran_data) > 0) {
                        $no = 1;
                        foreach ($pelanggaran_data as $p) { ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= e($p['tanggal'] ?? '-'); ?></td>
                                <td>
                                    <?= e($p['nama_pelanggaran'] ?? '-'); ?>
                                    <?php if (!empty($p['keterangan'])) { ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= e($p['keterangan']); ?>
                                        </small>
                                    <?php } ?>
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
                    <?php }
                    } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Belum ada riwayat pelanggaran untuk siswa ini.
                            </td>
                        </tr>
                    <?php } ?>

                        <tr class="total-poin">
                            <td colspan="4" class="text-end">TOTAL POIN</td>
                            <td class="text-center"><?= $total_poin; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-5">
                <div class="col-6 text-center">
                    Mengetahui,<br>
                    Wali Kelas<br><br><br><br>
                    (________________________)
                </div>
                <div class="col-6 text-center">
                    Guru<br>
                    Pencatat Pelanggaran<br><br><br><br>
                    (________________________)
                </div>
            </div>

            <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    Cetak Kartu
                </button>
            </div>
        </div>
    </div>
    <?php } elseif ($siswa_id !== '' && $error === '') { ?>
        <div class="alert alert-warning">Data siswa tidak ditemukan.</div>
    <?php } ?>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
