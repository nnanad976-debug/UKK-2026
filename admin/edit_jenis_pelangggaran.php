
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit("Anda tidak memiliki akses");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);
$pesan = '';

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_pelanggaran WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit("Data jenis pelanggaran tidak ditemukan. <a href='jenis_pelanggaran.php'>Kembali</a>");
}

$kategori_id = (int)$data['pelanggaran_kategori_id'];
$kode = $data['kode'];
$nama = $data['nama'];
$poin = (int)$data['poin'];
$deskripsi = $data['deskripsi'];
$status_aktif = (int)$data['status_aktif'];

if (isset($_POST['simpan'])) {
    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } else {
        // Pastikan kategori tersedia.
        $cekKategori = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori WHERE id = ?"
        );
        mysqli_stmt_bind_param($cekKategori, "i", $kategori_id);
        mysqli_stmt_execute($cekKategori);
        $hasilKategori = mysqli_stmt_get_result($cekKategori);

        if (mysqli_num_rows($hasilKategori) === 0) {
            $pesan = "Kategori pelanggaran tidak ditemukan.";
        } else {
            // Pastikan kode tidak dipakai data lain.
            $cekKode = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_pelanggaran WHERE kode = ? AND id <> ?"
            );
            mysqli_stmt_bind_param($cekKode, "si", $kode, $id);
            mysqli_stmt_execute($cekKode);
            $hasilKode = mysqli_stmt_get_result($cekKode);

            if (mysqli_num_rows($hasilKode) > 0) {
                $pesan = "Kode pelanggaran sudah digunakan.";
            } else {
                $stmtUpdate = mysqli_prepare(
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

                mysqli_stmt_bind_param(
                    $stmtUpdate,
                    "issisii",
                    $kategori_id,
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi,
                    $status_aktif,
                    $id
                );

                if (mysqli_stmt_execute($stmtUpdate)) {
                    header("Location: jenis_pelanggaran.php?status=edit_sukses");
                    exit;
                } else {
                    $pesan = "Gagal memperbarui data pelanggaran.";
                }
            }
        }
    }
}

$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori
     WHERE status_aktif = 1 OR id = " . $kategori_id . "
     ORDER BY nama ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f9fd;
            color: #203e68;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border: 1px solid #dce9f8;
            border-radius: 12px;
        }
        .btn-primary {
            background: #2878cf;
            border-color: #2878cf;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Edit Jenis Pelanggaran</h3>
            <p class="text-secondary mb-0">Perbarui data jenis pelanggaran.</p>
        </div>
        <a href="jenis_pelanggaran.php" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesan); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_jenis_pelanggaran.php?id=<?= $id; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori Pelanggaran</label>
                        <select name="pelanggaran_kategori_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                                <option value="<?= (int)$k['id']; ?>"
                                    <?= $kategori_id === (int)$k['id'] ? 'selected' : ''; ?>>
                                    <?= e($k['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Pelanggaran</label>
                        <input type="text" name="kode" class="form-control"
                               value="<?= e($kode); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Pelanggaran</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= e($nama); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poin</label>
                        <input type="number" name="poin" class="form-control"
                               min="0" value="<?= $poin; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Aktif</label>
                        <select name="status_aktif" class="form-select" required>
                            <option value="1" <?= $status_aktif === 1 ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= $status_aktif === 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required><?= e($deskripsi); ?></textarea>
                    </div>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Perubahan
                </button>
                <a href="jenis_pelanggaran.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
