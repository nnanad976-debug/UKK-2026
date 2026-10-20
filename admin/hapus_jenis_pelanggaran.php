
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit("Anda tidak memiliki akses");
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) {
    header("Location: jenis_pelanggaran.php");
    exit;
}

$id = (int)$_GET['id'];

// Pastikan data jenis pelanggaran tersedia.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_pelanggaran WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: jenis_pelanggaran.php?hapus=gagal");
    exit;
}

// Periksa apakah masih digunakan oleh catatan pelanggaran siswa.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE pelanggaran_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cek = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ((int)$cek['total'] > 0) {
    header("Location: jenis_pelanggaran.php?hapus=dipakai");
    exit;
}

// Hapus jenis pelanggaran.
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: jenis_pelanggaran.php?hapus=sukses");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: jenis_pelanggaran.php?hapus=gagal");
exit;
?>
