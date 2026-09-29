<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   PROSES TAMBAH SISWA
========================= */

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_siswa
        (
            nis,
            nisn,
            nama,
            jenis_kelamin,
            tanggal_lahir,
            alamat,
            status_aktif
        )
        VALUES
        (
            '$nis',
            '$nisn',
            '$nama',
            '$jenis_kelamin',
            '$tanggal_lahir',
            '$alamat',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Data siswa berhasil disimpan!";
    } else {
        echo "Data siswa gagal disimpan!";
    }
}


/* =========================
   DATA SISWA
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_siswa
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Siswa</title>

</head>

<body>

<h2>Kelola Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Data Siswa</h3>

<form method="POST">

    <label>NIS</label>
    <br>

    <input
        type="text"
        name="nis"
        required
    >

    <br><br>


    <label>NISN</label>
    <br>

    <input
        type="text"
        name="nisn"
        required
    >

    <br><br>


    <label>Nama</label>
    <br>

    <input
        type="text"
        name="nama"
        required
    >

    <br><br>


    <label>Jenis Kelamin</label>
    <br>

    <select name="jenis_kelamin" required>

        <option value="">
            -- Pilih Jenis Kelamin --
        </option>

        <option value="L">
            Laki-laki
        </option>

        <option value="P">
            Perempuan
        </option>

    </select>

    <br><br>


    <label>Tanggal Lahir</label>
    <br>

    <input
        type="date"
        name="tanggal_lahir"
        required
    >

    <br><br>


    <label>Alamat</label>
    <br>

    <textarea
        name="alamat"
        rows="4"
        cols="40"
        required
    ></textarea>

    <br><br>


    <label>Status Aktif</label>
    <br>

    <select name="status_aktif" required>

        <option value="1">
            Aktif
        </option>

        <option value="0">
            Tidak Aktif
        </option>

    </select>

    <br><br>


    <button type="submit" name="simpan">
        Tambah Siswa
    </button>

</form>

<hr>

<h3>Data Siswa</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>NIS</th>
        <th>NISN</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($siswa = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td>
            <?= $no++; ?>
        </td>

        <td>
            <?= $siswa['nis']; ?>
        </td>

        <td>
            <?= $siswa['nisn']; ?>
        </td>

        <td>
            <?= $siswa['nama']; ?>
        </td>

        <td>

            <?php

            if ($siswa['jenis_kelamin'] == 'L') {
                echo "Laki-laki";
            } else {
                echo "Perempuan";
            }

            ?>

        </td>

        <td>
            <?= $siswa['tanggal_lahir']; ?>
        </td>

        <td>
            <?= $siswa['alamat']; ?>
        </td>

        <td>

            <?php

            if ($siswa['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_siswa.php?id=<?= $siswa['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_siswa.php?id=<?= $siswa['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus siswa ini?')"
            >
                Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</body>

</html>