<?php
include "../middleware/auth.php";
include "../config/koneksi.php";
if (($_SESSION['role'] ?? '') !== 'admin') { echo "Anda tidak memiliki akses"; exit; }
$pesan = '';
$jenis_pesan = 'success';
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO t_pelanggaran_kategori (nama, deskripsi, status_aktif) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssi', $nama, $deskripsi, $status_aktif);
    if (mysqli_stmt_execute($stmt)) { $pesan = 'Kategori pelanggaran berhasil disimpan!'; }
    else { $pesan = 'Kategori pelanggaran gagal disimpan: ' . mysqli_error($koneksi); $jenis_pesan = 'danger'; }
}
$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kelola Kategori Pelanggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<style>

:root { --blue:#2878cf; --blue-dark:#203e68; --blue-soft:#eaf3ff; --page:#f6f9fd; --white:#fff; --text:#263f63; --muted:#7890ad; --line:#dce9f8; --shadow:0 8px 22px rgba(32,62,104,.07); }
* { box-sizing:border-box; }
body { margin:0; background:var(--page); color:var(--text); font-family:"Segoe UI",Arial,sans-serif; }
a { text-decoration:none; }
.layout { min-height:100vh; }
.sidebar { position:fixed; left:0; top:0; width:282px; height:100vh; background:var(--white); border-right:1px solid var(--line); padding:25px 14px 16px; display:flex; flex-direction:column; z-index:1000; }
.brand { padding:0 15px 22px; border-bottom:1px solid var(--line); margin-bottom:22px; }
.brand-title { font-size:18px; font-weight:700; color:var(--blue-dark); margin-bottom:5px; }
.brand-subtitle { font-size:12px; color:var(--muted); }
.menu-title { font-size:10px; font-weight:700; color:#9aabc0; letter-spacing:1px; text-transform:uppercase; margin:0 15px 10px; }
.menu { display:flex; flex-direction:column; gap:3px; }
.menu-link { min-height:43px; display:flex; align-items:center; padding:10px 15px; border-radius:10px; color:#5e7695; font-size:13px; transition:.2s ease; }
.menu-link:hover { background:var(--blue-soft); color:var(--blue); }
.menu-link.active { background:var(--blue-soft); color:var(--blue); font-weight:600; box-shadow:inset 4px 0 0 var(--blue); }
.logout { margin-top:auto; padding-top:14px; border-top:1px solid var(--line); }
.logout .menu-link { color:#d9534f; }
.logout .menu-link:hover { background:#fff0ef; color:#c03935; }
.main { margin-left:282px; min-height:100vh; padding:24px 28px 40px; }
.container { width:100%; max-width:none !important; margin:0 auto; padding:0 !important; }
@media (max-width:900px) { .sidebar { position:static; width:100%; height:auto; } .main { margin-left:0; padding:18px 14px 30px; } }

.judul, h4 {

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

</style>
<style>
.btn-primary{--bs-btn-bg:#2878cf;--bs-btn-border-color:#2878cf;--bs-btn-hover-bg:#2068b5;--bs-btn-hover-border-color:#2068b5}
.table{color:#344b63;vertical-align:middle}.table thead th{background:#eaf2fa;color:#315b82;border-color:#dce7f1;padding:12px;white-space:nowrap}.table tbody td{border-color:#e5edf5;padding:11px 12px}
.alert{border-radius:10px}
</style></head><body>
<div class="layout"><aside class="sidebar"><div class="brand"><div class="brand-title">Sistem Pelanggaran</div><div class="brand-subtitle">SMK Muhammadiyah</div></div><div class="menu-title">Menu Utama</div><nav class="menu">
<a href="../dashboard.php" class="menu-link">Dashboard</a><a href="guru.php" class="menu-link">Kelola Guru</a><a href="siswa.php" class="menu-link">Kelola Siswa</a><a href="kelas.php" class="menu-link">Kelola Kelas</a><a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a><a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a><a href="wali_kelas.php" class="menu-link">Wali Kelas</a><a href="kategori_pelanggaran.php" class="menu-link active">Kategori Pelanggaran</a><a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a><a href="cetak_export.php" class="menu-link">Cetak / Export</a><a href="../about.php" class="menu-link">About Me</a></nav><div class="logout"><a href="../logout.php" class="menu-link">Logout</a></div></aside>
<main class="main"><div class="container py-4">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3"><a href="../dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a><button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formTambahKategori" aria-expanded="<?= $pesan !== '' ? 'true' : 'false'; ?>" aria-controls="formTambahKategori">+ Tambah Kategori Pelanggaran</button></div>
<div class="card mb-4"><div class="card-body"><h2 class="judul mb-1">Kelola Kategori Pelanggaran</h2><p class="text-muted mb-0">Kelola kategori pelanggaran siswa.</p></div></div>
<?php if ($pesan !== ''): ?><div class="alert alert-<?= htmlspecialchars($jenis_pesan); ?>"><?= htmlspecialchars($pesan); ?></div><?php endif; ?>
<div class="collapse <?= $pesan !== '' && $jenis_pesan === 'danger' ? 'show' : ''; ?>" id="formTambahKategori"><div class="card mb-4"><div class="card-body"><h4 class="mb-4">Tambah Kategori Pelanggaran</h4><form method="POST">
<div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama" class="form-control" placeholder="Contoh: Kedisiplinan" required></div>
<div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsi kategori pelanggaran" required></textarea></div>
<div class="mb-3"><label class="form-label">Status Aktif</label><select name="status_aktif" class="form-select" required><option value="1">Aktif</option><option value="0">Tidak Aktif</option></select></div>
<button type="submit" name="simpan" class="btn btn-primary">Simpan Kategori</button></form></div></div></div>
<div class="card"><div class="card-body"><h4 class="mb-3">Data Kategori Pelanggaran</h4><div class="table-responsive"><table class="table table-bordered table-hover mt-3"><thead><tr><th>No</th><th>Nama Kategori</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php $no=1; while ($data=mysqli_fetch_assoc($query)): ?><tr><td><?= $no++; ?></td><td><?= htmlspecialchars($data['nama']); ?></td><td><?= htmlspecialchars($data['deskripsi']); ?></td><td><?= $data['status_aktif']==1 ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Tidak Aktif</span>'; ?></td><td class="text-nowrap"><a href="edit_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>" class="btn btn-sm btn-primary">Edit</a> <a href="hapus_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a></td></tr><?php endwhile; ?>
</tbody></table></div></div></div></div></main></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script></body></html>
