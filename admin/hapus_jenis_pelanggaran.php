```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

// Penghapusan hanya boleh dilakukan melalui formulir POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: jenis_pelanggaran.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: jenis_pelanggaran.php");
    exit;
}

// Periksa apakah data tersedia.
$cek = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_pelanggaran WHERE id = ? LIMIT 1"
);

if (!$cek) {
    header("Location: jenis_pelanggaran.php?gagal_hapus=1");
    exit;
}

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
$hasil = mysqli_stmt_get_result($cek);
$ada = mysqli_num_rows($hasil) > 0;
mysqli_stmt_close($cek);

if (!$ada) {
    header("Location: jenis_pelanggaran.php");
    exit;
}

// Hapus data yang dipilih.
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran WHERE id = ?"
);

if (!$hapus) {
    header("Location: jenis_pelanggaran.php?gagal_hapus=1");
    exit;
}

mysqli_stmt_bind_param($hapus, "i", $id);
$berhasil = mysqli_stmt_execute($hapus);
mysqli_stmt_close($hapus);

if ($berhasil) {
    header("Location: jenis_pelanggaran.php?dihapus=1");
    exit;
}

// Kemungkinan data masih digunakan oleh tabel lain.
header("Location: jenis_pelanggaran.php?gagal_hapus=1");
exit;
?>
```