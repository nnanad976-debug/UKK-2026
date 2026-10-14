<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo "Anda tidak memiliki akses";
    exit;
}

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$pesan = '';
$jenis_pesan = '';
$tampilkan_form = false;

/* Notifikasi setelah redirect, termasuk dari proses hapus siswa. */
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'tambah_sukses':
            $pesan = 'Data siswa berhasil disimpan!';
            $jenis_pesan = 'success';
            break;
        case 'hapus_sukses':
            $pesan = 'Data siswa berhasil dihapus!';
            $jenis_pesan = 'success';
            break;
        case 'data_terkait':
            $pesan = 'Siswa tidak bisa dihapus karena masih memiliki data penempatan kelas atau catatan pelanggaran.';
            $jenis_pesan = 'warning';
            break;
        case 'hapus_gagal':
            $pesan = 'Data siswa gagal dihapus. Periksa data terkait atau konfigurasi database.';
            $jenis_pesan = 'danger';
            break;
        case 'id_tidak_valid':
            $pesan = 'ID siswa tidak valid.';
            $jenis_pesan = 'danger';
            break;
    }
}

/* =========================
   PROSES TAMBAH SISWA
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = filter_input(INPUT_POST, 'status_aktif', FILTER_VALIDATE_INT);

    $tampilkan_form = true;

    if ($nis === '' || $nisn === '' || $nama === '' ||
        !in_array($jenis_kelamin, ['L', 'P'], true) ||
        $tanggal_lahir === '' || $alamat === '' ||
        !in_array($status_aktif, [0, 1], true)) {
        $pesan = 'Semua data wajib diisi dengan benar.';
        $jenis_pesan = 'danger';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_siswa
                (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'ssssssi',
                $nis,
                $nisn,
                $nama,
                $jenis_kelamin,
                $tanggal_lahir,
                $alamat,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: siswa.php?status=tambah_sukses');
                exit;
            }
            mysqli_stmt_close($stmt);
        }

        $pesan = 'Data siswa gagal disimpan. Pastikan NIS/NISN tidak bermasalah dan periksa database.';
        $jenis_pesan = 'danger';
    }
}

/* =========================
   AMBIL DATA SISWA
========================= */
$query = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY id DESC");
if (!$query) {
    $pesan = 'Data siswa gagal dimuat. Periksa tabel t_siswa pada database.';
    $jenis_pesan = 'danger';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --white: #ffffff;
            --muted: #7890ad;
            --line: #dce9f8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background-color: #f3f7fc;
            color: #2f435a;
            font-family: "Segoe UI", Arial, sans-serif;
        }
        a { text-decoration: none; }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
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
        .brand-subtitle { font-size: 12px; color: var(--muted); }
        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }
        .menu { display: flex; flex-direction: column; gap: 3px; }
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
        .menu-link:hover { background: var(--blue-soft); color: var(--blue); }
        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }
        .logout { margin-top: auto; padding-top: 14px; border-top: 1px solid var(--line); }
        .logout .menu-link { color: #d9534f; }
        .logout .menu-link:hover { background: #fff0ef; color: #c03935; }
        .main { margin-left: 282px; min-height: 100vh; padding: 24px 28px 40px; }
        .container { width: 100%; max-width: none !important; margin: 0 auto; padding: 0 !important; }
        .judul, h4 { color: #234e78; }
        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35,78,120,.05) !important;
        }
        .card-body { padding: 24px; }
        .form-label { color: #405873; font-weight: 600; }
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
        .btn-danger {
            --bs-btn-color: #fff;
            --bs-btn-bg: #cf8888;
            --bs-btn-border-color: #cf8888;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #bd7777;
            --bs-btn-hover-border-color: #bd7777;
            border-radius: 6px;
        }
        .btn-sm { padding: 5px 11px; }
        .table { color: #344b63; vertical-align: middle; margin-bottom: 0; }
        .table thead th {
            background-color: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
        }
        .table tbody td { border-color: #e5edf5; padding: 11px 12px; }
        .table-hover tbody tr:hover > * { --bs-table-accent-bg: #f4f8fc; }
        .status-aktif, .status-nonaktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-aktif { background: #e4f3e9; color: #34734b; }
        .status-nonaktif { background: #fce9e9; color: #a34f4f; }
        .keterangan { color: #71849a; font-size: 14px; }
        @media (max-width: 900px) {
            .sidebar { position: relative; width: 100%; height: auto; min-height: auto; }
            .main { margin-left: 0; padding: 18px 14px 30px; }
            .logout { margin-top: 14px; }
        }
        @media (max-width: 576px) {
            .card-body { padding: 17px; }
        }
    </style>
</head>
<body>
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>
    <div class="menu-title">Menu Utama</div>
    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru.php" class="menu-link">Kelola Guru</a>
        <a href="siswa.php" class="menu-link active">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>
    <div class="logout"><a href="../logout.php" class="menu-link">Logout</a></div>
</aside>

<!-- KONTEN UTAMA -->
<main class="main">
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <a href="../dashboard.php" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        <button type="button" class="btn btn-primary" id="btnToggleSiswa"
                aria-expanded="<?= $tampilkan_form ? 'true' : 'false'; ?>"
                aria-controls="formTambahSiswa">+ Tambah Data Siswa</button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Kelola Siswa</h2>
            <p class="text-muted mb-0">Tambah dan kelola data siswa.</p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show" role="alert">
            <?= e($pesan); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- FORM TAMBAH SISWA -->
    <div class="card mb-4 <?= $tampilkan_form ? '' : 'd-none'; ?>" id="formTambahSiswa">
        <div class="card-body">
            <h4 class="mb-4">Tambah Data Siswa</h4>
            <form method="POST" action="siswa.php">
                <div class="mb-3">
                    <label for="nis" class="form-label">NIS</label>
                    <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS siswa" value="<?= e($_POST['nis'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" id="nisn" name="nisn" class="form-control" placeholder="Masukkan NISN siswa" value="<?= e($_POST['nisn'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Siswa</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama siswa" value="<?= e($_POST['nama'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" <?= (($_POST['jenis_kelamin'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?= (($_POST['jenis_kelamin'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" value="<?= e($_POST['tanggal_lahir'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat siswa" required><?= e($_POST['alamat'] ?? ''); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="status_aktif" class="form-label">Status</label>
                    <select id="status_aktif" name="status_aktif" class="form-select" required>
                        <option value="1" <?= (($_POST['status_aktif'] ?? '1') === '1') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= (($_POST['status_aktif'] ?? '') === '0') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">Tambah Siswa</button>
                <button type="button" class="btn btn-secondary" id="btnTutupForm">Tutup Form</button>
            </form>
        </div>
    </div>

    <!-- TABEL DATA SISWA -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-1">Data Siswa</h4>
            <p class="keterangan mb-3">Daftar seluruh siswa yang terdaftar.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th style="width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($query && mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($siswa = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($siswa['nis']); ?></td>
                                <td><?= e($siswa['nisn']); ?></td>
                                <td><?= e($siswa['nama']); ?></td>
                                <td><?= ($siswa['jenis_kelamin'] === 'L') ? 'Laki-laki' : (($siswa['jenis_kelamin'] === 'P') ? 'Perempuan' : '-'); ?></td>
                                <td><?= e($siswa['tanggal_lahir']); ?></td>
                                <td><?= e($siswa['alamat']); ?></td>
                                <td>
                                    <?php if ((int)$siswa['status_aktif'] === 1): ?>
                                        <span class="status-aktif">Aktif</span>
                                    <?php else: ?>
                                        <span class="status-nonaktif">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="edit_siswa.php?id=<?= (int)$siswa['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="hapus_siswa.php?id=<?= (int)$siswa['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php elseif ($query): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data siswa.</td></tr>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-danger py-4">Data siswa gagal dimuat.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tombol = document.getElementById('btnToggleSiswa');
    const tombolTutup = document.getElementById('btnTutupForm');
    const form = document.getElementById('formTambahSiswa');

    function bukaForm() {
        form.classList.remove('d-none');
        tombol.textContent = 'Tutup Form';
        tombol.setAttribute('aria-expanded', 'true');
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function tutupForm() {
        form.classList.add('d-none');
        tombol.textContent = '+ Tambah Data Siswa';
        tombol.setAttribute('aria-expanded', 'false');
    }

    tombol.addEventListener('click', function () {
        if (form.classList.contains('d-none')) bukaForm();
        else tutupForm();
    });

    if (tombolTutup) tombolTutup.addEventListener('click', tutupForm);

    <?php if ($tampilkan_form): ?>
    tombol.textContent = 'Tutup Form';
    tombol.setAttribute('aria-expanded', 'true');
    <?php endif; ?>
});
</script>
</body>
</html>
