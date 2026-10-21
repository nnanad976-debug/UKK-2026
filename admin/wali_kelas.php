<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    exit("Anda tidak memiliki akses");
}

$pesan = "";
$jenis_pesan = "";

function e($nilai) {
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['simpan'])) {
    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $guru_id = (int)($_POST['guru_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    if ($tahun_ajaran_id <= 0 || $kelas_id <= 0 || $guru_id <= 0 || $tanggal_mulai === '') {
        $pesan = "Semua data wajib diisi dengan benar.";
        $jenis_pesan = "danger";
    } elseif ($tanggal_selesai !== '' && $tanggal_selesai < $tanggal_mulai) {
        $pesan = "Tanggal selesai tidak boleh lebih awal dari tanggal mulai.";
        $jenis_pesan = "danger";
    } else {
        $cek_tahun = mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE id = $tahun_ajaran_id LIMIT 1");
        $cek_kelas = mysqli_query($koneksi, "SELECT id FROM t_kelas WHERE id = $kelas_id AND status_aktif = 1 LIMIT 1");
        $cek_guru = mysqli_query($koneksi, "SELECT id FROM t_guru WHERE id = $guru_id AND status_aktif = 1 LIMIT 1");

        if (!$cek_tahun || !$cek_kelas || !$cek_guru) {
            $pesan = "Gagal memeriksa data: " . mysqli_error($koneksi);
            $jenis_pesan = "danger";
        } elseif (mysqli_num_rows($cek_tahun) === 0 || mysqli_num_rows($cek_kelas) === 0 || mysqli_num_rows($cek_guru) === 0) {
            $pesan = "Tahun ajaran, kelas, atau guru tidak valid.";
            $jenis_pesan = "danger";
        } else {
            $mulai = mysqli_real_escape_string($koneksi, $tanggal_mulai);
            $selesai = $tanggal_selesai === '' ? "NULL" : "'" . mysqli_real_escape_string($koneksi, $tanggal_selesai) . "'";

            $sql = "INSERT INTO t_wali_kelas
                    (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai, tanggal_selesai, status_aktif)
                    VALUES ($tahun_ajaran_id, $kelas_id, $guru_id, '$mulai', $selesai, 1)";

            if (mysqli_query($koneksi, $sql)) {
                header("Location: wali_kelas.php?berhasil=1");
                exit;
            }
            $pesan = "Data wali kelas gagal disimpan: " . mysqli_error($koneksi);
            $jenis_pesan = "danger";
        }
    }
}

if (isset($_GET['berhasil'])) {
    $pesan = "Data wali kelas berhasil disimpan!";
    $jenis_pesan = "success";
}

$query_tahun = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY nama DESC");
$query_kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE status_aktif = 1 ORDER BY tingkat ASC, jurusan ASC, nama ASC");
$query_guru = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE status_aktif = 1 ORDER BY nama ASC");
$query = mysqli_query($koneksi, "SELECT wk.id, ta.nama AS tahun_ajaran, k.nama AS nama_kelas, k.tingkat, k.jurusan, g.nip, g.nama AS nama_guru, wk.tanggal_mulai, wk.tanggal_selesai, wk.status_aktif
    FROM t_wali_kelas wk
    INNER JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id
    INNER JOIN t_kelas k ON wk.kelas_id = k.id
    INNER JOIN t_guru g ON wk.guru_id = g.id
    ORDER BY wk.id DESC");

if (!$query_tahun || !$query_kelas || !$query_guru || !$query) {
    die("Gagal mengambil data: " . e(mysqli_error($koneksi)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Wali Kelas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root{--blue:#2878cf;--blue-dark:#203e68;--blue-soft:#eaf3ff;--muted:#7890ad;--line:#dce9f8}
*{box-sizing:border-box}
body{margin:0;background:#f6f9fd;color:#2f435a;font-family:"Segoe UI",Arial,sans-serif;font-size:14px}
a{text-decoration:none}
.sidebar{position:fixed;top:0;left:0;width:282px;height:100vh;overflow-y:auto;background:#fff;border-right:1px solid var(--line);padding:25px 14px 16px;display:flex;flex-direction:column;z-index:1000}
.brand{padding:0 15px 22px;border-bottom:1px solid var(--line);margin-bottom:22px}
.brand-title{font-size:18px;font-weight:700;color:var(--blue-dark);margin-bottom:5px}
.brand-subtitle{font-size:12px;color:var(--muted)}
.menu-title{font-size:10px;font-weight:700;color:#9aabc0;letter-spacing:1px;text-transform:uppercase;margin:0 15px 10px}
.menu{display:flex;flex-direction:column;gap:3px}
.menu-link{min-height:43px;display:flex;align-items:center;padding:10px 15px;border-radius:10px;color:#5e7695;font-size:13px;transition:.2s ease}
.menu-link:hover{background:var(--blue-soft);color:var(--blue)}
.menu-link.active{background:var(--blue-soft);color:var(--blue);font-weight:600;box-shadow:inset 4px 0 0 var(--blue)}
.logout{margin-top:auto;padding-top:14px;border-top:1px solid var(--line)}
.logout .menu-link{color:#d9534f}
.logout .menu-link:hover{background:#fff0ef;color:#c03935}
.main{margin-left:282px;min-height:100vh;padding:24px 28px 40px}
.container{width:100%;max-width:none!important;padding:0!important}
.judul,h4{color:#234e78}
.judul{font-size:24px;font-weight:700}
.card{background:#fff;border:1px solid #e1eaf4!important;border-radius:10px;box-shadow:0 3px 12px rgba(35,78,120,.05)!important}
.card-body{padding:24px}
.form-label{color:#405873;font-weight:600}
.form-control,.form-select{border-color:#d7e3ef;border-radius:7px;padding:10px 12px;color:#344b63}
.form-control:focus,.form-select:focus{border-color:#8eb5d8;box-shadow:0 0 0 .2rem rgba(87,143,190,.14)}
.btn{font-size:13px;font-weight:500}
.btn-primary{--bs-btn-color:#fff;--bs-btn-bg:#2878cf;--bs-btn-border-color:#2878cf;--bs-btn-hover-color:#fff;--bs-btn-hover-bg:#2168b5;--bs-btn-hover-border-color:#2168b5;border-radius:7px;padding:9px 15px}
.btn-secondary{--bs-btn-bg:#e8f0f8;--bs-btn-border-color:#d8e5f1;--bs-btn-color:#426584;--bs-btn-hover-bg:#dceaf6;--bs-btn-hover-border-color:#cbddeb;--bs-btn-hover-color:#234e78;border-radius:7px;padding:9px 15px}
.table-responsive{width:100%;overflow-x:auto}
.table{color:#344b63;vertical-align:middle;margin-bottom:0;font-size:14px!important}
.table thead th{background:#eaf2fa;color:#315b82;border-color:#dce7f1;font-weight:600;padding:15px 12px!important;font-size:14px!important;white-space:nowrap}
.table tbody td{border-color:#e5edf5;padding:15px 12px!important;font-size:14px!important}
.table-hover tbody tr:hover>*{--bs-table-bg:#f4f8fc}
div.dt-container{width:100%;color:#344b63;font-size:14px!important}
div.dt-container .dt-length,div.dt-container .dt-search{margin-bottom:18px}
div.dt-container .dt-length label,div.dt-container .dt-search label{color:#526a84}
div.dt-container .dt-input{border:1px solid #d7e3ef;border-radius:6px;padding:7px 10px;color:#344b63;background:#fff}
div.dt-container .dt-input:focus{border-color:#8eb5d8;outline:none;box-shadow:0 0 0 .15rem rgba(87,143,190,.12)}
div.dt-container .dt-search input{margin-left:6px}
div.dt-container .dt-length select{margin:0 5px}
div.dt-container .dt-info{padding-top:15px;color:#71849a}
div.dt-container .dt-paging{padding-top:12px}
div.dt-container .dt-paging .dt-paging-button{border-radius:5px!important;padding:6px 11px;margin-left:3px}
div.dt-container .dt-paging .dt-paging-button.current{background:#2878cf!important;border-color:#2878cf!important;color:#fff!important}
div.dt-container .dt-paging .dt-paging-button:hover{background:#eaf3ff!important;border-color:#dce9f8!important;color:#2878cf!important}
.status-aktif,.status-nonaktif{display:inline-block;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap}
.status-aktif{background:#e4f3e9;color:#34734b}
.status-nonaktif{background:#fce9e9;color:#a34f4f}
.keterangan{color:#71849a;font-size:13px}
.alert{border-radius:8px;font-size:13px}
#formTambahWali{scroll-margin-top:20px}
@media(max-width:900px){.sidebar{position:relative;width:100%;height:auto;min-height:auto}.main{margin-left:0;padding:18px 14px 30px}.logout{margin-top:14px}}
@media(max-width:576px){.main{padding:14px 10px 25px}.card-body{padding:17px}.judul{font-size:21px}div.dt-container .dt-search{margin-top:10px}div.dt-container .dt-search input{max-width:150px}div.dt-container .dt-paging{overflow-x:auto}}
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
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link active">Wali Kelas</a>
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
        <button type="button" class="btn btn-primary" id="btnToggleWali" aria-expanded="<?= ($jenis_pesan === 'danger') ? 'true' : 'false'; ?>" aria-controls="formTambahWali">+ Tambah Wali Kelas</button>
    </div>

    <div class="card mb-4"><div class="card-body">
        <h2 class="judul mb-1">Kelola Wali Kelas</h2>
        <p class="text-muted mb-0">Tambah dan kelola data wali kelas.</p>
    </div></div>

    <?php if ($pesan !== '' && $jenis_pesan === 'success') { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= e($pesan); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
    <?php } ?>

    <div class="card mb-4 <?= ($jenis_pesan === 'danger') ? '' : 'd-none'; ?>" id="formTambahWali">
        <div class="card-body">
            <h4 class="mb-4">Tambah Wali Kelas</h4>
            <?php if ($pesan !== '' && $jenis_pesan === 'danger') { ?><div class="alert alert-danger"><?= e($pesan); ?></div><?php } ?>
            <form method="POST" action="wali_kelas.php">
                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-select" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php while ($ta = mysqli_fetch_assoc($query_tahun)) { ?><option value="<?= (int)$ta['id']; ?>"><?= e($ta['nama']); ?></option><?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = mysqli_fetch_assoc($query_kelas)) { ?><option value="<?= (int)$k['id']; ?>"><?= e($k['nama'].' - '.$k['tingkat'].' - '.$k['jurusan']); ?></option><?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Guru</label>
                    <select name="guru_id" class="form-select" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php while ($g = mysqli_fetch_assoc($query_guru)) { ?><option value="<?= (int)$g['id']; ?>"><?= e(($g['nip'] ?? '-').' - '.$g['nama']); ?></option><?php } ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" name="tanggal_mulai" value="2026-07-01" class="form-control" required></div>
                <div class="mb-4"><label class="form-label">Tanggal Selesai (opsional)</label><input type="date" name="tanggal_selesai" value="2027-06-30" class="form-control"></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Wali Kelas</button>
                <button type="button" id="btnBatalWali" class="btn btn-secondary">Batal</button>
            </form>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <h4 class="mb-1">Data Wali Kelas</h4>
        <p class="keterangan mb-3">Daftar seluruh wali kelas yang terdaftar.</p>
        <div class="table-responsive">
            <table id="tabelWaliKelas" class="table table-bordered table-hover align-middle w-100">
                <thead><tr><th>No</th><th>Tahun Ajaran</th><th>Kelas</th><th>Tingkat</th><th>Jurusan</th><th>NIP</th><th>Nama Guru</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Status</th></tr></thead>
                <tbody>
                <?php $no=1; while ($d=mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?= $no++; ?></td><td><?= e($d['tahun_ajaran']); ?></td><td><?= e($d['nama_kelas']); ?></td><td><?= e($d['tingkat']); ?></td><td><?= e($d['jurusan']); ?></td><td><?= e($d['nip'] ?? '-'); ?></td><td><?= e($d['nama_guru']); ?></td><td><?= e($d['tanggal_mulai']); ?></td><td><?= e($d['tanggal_selesai'] ?? '-'); ?></td>
                        <td><?php if ((int)$d['status_aktif'] === 1) { ?><span class="status-aktif">Aktif</span><?php } else { ?><span class="status-nonaktif">Tidak Aktif</span><?php } ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div></div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tombol = document.getElementById('btnToggleWali');
    const batal = document.getElementById('btnBatalWali');
    const form = document.getElementById('formTambahWali');

    function bukaForm() {
        form.classList.remove('d-none');
        tombol.textContent = 'Tutup Form';
        tombol.setAttribute('aria-expanded', 'true');
        form.scrollIntoView({behavior: 'smooth', block: 'start'});
    }
    function tutupForm() {
        form.classList.add('d-none');
        tombol.textContent = '+ Tambah Wali Kelas';
        tombol.setAttribute('aria-expanded', 'false');
    }
    tombol.addEventListener('click', function () {
        form.classList.contains('d-none') ? bukaForm() : tutupForm();
    });
    batal.addEventListener('click', tutupForm);
    if (!form.classList.contains('d-none')) {
        tombol.textContent = 'Tutup Form';
        tombol.setAttribute('aria-expanded', 'true');
    }
});

$(document).ready(function () {
    $('#tabelWaliKelas').DataTable({
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
            emptyTable: 'Belum ada data wali kelas',
            paginate: {first: '«', previous: 'Sebelumnya', next: 'Berikutnya', last: '»'}
        }
    });
});
</script>
</body>
</html>
