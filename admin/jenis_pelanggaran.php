<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

/* =========================
   TAMBAH JENIS PELANGGARAN
   ========================= */
if (isset($_POST['simpan'])) {

    $pelanggaran_kategori_id = $_POST['pelanggaran_kategori_id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $poin = $_POST['poin'];
    $deskripsi = $_POST['deskripsi'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_pelanggaran
        (
            pelanggaran_kategori_id,
            kode,
            nama,
            poin,
            deskripsi,
            status_aktif
        )
        VALUES
        (
            '$pelanggaran_kategori_id',
            '$kode',
            '$nama',
            '$poin',
            '$deskripsi',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Jenis pelanggaran berhasil disimpan!";
    } else {
        echo "Jenis pelanggaran gagal disimpan!";
    }
}


/* =========================
   DATA KATEGORI
   ========================= */
$query_kategori = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);


/* =========================
   DATA JENIS PELANGGARAN
   ========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        k.nama AS nama_kategori
    FROM t_pelanggaran p
    INNER JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
    ORDER BY p.id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Jenis Pelanggaran</title>
</head>

<body>

<h2>Jenis Pelanggaran</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<hr>

<h3>Tambah Jenis Pelanggaran</h3>

<form method="POST">

    <label>Kategori Pelanggaran</label>
    <br>

    <select name="pelanggaran_kategori_id" required>

        <option value="">
            -- Pilih Kategori --
        </option>

        <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

            <option value="<?= $kategori['id']; ?>">
                <?= $kategori['nama']; ?>
            </option>

        <?php } ?>

    </select>

    <br><br>


    <label>Kode Pelanggaran</label>
    <br>

    <input
        type="text"
        name="kode"
        placeholder="Contoh: PLG-051"
        required
    >

    <br><br>


    <label>Nama Pelanggaran</label>
    <br>

    <input
        type="text"
        name="nama"
        placeholder="Contoh: Datang terlambat"
        required
    >

    <br><br>


    <label>Poin</label>
    <br>

    <input
        type="number"
        name="poin"
        min="0"
        required
    >

    <br><br>


    <label>Deskripsi</label>
    <br>

    <textarea
        name="deskripsi"
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
        Tambah Jenis Pelanggaran
    </button>

</form>

<hr>

<h3>Data Jenis Pelanggaran</h3>

<table border="1" cellpadding="8" cellspacing="0">

<tr>

    <th>No</th>
    <th>Kode</th>
    <th>Kategori</th>
    <th>Nama Pelanggaran</th>
    <th>Poin</th>
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
        <?= $data['kode']; ?>
    </td>

    <td>
        <?= $data['nama_kategori']; ?>
    </td>

    <td>
        <?= $data['nama']; ?>
    </td>

    <td>
        <?= $data['poin']; ?>
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

        <a href="edit_jenis_pelanggaran.php?id=<?= $data['id']; ?>">
            Edit
        </a>

        |

        <a
            href="hapus_jenis_pelanggaran.php?id=<?= $data['id']; ?>"
            onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')"
        >
            Hapus
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>

</html>