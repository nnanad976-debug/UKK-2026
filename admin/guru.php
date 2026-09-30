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
    "SELECT *
     FROM t_guru
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Guru</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- WARNA DISAMAKAN DENGAN TAHUN AJARAN -->
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

    <h2>Kelola Guru</h2>

    <p class="text-muted">
        Kelola data guru.
    </p>

    <hr>


    <!-- TAMBAH DATA -->

    <h3>Tambah Data Guru</h3>

    <form method="POST">


        <!-- NIP -->

        <div class="mb-3">

            <label class="form-label">
                NIP
            </label>

            <input
                type="text"
                name="nip"
                class="form-control"
                required>

        </div>


        <!-- NAMA -->

        <div class="mb-3">

            <label class="form-label">
                Nama Guru
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                required>

        </div>


        <!-- EMAIL -->

        <div class="mb-3">

            <label class="form-label">
                Email
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                required>

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

            Tambah Guru

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA GURU -->

    <h3>Data Guru</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

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

                        <a
                            href="edit_guru.php?id=<?= $guru['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_guru.php?id=<?= $guru['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus guru ini?')">

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