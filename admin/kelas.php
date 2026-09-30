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
    "SELECT *
     FROM t_kelas
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Kelas</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- WARNA SAMA DENGAN TAHUN AJARAN -->
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

    <h2>Kelola Kelas</h2>

    <p class="text-muted">
        Kelola data kelas.
    </p>

    <hr>


    <!-- TAMBAH DATA -->

    <h3>Tambah Data Kelas</h3>

    <form method="POST">


        <!-- NAMA KELAS -->

        <div class="mb-3">

            <label class="form-label">
                Nama Kelas
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Contoh: RPL"
                required>

        </div>


        <!-- TINGKAT -->

        <div class="mb-3">

            <label class="form-label">
                Tingkat
            </label>

            <select
                name="tingkat"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Tingkat --
                </option>

                <option value="X">
                    X
                </option>

                <option value="XI">
                    XI
                </option>

                <option value="XII">
                    XII
                </option>

            </select>

        </div>


        <!-- JURUSAN -->

        <div class="mb-3">

            <label class="form-label">
                Jurusan
            </label>

            <select
                name="jurusan"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Jurusan --
                </option>

                <option value="RPL">
                    RPL
                </option>

                <option value="TKJ">
                    TKJ
                </option>

                <option value="BD">
                    BD
                </option>

                <option value="TKR">
                    TKR
                </option>

                <option value="TSM">
                    TSM
                </option>

            </select>

        </div>


        <!-- STATUS -->

        <div class="mb-3">

            <label class="form-label">
                Status
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

            Tambah Kelas

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA KELAS -->

    <h3>Data Kelas</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

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

                        <a
                            href="edit_kelas.php?id=<?= $kelas['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_kelas.php?id=<?= $kelas['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus kelas ini?')">

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