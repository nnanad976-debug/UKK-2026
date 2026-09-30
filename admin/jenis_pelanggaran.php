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

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- WARNA SAMA DENGAN HALAMAN LAIN -->
    <style>

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #6f96aa;
            --bs-btn-border-color: #6f96aa;

            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #5f879b;
            --bs-btn-hover-border-color: #5f879b;

            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #5f879b;
            --bs-btn-active-border-color: #5f879b;
        }

        .btn-danger {
            --bs-btn-color: #fff;
            --bs-btn-bg: #cf8888;
            --bs-btn-border-color: #cf8888;

            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #bd7777;
            --bs-btn-hover-border-color: #bd7777;

            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #bd7777;
            --bs-btn-active-border-color: #bd7777;
        }

    </style>

</head>

<body>

<div class="container mt-4">


    <!-- KEMBALI -->

    <a
        href="../dashboard.php"
        class="btn btn-secondary mb-3">

        Kembali ke Dashboard

    </a>


    <!-- JUDUL -->

    <h2>Jenis Pelanggaran</h2>

    <p class="text-muted">
        Kelola data jenis pelanggaran siswa.
    </p>

    <hr>


    <!-- TAMBAH JENIS PELANGGARAN -->

    <h3>Tambah Jenis Pelanggaran</h3>

    <form method="POST">


        <!-- KATEGORI -->

        <div class="mb-3">

            <label class="form-label">
                Kategori Pelanggaran
            </label>

            <select
                name="pelanggaran_kategori_id"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Kategori --
                </option>

                <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

                    <option value="<?= $kategori['id']; ?>">

                        <?= $kategori['nama']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- KODE -->

        <div class="mb-3">

            <label class="form-label">
                Kode Pelanggaran
            </label>

            <input
                type="text"
                name="kode"
                class="form-control"
                placeholder="Contoh: PLG-051"
                required>

        </div>


        <!-- NAMA -->

        <div class="mb-3">

            <label class="form-label">
                Nama Pelanggaran
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Contoh: Datang terlambat"
                required>

        </div>


        <!-- POIN -->

        <div class="mb-3">

            <label class="form-label">
                Poin
            </label>

            <input
                type="number"
                name="poin"
                class="form-control"
                min="0"
                required>

        </div>


        <!-- DESKRIPSI -->

        <div class="mb-3">

            <label class="form-label">
                Deskripsi
            </label>

            <textarea
                name="deskripsi"
                class="form-control"
                rows="4"
                required></textarea>

        </div>


        <!-- STATUS -->

        <div class="mb-3">

            <label class="form-label">
                Status Aktif
            </label>

            <select
                name="status_aktif"
                class="form-select"
                required>

                <option value="1">
                    Aktif
                </option>

                <option value="0">
                    Tidak Aktif
                </option>

            </select>

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            name="simpan"
            class="btn btn-primary">

            Tambah Jenis Pelanggaran

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA JENIS PELANGGARAN -->

    <h3>Data Jenis Pelanggaran</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

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

            </thead>

            <tbody>

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

                        <a
                            href="edit_jenis_pelanggaran.php?id=<?= $data['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_jenis_pelanggaran.php?id=<?= $data['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">

                            Hapus

                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
</script>

</body>

</html>