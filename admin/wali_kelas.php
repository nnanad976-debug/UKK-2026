<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}


/* =========================
   PROSES SIMPAN
========================= */

if (isset($_POST['simpan'])) {

    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $guru_id = $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_wali_kelas
        (
            tahun_ajaran_id,
            kelas_id,
            guru_id,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES
        (
            '$tahun_ajaran_id',
            '$kelas_id',
            '$guru_id',
            '$tanggal_mulai',
            '$tanggal_selesai',
            1
        )"
    );

    if ($query_simpan) {
        echo "Data berhasil disimpan!";
    } else {
        echo "Data gagal disimpan!";
    }
}


/* =========================
   DATA TAHUN AJARAN
========================= */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran
     WHERE nama = '2026/2027'
     LIMIT 1"
);


/* =========================
   DATA KELAS
========================= */

$query_kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY id ASC"
);


/* =========================
   DATA GURU
========================= */

$query_guru = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/* =========================
   DATA WALI KELAS
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT
        wk.id,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        g.nip,
        g.nama AS nama_guru,
        wk.tanggal_mulai,
        wk.tanggal_selesai,
        wk.status_aktif

    FROM t_wali_kelas wk

    INNER JOIN t_tahun_ajaran ta
        ON wk.tahun_ajaran_id = ta.id

    INNER JOIN t_kelas k
        ON wk.kelas_id = k.id

    INNER JOIN t_guru g
        ON wk.guru_id = g.id

    ORDER BY wk.id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Wali Kelas</title>

</head>

<body>

<h2>Kelola Wali Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Wali Kelas</h3>

<form method="POST">

 <label>Tahun Ajaran</label>
<br>

<select name="tahun_ajaran_id" required>

    <option value="">-- Pilih Tahun Ajaran --</option>

    <?php while ($data = mysqli_fetch_assoc($query_tahun)) { ?>

        <option value="<?= $data['id']; ?>">
            <?= $data['nama']; ?>
        </option>

    <?php } ?>

</select>
    <br><br>


    <label>Kelas</label>
<br>

<select name="kelas_id" required>

    <option value="">-- Pilih Kelas --</option>

    <?php while ($data = mysqli_fetch_assoc($query_kelas)) { ?>

        <option value="<?= $data['id']; ?>">

            <?= $data['nama']; ?> -
            <?= $data['tingkat']; ?> -
            <?= $data['jurusan']; ?>

        </option>

    <?php } ?>

</select>

    <br><br>


   <label>Guru</label>
<br>

<select name="guru_id" required>

    <option value="">-- Pilih Guru --</option>

    <?php while ($data = mysqli_fetch_assoc($query_guru)) { ?>

        <option value="<?= $data['id']; ?>">

            <?= $data['nip']; ?> -
            <?= $data['nama']; ?>

        </option>

    <?php } ?>

</select>
    <br><br>


    <label>Tanggal Mulai</label>
    <br>

    <input
        type="date"
        name="tanggal_mulai"
        value="2026-07-01"
        required
    >

    <br><br>


    <label>Tanggal Selesai</label>
    <br>

    <input
        type="date"
        name="tanggal_selesai"
        value="2027-06-30"
    >

    <br><br>


    <button type="submit" name="simpan">
        Simpan
    </button>

</form>

<hr>

<h3>Data Wali Kelas</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>NIP</th>
        <th>Nama Guru</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>

    </tr>

    <?php

    $no = 1;

    while ($data = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td><?= $no++; ?></td>

        <td>
            <?= $data['tahun_ajaran']; ?>
        </td>

        <td>
            <?= $data['nama_kelas']; ?>
        </td>

        <td>
            <?= $data['tingkat']; ?>
        </td>

        <td>
            <?= $data['jurusan']; ?>
        </td>

        <td>
            <?= $data['nip']; ?>
        </td>

        <td>
            <?= $data['nama_guru']; ?>
        </td>

        <td>
            <?= $data['tanggal_mulai']; ?>
        </td>

        <td>
            <?= $data['tanggal_selesai']; ?>
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

    </tr>

    <?php } ?>

</table>

</body>

</html>