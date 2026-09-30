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

    <h2>Kelola Kategori Pelanggaran</h2>

    <p class="text-muted">
        Kelola kategori pelanggaran siswa.
    </p>

    <hr>


    <!-- TAMBAH KATEGORI -->

    <h3>Tambah Kategori Pelanggaran</h3>

    <form method="POST">


        <!-- NAMA KATEGORI -->

        <div class="mb-3">

            <label class="form-label">
                Nama Kategori
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Contoh: Kedisiplinan"
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
                placeholder="Contoh: Pelanggaran yang berkaitan dengan kedisiplinan siswa."
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

            Tambah Kategori

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA KATEGORI -->

    <h3>Data Kategori Pelanggaran</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Kategori</th>
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

                        <a
                            href="edit_kategori_pelanggaran.php?id=<?= $data['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_kategori_pelanggaran.php?id=<?= $data['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus kategori ini?')">

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