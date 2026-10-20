
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit("Anda tidak memiliki akses");
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) {
    header("Location: kategori_pelanggaran.php");
    exit;
}

$id = (int)$_GET['id'];

// Pastikan kategori tersedia.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_pelanggaran_kategori WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kategori = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$kategori) {
    header("Location: kategori_pelanggaran.php?hapus=gagal");
    exit;
}

// Periksa apakah kategori masih digunakan jenis pelanggaran.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran
     WHERE pelanggaran_kategori_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ((int)$data['total'] > 0) {
    header("Location: kategori_pelanggaran.php?hapus=dipakai");
    exit;
}

// Hapus kategori jika tidak memiliki data yang berhubungan.
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: kategori_pelanggaran.php?hapus=sukses");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: kategori_pelanggaran.php?hapus=gagal");
exit;
?>
