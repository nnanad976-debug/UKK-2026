```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Anda tidak memiliki akses");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$batas_kelas = [
    'TKR' => ['X' => 5, 'XI' => 4, 'XII' => 5],
    'TSM' => ['X' => 3, 'XI' => 3, 'XII' => 3],
    'TKJ' => ['X' => 4, 'XI' => 4, 'XII' => 4],
    'RPL' => ['X' => 2, 'XI' => 1, 'XII' => 2],
    'BD'  => ['X' => 1, 'XI' => 1, 'XII' => 1]
];

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: kelas.php");
    exit;
}

$id = (int) $_GET['id'];

$hasil = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas WHERE id = $id LIMIT 1"
);

if (!$hasil || mysqli_num_rows($hasil) === 0) {
    header("Location: kelas.php");
    exit;
}

$kelas = mysqli_fetch_assoc($hasil);

$nama_awal = $kelas['nama'];
$tingkat = $kelas['tingkat'];
$jurusan = $kelas['jurusan'];
$nomor = (int) substr(
    $nama_awal,
    strrpos($nama_awal, ' ') + 1
);
$status_aktif = (int) $kelas['status_aktif'];

$pesan = "";
$jenis_pesan = "";

if (isset($_POST['simpan'])) {
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $nomor = filter_var(
        $_POST['nomor'] ?? '',
        FILTER_VALIDATE_INT
    );
    $status_aktif = filter_var(
        $_POST['status_aktif'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (
        !in_array($tingkat, ['X', 'XI', 'XII'], true) ||
        !isset($batas_kelas[$jurusan][$tingkat]) ||
        $nomor === false ||
        $nomor === null ||
        $nomor < 1 ||
        $nomor > $batas_kelas[$jurusan][$tingkat] ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Data kelas tidak valid. Periksa kembali isian kamu.";
        $jenis_pesan = "danger";
    } else {
        $nama = "$tingkat $jurusan $nomor";

        $nama_db = mysqli_real_escape_string($koneksi, $nama);
        $tingkat_db = mysqli_real_escape_string($koneksi, $tingkat);
        $jurusan_db = mysqli_real_escape_string($koneksi, $jurusan);

        $cek = mysqli_query(
            $koneksi,
            "SELECT id FROM t_kelas
             WHERE nama = '$nama_db'
             AND id != $id
             LIMIT 1"
        );

        if (!$cek) {
            $pesan = "Gagal memeriksa nama kelas.";
            $jenis_pesan = "danger";
        } elseif (mysqli_num_rows($cek) > 0) {
            $pesan = "Kelas $nama sudah terdaftar.";
            $jenis_pesan = "danger";
        } else {
            $update = mysqli_query(
                $koneksi,
                "UPDATE t_kelas
                 SET nama = '$nama_db',
                     tingkat = '$tingkat_db',
                     jurusan = '$jurusan_db',
                     status_aktif = $status_aktif
                 WHERE id = $id"
            );

            if ($update) {
                header("Location: kelas.php?berhasil_edit=1");
                exit;
            } else {
                $pesan = "Data kelas gagal diperbarui: " .
                    mysqli_error($koneksi);
                $jenis_pesan = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Kelas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --muted: #7890ad;
    --line: #dce9f8;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f3f7fc;
    color: #2f435a;
    font-family: "Segoe UI", Arial, sans-serif;
}

a {
    text-decoration: none;
}

.sidebar {
    position: fixed;
    inset: 0 auto 0 0;
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

.menu-link:hover,
.menu-link.active {
    background: var(--blue-soft);
    color: var(--blue);
}

.menu-link.active {
    font-weight: 600;
    box-shadow: inset 4px 0 0 var(--blue);
}

.logout {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid var(--line);
}

.logout .menu-link {
    color: #d9534f;
}

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

    .logout {
        margin-top: 14px;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 17px;
    }
}
</style>
</head>

<body>

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
        <a href="kelas.php" class="menu-link active">Kelola Kelas</a>
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

<main class="main">
<div class="container py-4">

    <div class="mb-4">
        <a href="kelas.php" class="btn btn-secondary">
            &larr; Kembali ke Kelola Kelas
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Edit Data Kelas</h2>
            <p class="text-muted mb-0">
                Perbarui informasi kelas di bawah ini.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <?php if ($pesan !== '') { ?>
                <div class="alert alert-<?= e($jenis_pesan); ?>">
                    <?= e($pesan); ?>
                </div>
            <?php } ?>

            <form method="POST" action="edit_kelas.php?id=<?= $id; ?>">

                <div class="mb-3">
                    <label class="form-label">Tingkat</label>
                    <select name="tingkat" class="form-select" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <?php foreach (['X', 'XI', 'XII'] as $pilihan) { ?>
                            <option value="<?= e($pilihan); ?>"
                                <?= $tingkat === $pilihan ? 'selected' : ''; ?>>
                                <?= e($pilihan); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach (['TKR', 'TSM', 'TKJ', 'RPL', 'BD'] as $pilihan) { ?>
                            <option value="<?= e($pilihan); ?>"
                                <?= $jurusan === $pilihan ? 'selected' : ''; ?>>
                                <?= e($pilihan); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor Kelas</label>
                    <input
                        type="number"
                        name="nomor"
                        class="form-control"
                        min="1"
                        max="5"
                        value="<?= e($nomor); ?>"
                        required
                    >
                    <small class="text-muted">
                        Nama kelas dibuat otomatis, misalnya X TKR 1.
                    </small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1"
                            <?= $status_aktif === 1 ? 'selected' : ''; ?>>
                            Aktif
                        </option>
                        <option value="0"
                            <?= $status_aktif === 0 ? 'selected' : ''; ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="kelas.php" class="btn btn-secondary">
                    Batal
                </a>

            </form>
        </div>
    </div>

</div>
</main>

</body>
</html>
```