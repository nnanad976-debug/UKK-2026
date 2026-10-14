<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$pesan = "";
$jenis_pesan = "";

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = (int) ($_POST['status_aktif'] ?? 0);

    if ($tanggal_mulai > $tanggal_selesai) {
        $pesan = "Tanggal mulai tidak boleh melewati tanggal selesai.";
        $jenis_pesan = "danger";
    } else {
        mysqli_begin_transaction($koneksi);
        try {
            if ($status_aktif === 1) {
                if (!mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET status_aktif = 0")) {
                    throw new Exception(mysqli_error($koneksi));
                }
            }
            $mulai = mysqli_real_escape_string($koneksi, $tanggal_mulai);
            $selesai = mysqli_real_escape_string($koneksi, $tanggal_selesai);
            $status = $status_aktif;
            $sql = "INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif) VALUES ('$nama', '$mulai', '$selesai', '$status')";
            if (!mysqli_query($koneksi, $sql)) {
                throw new Exception(mysqli_error($koneksi));
            }
            mysqli_commit($koneksi);
            header("Location: tahun_ajaran.php?berhasil=1");
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            $pesan = "Data tahun ajaran gagal disimpan. Periksa kembali data dan struktur tabel.";
            $jenis_pesan = "danger";
        }
    }
}

if (isset($_GET['berhasil'])) {
    $pesan = "Data tahun ajaran berhasil disimpan!";
    $jenis_pesan = "success";
}

$query = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
if (!$query) {
    die("Gagal mengambil data tahun ajaran: " . htmlspecialchars(mysqli_error($koneksi)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Tahun Ajaran</title>
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
        .logout .menu-link:hover { background: #fff0ef; color: #c03935; }

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



        .judul,

        h4 {

            color: #234e78;

        }



        .card {

            border: 1px solid #e1eaf4 !important;

            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(35,78,120,.05) !important;

        }



        .card-body {

            padding: 24px;

        }



        .form-label {

            color: #405873;

            font-weight: 600;

        }



        .form-control,

        .form-select {

            border-color: #d7e3ef;

            border-radius: 7px;

            padding: 10px 12px;

        }



        .form-control:focus,

        .form-select:focus {

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



        .btn-sm {

            padding: 5px 11px;

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



        .status-aktif {

            display: inline-block;

            background: #e4f3e9;

            color: #34734b;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;

        }



        .status-nonaktif {

            display: inline-block;

            background: #fce9e9;

            color: #a34f4f;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;

        }



        .keterangan {

            color: #71849a;

            font-size: 14px;

        }



        @media (max-width: 576px) {



            .container {

                padding-left: 14px;

                padding-right: 14px;

            }



            .card-body {

                padding: 17px;

            }



        }



        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .main {
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .logout { margin-top: 14px; }
        }

    
    
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="brand"><div class="brand-title">Sistem Pelanggaran</div><div class="brand-subtitle">SMK Muhammadiyah</div></div>
    <div class="menu-title">Menu Utama</div>
    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru.php" class="menu-link">Kelola Guru</a>
        <a href="siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link active">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>
    <div class="logout"><a href="../logout.php" class="menu-link">Logout</a></div>
</aside>
<main class="main">
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <a href="../dashboard.php" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        <button type="button" class="btn btn-primary" id="btnToggleTahun" aria-expanded="false" aria-controls="formTambahTahun">+ Tambah Tahun Ajaran</button>
    </div>
    <div class="card mb-4"><div class="card-body"><h2 class="judul mb-1">Kelola Tahun Ajaran</h2><p class="text-muted mb-0">Kelola data tahun ajaran.</p></div></div>
    <div class="card mb-4 <?= ($pesan !== '' && $jenis_pesan === 'danger') ? '' : 'd-none'; ?>" id="formTambahTahun">
        <div class="card-body"><h4 class="mb-4">Tambah Tahun Ajaran</h4>
            <?php if ($pesan !== '') { ?><div class="alert alert-<?= htmlspecialchars($jenis_pesan); ?>"><?= htmlspecialchars($pesan); ?></div><?php } ?>
            <form method="POST" action="tahun_ajaran.php">
                <div class="mb-3"><label class="form-label">Tahun Ajaran</label><input type="text" name="nama" class="form-control" placeholder="Contoh: 2026/2027" required></div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control" required></div>
                <div class="mb-4"><label class="form-label">Status</label><select name="status_aktif" class="form-select" required><option value="1">Aktif</option><option value="0">Tidak Aktif</option></select></div>
                <button type="submit" name="simpan" class="btn btn-primary">Tambah Tahun Ajaran</button>
            </form>
        </div>
    </div>
    <div class="card"><div class="card-body"><h4 class="mb-1">Data Tahun Ajaran</h4><p class="keterangan mb-3">Daftar seluruh tahun ajaran yang terdaftar.</p>
        <div class="table-responsive"><table class="table table-bordered table-hover align-middle">
            <thead><tr><th style="width:60px">No</th><th>Tahun Ajaran</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $no = 1; while ($tahun = mysqli_fetch_assoc($query)) { ?>
                <tr><td><?= $no++; ?></td><td><?= htmlspecialchars($tahun['nama']); ?></td><td><?= htmlspecialchars($tahun['tanggal_mulai']); ?></td><td><?= htmlspecialchars($tahun['tanggal_selesai']); ?></td>
                <td><?php if ((int)$tahun['status_aktif'] === 1) { ?><span class="status-aktif">Aktif</span><?php } else { ?><span class="status-nonaktif">Tidak Aktif</span><?php } ?></td>
                <td><div class="d-flex gap-2"><a href="edit_tahun_ajaran.php?id=<?= (int)$tahun['id']; ?>" class="btn btn-sm btn-primary">Edit</a><a href="hapus_tahun_ajaran.php?id=<?= (int)$tahun['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">Hapus</a></div></td></tr>
            <?php } ?>
            <?php if (mysqli_num_rows($query) === 0) { ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada data tahun ajaran.</td></tr><?php } ?>
            </tbody></table></div>
    </div></div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tombol = document.getElementById('btnToggleTahun');
    const form = document.getElementById('formTambahTahun');
    if (!tombol || !form) return;
    tombol.addEventListener('click', function () {
        const tersembunyi = form.classList.contains('d-none');
        if (tersembunyi) {
            form.classList.remove('d-none'); tombol.textContent = 'Tutup Form'; tombol.setAttribute('aria-expanded', 'true');
            form.scrollIntoView({behavior: 'smooth', block: 'start'});
        } else {
            form.classList.add('d-none'); tombol.textContent = '+ Tambah Tahun Ajaran'; tombol.setAttribute('aria-expanded', 'false');
        }
    });
    if (!form.classList.contains('d-none')) { tombol.textContent = 'Tutup Form'; tombol.setAttribute('aria-expanded', 'true'); }
});
</script>
</body>
</html>
