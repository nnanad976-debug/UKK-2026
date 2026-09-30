<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {

    echo "Anda tidak memiliki akses";

    exit;
}


/* =========================
   PROSES TAMBAH SISWA
========================= */

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_siswa
        (
            nis,
            nisn,
            nama,
            jenis_kelamin,
            tanggal_lahir,
            alamat,
            status_aktif
        )
        VALUES
        (
            '$nis',
            '$nisn',
            '$nama',
            '$jenis_kelamin',
            '$tanggal_lahir',
            '$alamat',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {

        echo "Data siswa berhasil disimpan!";

    } else {

        echo "Data siswa gagal disimpan!";

    }
}


/* =========================
   DATA SISWA
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_siswa
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Siswa</title>

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

    <h2>Kelola Siswa</h2>

    <p class="text-muted">
        Kelola data siswa.
    </p>

    <hr>


    <!-- TAMBAH DATA -->

    <h3>Tambah Data Siswa</h3>

    <form method="POST">


        <div class="mb-3">

            <label class="form-label">
                NIS
            </label>

            <input
                type="text"
                name="nis"
                class="form-control"
                required>

        </div>


        <div class="mb-3">

            <label class="form-label">
                NISN
            </label>

            <input
                type="text"
                name="nisn"
                class="form-control"
                required>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Nama
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                required>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Jenis Kelamin
            </label>

            <select
                name="jenis_kelamin"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Jenis Kelamin --
                </option>

                <option value="L">
                    Laki-laki
                </option>

                <option value="P">
                    Perempuan
                </option>

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Tanggal Lahir
            </label>

            <input
                type="date"
                name="tanggal_lahir"
                class="form-control"
                required>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Alamat
            </label>

            <textarea
                name="alamat"
                class="form-control"
                rows="3"
                required></textarea>

        </div>


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


        <button
            type="submit"
            name="simpan"
            class="btn btn-primary">

            Tambah Siswa

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA SISWA -->

    <h3>Data Siswa</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>NIS</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while ($siswa = mysqli_fetch_assoc($query)) {

            ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <?= $siswa['nis']; ?>
                    </td>

                    <td>
                        <?= $siswa['nisn']; ?>
                    </td>

                    <td>
                        <?= $siswa['nama']; ?>
                    </td>

                    <td>

                        <?php

                        if ($siswa['jenis_kelamin'] == 'L') {

                            echo "Laki-laki";

                        } else {

                            echo "Perempuan";

                        }

                        ?>

                    </td>

                    <td>
                        <?= $siswa['tanggal_lahir']; ?>
                    </td>

                    <td>
                        <?= $siswa['alamat']; ?>
                    </td>

                    <td>

                        <?php

                        if ($siswa['status_aktif'] == 1) {

                            echo "Aktif";

                        } else {

                            echo "Tidak Aktif";

                        }

                        ?>

                    </td>

                    <td>

                        <a
                            href="edit_siswa.php?id=<?= $siswa['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_siswa.php?id=<?= $siswa['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus siswa ini?')">

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