
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Anda tidak memiliki akses");
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) {
    header("Location: tahun_ajaran.php");
    exit;
}

$id = (int)$_GET['id'];

// Pastikan tahun ajaran tidak sedang digunakan penempatan siswa.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM t_kelas_siswa
     WHERE tahun_ajaran_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ((int)$data['jumlah'] > 0) {
    header("Location: tahun_ajaran.php?gagal_hapus=dipakai");
    exit;
}

// Pastikan tahun ajaran tidak sedang digunakan wali kelas.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM t_wali_kelas
     WHERE tahun_ajaran_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ((int)$data['jumlah'] > 0) {
    header("Location: tahun_ajaran.php?gagal_hapus=dipakai");
    exit;
}

// Hapus data tahun ajaran.
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: tahun_ajaran.php?berhasil_hapus=1");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: tahun_ajaran.php?gagal_hapus=error");
exit;
?>
