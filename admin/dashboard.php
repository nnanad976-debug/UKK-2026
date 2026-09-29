<?php

include "../middleware/auth.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<body>

<h2>Dashboard Admin</h2>

<p>
    Selamat datang, <?= $_SESSION['nama']; ?>
</p>

<hr>

<h3>Menu Admin</h3>
<a href="siswa.php">Kelola Siswa</a>
<br><br>

<a href="guru.php">Kelola Guru</a>
<br><br>

<a href="kelas.php">Kelola Kelas</a>
<br><br>

<a href="tahun_ajaran.php">Kelola Tahun Ajaran</a>
<br><br>

<a href="penempatan_siswa.php">Penempatan Siswa</a>
<br><br>

<a href="wali_kelas.php">Kelola Wali Kelas</a>
<br><br>

<a href="kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a>
<br><br>

<a href="jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a>
<br><br>

<a href="cetak_export.php">Cetak / Export</a>
<br><br>

<a href="../logout.php">Logout</a>

</body>
</html>