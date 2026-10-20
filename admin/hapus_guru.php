
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

// Cek hak akses admin
if (($_SESSION['role'] ?? '') != 'admin') {
    exit("Anda tidak memiliki akses");
}

// Cek ID guru
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: guru.php");
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah data guru tersedia
$cek = mysqli_query($koneksi, "SELECT id FROM t_guru WHERE id = $id");

if (!$cek || mysqli_num_rows($cek) == 0) {
    header("Location: guru.php");
    exit;
}

// Hapus data guru
$hapus = mysqli_query($koneksi, "DELETE FROM t_guru WHERE id = $id");

if ($hapus) {
    header("Location: guru.php");
    exit;
} else {
    echo "<script>
        alert('Data guru gagal dihapus. Guru mungkin masih digunakan pada data wali kelas atau data terkait lainnya.');
        window.location.href = 'guru.php';
    </script>";
    exit;
}
?>
