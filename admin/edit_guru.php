```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') != 'admin') {
    exit("Anda tidak memiliki akses");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: guru.php");
    exit;
}

$id = (int) $_GET['id'];
$pesan = "";

$query = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id = $id");
$guru = mysqli_fetch_assoc($query);

if (!$guru) {
    exit("Data guru tidak ditemukan.");
}

if (isset($_POST['update'])) {
    $nip = mysqli_real_escape_string($koneksi, trim($_POST['nip']));
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $status_aktif = (int) $_POST['status_aktif'];

    $update = mysqli_query($koneksi, "
        UPDATE t_guru SET
            nip = '$nip',
            nama = '$nama',
            email = '$email',
            status_aktif = '$status_aktif'
        WHERE id = $id
    ");

    if ($update) {
        header("Location: guru.php");
        exit;
    } else {
        $pesan = "Data guru gagal diperbarui!";
    }

    // Pertahankan data yang diketik jika gagal
    $guru['nip'] = $_POST['nip'];
    $guru['nama'] = $_POST['nama'];
    $guru['email'] = $_POST['email'];
    $guru['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Guru</title>

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

.sidebar {
    position:fixed;
    left:0;
    top:0;
    width:282px;
    height:100vh;
    overflow-y:auto;
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

.container-edit {
    width:100%;
    max-width:none;
    margin:0 auto;
}

.card {
    border:1px solid #e1eaf4;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(35,78,120,.05);
}

.card-body {
    padding:24px;
}

.judul,
h4 {
    color:#234e78;
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
}

.form-control:focus,
.form-select:focus {
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
        <a href="guru.php" class="menu-link active">Kelola Guru</a>
        <a href="siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<!-- KONTEN EDIT GURU -->
<main class="main">
    <div class="container-edit">

        <div class="mb-3">
            <a href="guru.php" class="btn btn-secondary">
                Kembali ke Data Guru
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Edit Data Guru</h2>
                <p class="text-muted mb-0">
                    Perbarui informasi guru yang terdaftar.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <h4 class="mb-4">Form Edit Guru</h4>

                <?php if ($pesan != ''): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($pesan) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip"
                               class="form-control"
                               value="<?= htmlspecialchars($guru['nip'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Guru</label>
                        <input type="text" name="nama"
                               class="form-control"
                               value="<?= htmlspecialchars($guru['nama'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="<?= htmlspecialchars($guru['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status_aktif" class="form-select" required>
                            <option value="1"
                                <?= ($guru['status_aktif'] == 1) ? 'selected' : '' ?>>
                                Aktif
                            </option>
                            <option value="0"
                                <?= ($guru['status_aktif'] == 0) ? 'selected' : '' ?>>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="guru.php" class="btn btn-secondary">
                        Batal
                    </a>

                </form>
            </div>
        </div>

    </div>
</main>

</body>
</html>