<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   PROSES TAMBAH GURU
========================= */

if (isset($_POST['simpan'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_guru
        (
            nip,
            nama,
            email,
            status_aktif
        )
        VALUES
        (
            '$nip',
            '$nama',
            '$email',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Data guru berhasil disimpan!";
    } else {
        echo "Data guru gagal disimpan!";
    }
}


/* =========================
   DATA GURU
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Guru</title>

</head>

<body>

<h2>Kelola Guru</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Data Guru</h3>

<form method="POST">

    <label>NIP</label>
    <br>

    <input
        type="text"
        name="nip"
        required
    >

    <br><br>


    <label>Nama Guru</label>
    <br>

    <input
        type="text"
        name="nama"
        required
    >

    <br><br>


    <label>Email</label>
    <br>

    <input
        type="email"
        name="email"
        required
    >

    <br><br>


    <label>Status</label>
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
        Tambah Guru
    </button>

</form>

<hr>

<h3>Data Guru</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($guru = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td>
            <?= $no++; ?>
        </td>

        <td>
            <?= $guru['nip']; ?>
        </td>

        <td>
            <?= $guru['nama']; ?>
        </td>

        <td>
            <?= $guru['email']; ?>
        </td>

        <td>

            <?php

            if ($guru['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_guru.php?id=<?= $guru['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_guru.php?id=<?= $guru['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus guru ini?')"
            >
                Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</body>

</html>