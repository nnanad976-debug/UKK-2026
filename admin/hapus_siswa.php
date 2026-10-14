```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: siswa.php?pesan=id_tidak_valid");
    exit;
}

$id = (int) $_GET['id'];

// Periksa apakah siswa tersedia
$stmt = mysqli_prepare($koneksi, "SELECT id FROM t_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    header("Location: siswa.php?pesan=siswa_tidak_ditemukan");
    exit;
}

mysqli_stmt_close($stmt);

// Hapus data siswa
$stmt = mysqli_prepare($koneksi, "DELETE FROM t_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: siswa.php?pesan=hapus_berhasil");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: siswa.php?pesan=hapus_gagal");
exit;
?>
```