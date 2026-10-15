```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: siswa.php?status=id_tidak_valid");
    exit;
}

$id = (int) $_GET['id'];

// Pastikan siswa tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    header("Location: siswa.php?status=hapus_gagal");
    exit;
}

mysqli_stmt_close($stmt);

// Hapus siswa
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

try {
    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header("Location: siswa.php?status=hapus_sukses");
        exit;
    }
} catch (mysqli_sql_exception $e) {
    mysqli_stmt_close($stmt);
    header("Location: siswa.php?status=data_terkait");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: siswa.php?status=hapus_gagal");
exit;
?>
```