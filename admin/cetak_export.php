<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$siswa_id = $_GET['siswa_id'] ?? '';

$siswa = mysqli_query($koneksi,
    "SELECT * FROM t_siswa
     WHERE id='$siswa_id'"
);

$data_siswa = mysqli_fetch_assoc($siswa);

$query_siswa = mysqli_query($koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif=1
     ORDER BY nama"
);

$kelas = '-';

if ($siswa_id != '') {

    $q_kelas = mysqli_query($koneksi,
        "SELECT k.tingkat, k.jurusan
         FROM t_pelanggaran_siswa ps
         INNER JOIN t_kelas k ON ps.kelas_id=k.id
         WHERE ps.siswa_id='$siswa_id'
         ORDER BY ps.tanggal DESC
         LIMIT 1"
    );

    $data_kelas = mysqli_fetch_assoc($q_kelas);

    if ($data_kelas) {
        $kelas = $data_kelas['tingkat'] . ' ' . $data_kelas['jurusan'];
    }
}

$pelanggaran = mysqli_query($koneksi,
    "SELECT *
     FROM t_pelanggaran_siswa
     WHERE siswa_id='$siswa_id'
     ORDER BY tanggal DESC"
);

$total = mysqli_query($koneksi,
    "SELECT SUM(poin) AS total
     FROM t_pelanggaran_siswa
     WHERE siswa_id='$siswa_id'"
);

$data_total = mysqli_fetch_assoc($total);
$total_poin = $data_total['total'] ?? 0;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Kartu Pelanggaran</title>
</head>

<body>

<h2>Cetak Kartu Pelanggaran Siswa</h2>

<a href="dashboard.php">Kembali</a>

<hr>

<form method="GET">

    <label>Pilih Siswa</label><br>

    <select name="siswa_id" required>

        <option value="">-- Pilih Siswa --</option>

        <?php while ($s = mysqli_fetch_assoc($query_siswa)) { ?>

            <option value="<?= $s['id']; ?>"
                <?= $siswa_id == $s['id'] ? 'selected' : ''; ?>>

                <?= $s['nis']; ?> - <?= $s['nama']; ?>

            </option>

        <?php } ?>

    </select>

    <button type="submit">Tampilkan</button>

</form>

<?php if ($data_siswa) { ?>

<hr>

<table border="1" cellpadding="8" cellspacing="0" width="800">

<tr>
    <td colspan="4" align="center">
        <h2>KARTU PELANGGARAN SISWA</h2>
        <b>SISTEM INFORMASI PELANGGARAN SISWA</b>
    </td>
</tr>

<tr>
    <td colspan="4"><b>IDENTITAS SISWA</b></td>
</tr>

<tr>
    <td>NIS</td>
    <td><?= $data_siswa['nis']; ?></td>
    <td>NISN</td>
    <td><?= $data_siswa['nisn']; ?></td>
</tr>

<tr>
    <td>Nama</td>
    <td colspan="3"><?= $data_siswa['nama']; ?></td>
</tr>

<tr>
    <td>Jenis Kelamin</td>
    <td>
        <?= $data_siswa['jenis_kelamin'] == 'L'
            ? 'Laki-laki'
            : 'Perempuan'; ?>
    </td>

    <td>Kelas</td>
    <td><?= $kelas; ?></td>
</tr>

<tr>
    <td>Alamat</td>
    <td colspan="3"><?= $data_siswa['alamat']; ?></td>
</tr>

<tr>
    <td colspan="4" align="center">
        <b>RIWAYAT PELANGGARAN</b>
    </td>
</tr>

<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Pelanggaran</th>
    <th>Poin</th>
</tr>

<?php
$no = 1;

while ($p = mysqli_fetch_assoc($pelanggaran)) {
?>

<tr>
    <td align="center"><?= $no++; ?></td>
    <td><?= $p['tanggal']; ?></td>
    <td>
        <?= $p['nama_pelanggaran']; ?>

        <?php if ($p['keterangan']) { ?>
            <br>
            <small><?= $p['keterangan']; ?></small>
        <?php } ?>
    </td>
    <td align="center"><?= $p['poin']; ?></td>
</tr>

<?php } ?>

<tr>
    <td colspan="3" align="right">
        <b>TOTAL POIN</b>
    </td>
    <td align="center">
        <b><?= $total_poin; ?></b>
    </td>
</tr>

<tr>
    <td colspan="2" align="center">
        Mengetahui,<br>
        Wali Kelas<br><br><br>
        (________________)
    </td>

    <td colspan="2" align="center">
        Guru<br>
        Pencatat Pelanggaran<br><br><br>
        (________________)
    </td>
</tr>

</table>

<br>

<button onclick="window.print()">Cetak</button>

<?php } ?>

</body>
</html>