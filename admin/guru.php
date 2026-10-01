
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$pesan = "";
$jenis_pesan = "";

/* =========================
   PROSES TAMBAH GURU
========================= */
if (isset($_POST['simpan'])) {
    $nip = mysqli_real_escape_string($koneksi, trim($_POST['nip']));
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $status_aktif = (int) $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_guru (nip, nama, email, status_aktif)
         VALUES ('$nip', '$nama', '$email', '$status_aktif')"
    );

    if ($query_simpan) {
        echo "<script>
            alert('Data guru berhasil disimpan!');
            window.location.href = 'kelola_guru.php';
        </script>";
        exit;
    } else {
        $pesan = "Data guru gagal disimpan!";
        $jenis_pesan = "danger";
    }
}

/* =========================
   DATA GURU
========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_guru
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Guru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f3f7fc;
    color: #2f435a;
    font-family: "Segoe UI", Arial, sans-serif;
}

.container {
    max-width: 1120px;
}

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
</head>

<body>
<div class="container py-4">

    <a href="../dashboard.php" class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <!-- JUDUL -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Kelola Guru</h2>
            <p class="text-muted mb-0">
                Tambah dan kelola data guru.
            </p>
        </div>
    </div>

    <!-- FORM TAMBAH GURU -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-4">Tambah Data Guru</h4>

            <?php if ($pesan != "") { ?>
                <div class="alert alert-<?= $jenis_pesan; ?>">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">NIP</label>
                    <input
                        type="text"
                        name="nip"
                        class="form-control"
                        placeholder="Masukkan NIP guru"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Guru</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama guru"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email guru"
                        required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select
                        name="status_aktif"
                        class="form-select"
                        required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary">
                    Tambah Guru
                </button>
            </form>
        </div>
    </div>

    <!-- TABEL DATA GURU -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-1">Data Guru</h4>
            <p class="keterangan mb-3">
                Daftar seluruh guru yang terdaftar.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;
                    while ($guru = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($guru['nip']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['nama']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['email']); ?>
                            </td>

                            <td>
                                <?php if ($guru['status_aktif'] == 1) { ?>
                                    <span class="status-aktif">Aktif</span>
                                <?php } else { ?>
                                    <span class="status-nonaktif">Tidak Aktif</span>
                                <?php } ?>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a
                                        href="edit_guru.php?id=<?= (int) $guru['id']; ?>"
                                        class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <a
                                        href="hapus_guru.php?id=<?= (int) $guru['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus guru ini?')">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if (mysqli_num_rows($query) == 0) { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data guru.
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>