
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Anda tidak memiliki akses");
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) {
    header("Location: tahun_ajaran.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_tahun_ajaran WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tahun = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$tahun) {
    exit("Data tahun ajaran tidak ditemukan.");
}

$pesan = "";
$nama = $tahun['nama'];
$tanggal_mulai = $tahun['tanggal_mulai'];
$tanggal_selesai = $tahun['tanggal_selesai'];
$status_aktif = (int)$tahun['status_aktif'];

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if (
        $nama === '' ||
        $tanggal_mulai === '' ||
        $tanggal_selesai === '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } elseif ($tanggal_mulai > $tanggal_selesai) {
        $pesan = "Tanggal mulai tidak boleh melewati tanggal selesai.";
    } else {
        mysqli_begin_transaction($koneksi);

        try {
            // Jika tahun ajaran ini diaktifkan,
            // nonaktifkan tahun ajaran lainnya terlebih dahulu.
            if ($status_aktif === 1) {
                if (!mysqli_query(
                    $koneksi,
                    "UPDATE t_tahun_ajaran SET status_aktif = 0"
                )) {
                    throw new Exception(mysqli_error($koneksi));
                }
            }

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_tahun_ajaran
                 SET nama = ?, tanggal_mulai = ?,
                     tanggal_selesai = ?, status_aktif = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                throw new Exception(mysqli_error($koneksi));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sssii",
                $nama,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($koneksi);

            header("Location: tahun_ajaran.php?berhasil_edit=1");
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            $pesan = "Data gagal diperbarui. Silakan periksa kembali.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Tahun Ajaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --line: #dce9f8;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    background: #f3f7fc;
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
.brand-subtitle { font-size: 12px; color: #7890ad; }
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
}
.menu-link:hover, .menu-link.active {
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
.logout .menu-link { color: #d9534f; }
.main {
    margin-left: 282px;
    min-height: 100vh;
    padding: 24px 28px 40px;
}
.judul, h4 { color: #234e78; }
.card {
    border: 1px solid #e1eaf4;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(35,78,120,.05);
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
    background: #5f91ba;
    border-color: #5f91ba;
}
.btn-primary:hover {
    background: #4b7fa9;
    border-color: #4b7fa9;
}
.btn-secondary {
    background: #e8f0f8;
    border-color: #d8e5f1;
    color: #426584;
}
.btn-secondary:hover {
    background: #dceaf6;
    color: #234e78;
}
@media(max-width:900px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }
    .main { margin-left: 0; padding: 18px 14px 30px; }
    .logout { margin-top: 14px; }
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
        <a href="tahun_ajaran.php" class="menu-link active">Tahun Ajaran</a>
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
    <div class="container-fluid py-4">

        <div class="mb-4">
            <a href="tahun_ajaran.php" class="btn btn-secondary">
                &larr; Kembali ke Tahun Ajaran
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Edit Tahun Ajaran</h2>
                <p class="text-muted mb-0">Perbarui informasi tahun ajaran.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <?php if ($pesan !== '') { ?>
                    <div class="alert alert-danger">
                        <?= e($pesan); ?>
                    </div>
                <?php } ?>

                <form method="POST"
                      action="edit_tahun_ajaran.php?id=<?= $id; ?>">

                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text"
                               name="nama"
                               class="form-control"
                               placeholder="Contoh: 2026/2027"
                               value="<?= e($nama); ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date"
                               name="tanggal_mulai"
                               class="form-control"
                               value="<?= e($tanggal_mulai); ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date"
                               name="tanggal_selesai"
                               class="form-control"
                               value="<?= e($tanggal_selesai); ?>"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status_aktif" class="form-select" required>
                            <option value="1" <?= $status_aktif === 1 ? 'selected' : ''; ?>>
                                Aktif
                            </option>
                            <option value="0" <?= $status_aktif === 0 ? 'selected' : ''; ?>>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="tahun_ajaran.php" class="btn btn-secondary ms-1">
                        Batal
                    </a>

                </form>
            </div>
        </div>

    </div>
</main>
</body>
</html>
