<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   TAMBAH KATEGORI
   ========================= */

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_pelanggaran_kategori
        (
            nama,
            deskripsi,
            status_aktif
        )
        VALUES
        (
            '$nama',
            '$deskripsi',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Kategori pelanggaran berhasil disimpan!";
    } else {
        echo "Kategori pelanggaran gagal disimpan!";
    }
}


/* =========================
   DATA KATEGORI
   ========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_kategori
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Kategori Pelanggaran</title>
</head>

<body>

<h2>Kelola Kategori Pelanggaran</h2>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

<br><br>

<hr>

<h3>Tambah Kategori Pelanggaran</h3>

<form method="POST">

    <label>Nama Kategori</label>
    <br>

    <input
        type="text"
        name="nama"
        placeholder="Contoh: Kedisiplinan"
        required
    >

    <br><br>


    <label>Deskripsi</label>
    <br>

    <textarea
        name="deskripsi"
        rows="4"
        cols="40"
        placeholder="Contoh: Pelanggaran yang berkaitan dengan kedisiplinan siswa."
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
        Tambah Kategori
    </button>

</form>

<hr>

<h3>Data Kategori Pelanggaran</h3>

<table border="1" cellpadding="8" cellspacing="0">

<tr>

    <th>No</th>
    <th>Nama Kategori</th>
    <th>Deskripsi</th>
    <th>Status</th>
    <th>Aksi</th>

</tr>

<?php

$no = 1;

while ($data = mysqli_fetch_assoc($query)) {

?>

<tr>

    <td>
        <?= $no++; ?>
    </td>

    <td>
        <?= $data['nama']; ?>
    </td>

    <td>
        <?= $data['deskripsi']; ?>
    </td>

    <td>

        <?php

        if ($data['status_aktif'] == 1) {
            echo "Aktif";
        } else {
            echo "Tidak Aktif";
        }

        ?>

    </td>

    <td>

        <a href="edit_kategori_pelanggaran.php?id=<?= $data['id']; ?>">
            Edit
        </a>

        |

        <a
            href="hapus_kategori_pelanggaran.php?id=<?= $data['id']; ?>"
            onclick="return confirm('Yakin ingin menghapus kategori ini?')"
        >
            Hapus
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>

</html>