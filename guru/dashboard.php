<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$user_id = $_SESSION['user_id'];

/* CARI DATA GURU */
$query_guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id = '$user_id'
     LIMIT 1"
);

$data_guru = mysqli_fetch_assoc($query_guru);

$guru_id = $data_guru['id'] ?? 0;
$nama_guru = $data_guru['nama'] ?? $_SESSION['nama'];


/* JUMLAH SISWA */
$query_siswa = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_siswa
     WHERE status_aktif = 1"
);

$data_siswa = mysqli_fetch_assoc($query_siswa);


/* JUMLAH PELANGGARAN GURU */
$query_pelanggaran = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE guru_id = '$guru_id'"
);

$data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran);


/* TOTAL POIN */
$query_poin = mysqli_query(
    $koneksi,
    "SELECT SUM(poin) AS total
     FROM t_pelanggaran_siswa
     WHERE guru_id = '$guru_id'"
);

$data_poin = mysqli_fetch_assoc($query_poin);

$total_poin = $data_poin['total'] ?? 0;


/* TINDAKAN YANG BELUM SELESAI */
$query_tindakan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE guru_id = '$guru_id'
     AND status != 'Selesai'"
);

$data_tindakan = mysqli_fetch_assoc($query_tindakan);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard Guru</title>

</head>

<body>

<h2>Dashboard Guru</h2>

<p>
    Selamat datang, <b><?= $nama_guru; ?></b>
</p>

<hr>

<h3>Informasi</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>Jumlah Siswa</th>
        <th>Pelanggaran Dicatat</th>
        <th>Total Poin</th>
        <th>Tindakan Belum Selesai</th>
    </tr>

    <tr>

        <td align="center">
            <?= $data_siswa['total']; ?>
        </td>

        <td align="center">
            <?= $data_pelanggaran['total']; ?>
        </td>

        <td align="center">
            <?= $total_poin; ?>
        </td>

        <td align="center">
            <?= $data_tindakan['total']; ?>
        </td>

    </tr>

</table>

<hr>

<h3>Menu Guru</h3>

<a href="catat_pelanggaran.php">
    Catat Pelanggaran
</a>

<br><br>

<a href="tindakan.php">
    Tindakan
</a>

<br><br>

<a href="laporan.php">
    Laporan
</a>

<br><br>

<a href="riwayat.php">
    Riwayat
</a>

<br><br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>