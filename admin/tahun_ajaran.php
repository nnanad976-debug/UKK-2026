<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   PROSES TAMBAH
========================= */

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    /*
       Jika tahun ajaran baru dibuat aktif,
       tahun ajaran lainnya dibuat tidak aktif.
    */

    if ($status_aktif == 1) {

        mysqli_query(
            $koneksi,
            "UPDATE t_tahun_ajaran
             SET status_aktif = 0"
        );
    }

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_tahun_ajaran
        (
            nama,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES
        (
            '$nama',
            '$tanggal_mulai',
            '$tanggal_selesai',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Data tahun ajaran berhasil disimpan!";
    } else {
        echo "Data tahun ajaran gagal disimpan!";
    }
}


/* =========================
   DATA TAHUN AJARAN
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Tahun Ajaran</title>

</head>

<body>

<h2>Kelola Tahun Ajaran</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Tahun Ajaran</h3>

<form method="POST">

    <label>Tahun Ajaran</label>
    <br>

    <input
        type="text"
        name="nama"
        placeholder="Contoh: 2026/2027"
        required
    >

    <br><br>


    <label>Tanggal Mulai</label>
    <br>

    <input
        type="date"
        name="tanggal_mulai"
        required
    >

    <br><br>


    <label>Tanggal Selesai</label>
    <br>

    <input
        type="date"
        name="tanggal_selesai"
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
        Tambah Tahun Ajaran
    </button>

</form>

<hr>

<h3>Data Tahun Ajaran</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>Tahun Ajaran</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($tahun = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td>
            <?= $no++; ?>
        </td>

        <td>
            <?= $tahun['nama']; ?>
        </td>

        <td>
            <?= $tahun['tanggal_mulai']; ?>
        </td>

        <td>
            <?= $tahun['tanggal_selesai']; ?>
        </td>

        <td>

            <?php

            if ($tahun['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_tahun_ajaran.php?id=<?= $tahun['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_tahun_ajaran.php?id=<?= $tahun['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')"
            >
                Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</body>

</html>