```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: jenis_pelanggaran.php");
    exit;
}

$pesan = "";
$jenis_pesan = "danger";

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: jenis_pelanggaran.php");
    exit;
}

if (isset($_POST['update'])) {
    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = filter_var($_POST['poin'] ?? null, FILTER_VALIDATE_INT);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    $data['pelanggaran_kategori_id'] = $kategori_id;
    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['poin'] = $poin;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;

    if (
        $kategori_id <= 0 ||
        $kode === '' ||
        $nama === '' ||
        $deskripsi === '' ||
        $poin === false ||
        $poin === null ||
        $poin < 0 ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Semua kolom wajib diisi dengan data yang benar.";
    } else {
        $cekKategori = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori
             WHERE id = ? AND status_aktif = 1 LIMIT 1"
        );

        mysqli_stmt_bind_param($cekKategori, "i", $kategori_id);
        mysqli_stmt_execute($cekKategori);
        $hasilKategori = mysqli_stmt_get_result($cekKategori);
        $kategoriValid = mysqli_num_rows($hasilKategori) > 0;
        mysqli_stmt_close($cekKategori);

        if (!$kategoriValid) {
            $pesan = "Kategori tidak ditemukan atau sudah tidak aktif.";
        } else {
            $cekKode = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_pelanggaran
                 WHERE kode = ? AND id <> ? LIMIT 1"
            );

            mysqli_stmt_bind_param($cekKode, "si", $kode, $id);
            mysqli_stmt_execute($cekKode);
            $hasilKode = mysqli_stmt_get_result($cekKode);
            $kodeAda = mysqli_num_rows($hasilKode) > 0;
            mysqli_stmt_close($cekKode);

            if ($kodeAda) {
                $pesan = "Kode pelanggaran sudah digunakan oleh data lain.";
            } else {
                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE t_pelanggaran
                     SET pelanggaran_kategori_id = ?,
                         kode = ?,
                         nama = ?,
                         poin = ?,
                         deskripsi = ?,
                         status_aktif = ?
                     WHERE id = ?"
                );

                if ($update) {
                    mysqli_stmt_bind_param(
                        $update,
                        "issisii",
                        $kategori_id,
                        $kode,
                        $nama,
                        $poin,
                        $deskripsi,
                        $status_aktif,
                        $id
                    );

                    if (mysqli_stmt_execute($update)) {
                        mysqli_stmt_close($update);
                        header("Location: jenis_pelanggaran.php?diubah=1");
                        exit;
                    }

                    $pesan = "Perubahan gagal disimpan. Periksa kembali data.";
                    mysqli_stmt_close($update);
                } else {
                    $pesan = "Gagal menyiapkan perubahan data.";
                }
            }
        }
    }
}

$queryKategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
        OR id = " . (int)$data['pelanggaran_kategori_id'] . "
     ORDER BY nama ASC"
);

if (!$queryKategori) {
    die("Gagal mengambil data kategori.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Jenis Pelanggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --blue:#2878cf;
    --blue-dark:#203e68;
    --blue-soft:#eaf3ff;
    --page:#f3f7fc;
    --line:#dce9f8;
}
* { box-sizing:border-box; }
body {
    margin:0;
    background:var(--page);
    color:#2f435a;
    font-family:"Segoe UI",Arial,sans-serif;
}
a { text-decoration:none; }
.sidebar {
    position:fixed;
    left:0;
    top:0;
    width:282px;
    min-width:282px;
    height:100vh;
    overflow-y:auto;
    background:#fff;
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
.brand-subtitle { font-size:12px; color:#7890ad; }
.menu-title {
    font-size:10px;
    font-weight:700;
    color:#9aabc0;
    letter-spacing:1px;
    text-transform:uppercase;
    margin:0 15px 10px;
}
.menu { display:flex; flex-direction:column; gap:3px; }
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
.menu-link:hover,.menu-link.active {
    background:var(--blue-soft);
    color:var(--blue);
}
.menu-link.active {
    font-weight:600;
    box-shadow:inset 4px 0 0 var(--blue);
}
.logout {
    margin-top:auto;
    padding-top:14px;
    border-top:1px solid var(--line);
}
.logout .menu-link { color:#d9534f; }
.logout .menu-link:hover { background:#fff0ef; color:#c03935; }
.main {
    margin-left:282px;
    min-height:100vh;
    padding:24px 28px 40px;
}
.card {
    border:1px solid #e1eaf4!important;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(35,78,120,.05)!important;
}
.card-body { padding:24px; }
.judul,h4 { color:#234e78; }
.judul { font-size:28px; font-weight:600; }
.form-label { color:#405873; font-weight:600; font-size:14px; }
.form-control,.form-select {
    border-color:#d7e3ef;
    border-radius:7px;
    padding:10px 12px;
}
.form-control:focus,.form-select:focus {
    border-color:#8eb5d8;
    box-shadow:0 0 0 .2rem rgba(87,143,190,.14);
}
.btn { border-radius:7px; }
.btn-primary {
    --bs-btn-color:#fff;
    --bs-btn-bg:#5f91ba;
    --bs-btn-border-color:#5f91ba;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#4b7fa9;
    --bs-btn-hover-border-color:#4b7fa9;
    padding:8px 15px;
}
.btn-secondary {
    --bs-btn-bg:#e8f0f8;
    --bs-btn-border-color:#d8e5f1;
    --bs-btn-color:#426584;
    --bs-btn-hover-bg:#dceaf6;
    --bs-btn-hover-border-color:#cbddeb;
    --bs-btn-hover-color:#234e78;
}
@media(max-width:900px) {
    .sidebar { position:relative; width:100%; min-width:100%; height:auto; }
    .main { margin-left:0; padding:18px 14px 30px; }
    .logout { margin-top:14px; }
}
@media(max-width:576px) {
    .card-body { padding:17px; }
    .judul { font-size:24px; }
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
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link active">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>
    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">
    <a href="jenis_pelanggaran.php" class="btn btn-secondary mb-4">&larr; Kembali ke Jenis Pelanggaran</a>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Edit Jenis Pelanggaran</h2>
            <p class="text-muted mb-0">Perbarui informasi jenis pelanggaran.</p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show">
            <?= e($pesan); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="edit_jenis_pelanggaran.php?id=<?= (int)$id; ?>" autocomplete="off">

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori Pelanggaran</label>
                    <select name="pelanggaran_kategori_id" id="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($kategori = mysqli_fetch_assoc($queryKategori)): ?>
                            <option value="<?= (int)$kategori['id']; ?>"
                                <?= ((string)$data['pelanggaran_kategori_id'] === (string)$kategori['id']) ? 'selected' : ''; ?>>
                                <?= e($kategori['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode" class="form-label">Kode Pelanggaran</label>
                        <input type="text" name="kode" id="kode" class="form-control"
                            maxlength="50" value="<?= e($data['kode']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Pelanggaran</label>
                        <input type="text" name="nama" id="nama" class="form-control"
                            value="<?= e($data['nama']); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="poin" class="form-label">Poin</label>
                        <input type="number" name="poin" id="poin" class="form-control"
                            min="0" value="<?= e($data['poin']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status_aktif" class="form-label">Status</label>
                        <select name="status_aktif" id="status_aktif" class="form-select" required>
                            <option value="1" <?= (int)$data['status_aktif'] === 1 ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= (int)$data['status_aktif'] === 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required><?= e($data['deskripsi']); ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="jenis_pelanggaran.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```