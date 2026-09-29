<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   PROSES TAMBAH KELAS
========================= */

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_kelas
        (
            nama,
            tingkat,
            jurusan,
            status_aktif
        )
        VALUES
        (
            '$nama',
            '$tingkat',
            '$jurusan',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Data kelas berhasil disimpan!";
    } else {
        echo "Data kelas gagal disimpan!";
    }
}


/* =========================
   DATA KELAS
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Kelas</title>

</head>

<body>

<h2>Kelola Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Data Kelas</h3>

<form method="POST">

    <label>Nama Kelas</label>
    <br>

    <input
        type="text"
        name="nama"
        placeholder="Contoh: RPL"
        required
    >

    <br><br>


    <label>Tingkat</label>
    <br>

    <select name="tingkat" required>

        <option value="">-- Pilih Tingkat --</option>

        <option value="X">X</option>

        <option value="XI">XI</option>

        <option value="XII">XII</option>

    </select>

    <br><br>


    <label>Jurusan</label>
    <br>

    <select name="jurusan" required>

        <option value="">-- Pilih Jurusan --</option>

        <option value="RPL">RPL</option>

        <option value="TKJ">TKJ</option>

        <option value="BD">BD</option>

        <option value="TKR">TKR</option>

        <option value="TSM">TSM</option>

    </select>

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
        Tambah Kelas
    </button>

</form>

<hr>

<h3>Data Kelas</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($kelas = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td>
            <?= $no++; ?>
        </td>

        <td>
            <?= $kelas['nama']; ?>
        </td>

        <td>
            <?= $kelas['tingkat']; ?>
        </td>

        <td>
            <?= $kelas['jurusan']; ?>
        </td>

        <td>

            <?php

            if ($kelas['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_kelas.php?id=<?= $kelas['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_kelas.php?id=<?= $kelas['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus kelas ini?')"
            >
                Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</body>

</html>