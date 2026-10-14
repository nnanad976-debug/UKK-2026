
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
    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO t_pelanggaran
        (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "issisi",
            $kategori_id,
            $kode,
            $nama,
            $poin,
            $deskripsi,
            $status_aktif
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>
                alert('Jenis pelanggaran berhasil disimpan!');
                window.location.href='jenis_pelanggaran.php';
            </script>";
            exit;
        }

        $pesan = "Jenis pelanggaran gagal disimpan. Pastikan kode belum digunakan dan data sudah benar.";
        $jenis_pesan = "danger";
        mysqli_stmt_close($stmt);
    } else {
        $pesan = "Jenis pelanggaran gagal disimpan.";
        $jenis_pesan = "danger";
    }
}

$query_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/*
 * Tampilkan satu data untuk setiap kombinasi nama,
 * poin, dan kategori. Data asli tidak dihapus.
 */
$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        k.nama AS nama_kategori
     FROM t_pelanggaran p
     INNER JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
     WHERE p.id = (
        SELECT MIN(p2.id)
        FROM t_pelanggaran p2
        WHERE p2.nama = p.nama
          AND p2.poin = p.poin
          AND p2.pelanggaran_kategori_id = p.pelanggaran_kategori_id
     )
     ORDER BY p.id DESC"
);

if (!$query) {
    $pesan = "Data jenis pelanggaran gagal ditampilkan.";
    $jenis_pesan = "danger";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Jenis Pelanggaran</title>

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
    --shadow: 0 8px 22px rgba(32,62,104,.07);
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--page);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
}

a {
    text-decoration: none;
}

.layout {
    min-height: 100vh;
}

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
    .card-body {
        padding: 17px;
    }
}
</style>
</head>

<body>
<div class="layout">

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
        <div class="container py-4">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <a href="../dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="collapse"
                    data-bs-target="#formTambahJenis"
                    aria-expanded="false"
                    aria-controls="formTambahJenis">
                    + Tambah Jenis Pelanggaran
                </button>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="judul mb-1">Kelola Jenis Pelanggaran</h2>
                    <p class="text-muted mb-0">
                        Tambah dan kelola data jenis pelanggaran siswa.
                    </p>
                </div>
            </div>

            <?php if ($pesan !== "") { ?>
                <div class="alert alert-<?php echo htmlspecialchars($jenis_pesan); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($pesan); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php } ?>

            <div class="collapse <?php echo $pesan !== '' ? 'show' : ''; ?>" id="formTambahJenis">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="mb-3">Tambah Jenis Pelanggaran</h4>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Kategori Pelanggaran</label>
                                <select name="pelanggaran_kategori_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>

                                    <?php
                                    if ($query_kategori) {
                                        while ($kategori = mysqli_fetch_assoc($query_kategori)) {
                                    ?>
                                        <option value="<?php echo (int)$kategori['id']; ?>">
                                            <?php echo htmlspecialchars($kategori['nama']); ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Pelanggaran</label>
                                    <input
                                        type="text"
                                        name="kode"
                                        class="form-control"
                                        placeholder="Contoh: PLG-051"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Pelanggaran</label>
                                    <input
                                        type="text"
                                        name="nama"
                                        class="form-control"
                                        placeholder="Contoh: Datang terlambat"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        name="poin"
                                        class="form-control"
                                        min="0"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Aktif</label>
                                    <select name="status_aktif" class="form-select" required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea
                                    name="deskripsi"
                                    class="form-control"
                                    rows="3"
                                    required></textarea>
                            </div>

                            <button type="submit" name="simpan" class="btn btn-primary">
                                Tambah Jenis Pelanggaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-1">Data Jenis Pelanggaran</h4>
                    <p class="keterangan mb-3">
                        Daftar jenis pelanggaran beserta kategori, poin, dan statusnya.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:60px">No</th>
                                    <th>Kode</th>
                                    <th>Kategori</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php
                            if ($query && mysqli_num_rows($query) > 0) {
                                $no = 1;

                                while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($data['kode']); ?></td>
                                    <td><?php echo htmlspecialchars($data['nama_kategori']); ?></td>
                                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td><?php echo (int)$data['poin']; ?></td>
                                    <td><?php echo htmlspecialchars($data['deskripsi'] ?? ''); ?></td>

                                    <td>
                                        <?php if ((int)$data['status_aktif'] === 1) { ?>
                                            <span class="status-aktif">Aktif</span>
                                        <?php } else { ?>
                                            <span class="status-nonaktif">Tidak Aktif</span>
                                        <?php } ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <a
                                            href="edit_jenis_pelanggaran.php?id=<?php echo (int)$data['id']; ?>"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <a
                                            href="hapus_jenis_pelanggaran.php?id=<?php echo (int)$data['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data jenis pelanggaran.
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
