<?php

include "middleware/auth.php";

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background-color: #e8dddd;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #1f6479;
        }

        .sidebar h4 {
            color: white;
        }

        .sidebar .nav-link {
            color: white;
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #8eaabd;
            color: white;
            border-radius: 6px;
        }

        main {
            background-color: white;
            min-height: 100vh;
        }

        .card-menu {
            border: none;
            color: white;
        }

        .menu-1 {
            background-color: #9bb8cc;
        }

        .menu-2 {
            background-color: #aaa7a4;
        }

        .menu-3 {
            background-color: #8eaabd;
        }

        .menu-4 {
            background-color: #6f96aa;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 sidebar p-3">

            <h4 class="mb-4">
                Sistem Pelanggaran
            </h4>


            <div class="nav flex-column nav-pills">


                <!-- =========================
                     MENU ADMIN
                ========================== -->

                <?php if ($role == 'admin') { ?>

                    <a
                        href="admin/guru.php"
                        class="nav-link">

                        Kelola Guru

                    </a>


                    <a
                        href="admin/siswa.php"
                        class="nav-link">

                        Kelola Siswa

                    </a>


                    <a
                        href="admin/kelas.php"
                        class="nav-link">

                        Kelola Kelas

                    </a>


                    <a
                        href="admin/tahun_ajaran.php"
                        class="nav-link">

                        Tahun Ajaran

                    </a>


                    <a
                        href="admin/penempatan_siswa.php"
                        class="nav-link">

                        Penempatan Siswa

                    </a>


                    <a
                        href="admin/wali_kelas.php"
                        class="nav-link">

                        Wali Kelas

                    </a>


                    <a
                        href="admin/kategori_pelanggaran.php"
                        class="nav-link">

                        Kategori Pelanggaran

                    </a>


                    <a
                        href="admin/jenis_pelanggaran.php"
                        class="nav-link">

                        Jenis Pelanggaran

                    </a>


                    <a
                        href="admin/cetak_export.php"
                        class="nav-link">

                        Cetak / Export

                    </a>

                <?php } ?>


                <!-- =========================
                     MENU GURU
                ========================== -->

                <?php if ($role == 'guru') { ?>

                    <a
                        href="guru/catat_pelanggaran.php"
                        class="nav-link">

                        Catat Pelanggaran

                    </a>


                    <a
                        href="guru/tindakan.php"
                        class="nav-link">

                        Tindakan

                    </a>


                    <a
                        href="guru/laporan.php"
                        class="nav-link">

                        Laporan

                    </a>


                    <a
                        href="guru/riwayat.php"
                        class="nav-link">

                        Riwayat

                    </a>

                <?php } ?>


                <!-- LOGOUT -->

                <a
                    href="logout.php"
                    class="nav-link">

                    Logout

                </a>

            </div>

        </div>


        <!-- =========================
             CONTENT
        ========================== -->

        <main class="col-md-9 col-lg-10 p-4">


            <h2>
                Dashboard
            </h2>


            <p class="text-muted">

                Selamat datang, <?= $nama; ?>

            </p>


            <?php if ($role == 'admin') { ?>


                <div class="row g-3 mt-3">


                    <!-- GURU -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-1">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Kelola Guru
                                </h5>

                                <p class="card-text">
                                    Kelola data guru.
                                </p>

                                <a
                                    href="admin/guru.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- SISWA -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-2">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Kelola Siswa
                                </h5>

                                <p class="card-text">
                                    Kelola data siswa.
                                </p>

                                <a
                                    href="admin/siswa.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- KELAS -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-3">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Kelola Kelas
                                </h5>

                                <p class="card-text">
                                    Kelola data kelas.
                                </p>

                                <a
                                    href="admin/kelas.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- TAHUN AJARAN -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-4">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Tahun Ajaran
                                </h5>

                                <p class="card-text">
                                    Kelola tahun ajaran.
                                </p>

                                <a
                                    href="admin/tahun_ajaran.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- PENEMPATAN -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-1">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Penempatan Siswa
                                </h5>

                                <p class="card-text">
                                    Kelola penempatan siswa.
                                </p>

                                <a
                                    href="admin/penempatan_siswa.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- WALI KELAS -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-2">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Wali Kelas
                                </h5>

                                <p class="card-text">
                                    Kelola wali kelas.
                                </p>

                                <a
                                    href="admin/wali_kelas.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- KATEGORI -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-3">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Kategori Pelanggaran
                                </h5>

                                <p class="card-text">
                                    Kelola kategori pelanggaran.
                                </p>

                                <a
                                    href="admin/kategori_pelanggaran.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- JENIS -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-4">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Jenis Pelanggaran
                                </h5>

                                <p class="card-text">
                                    Kelola jenis pelanggaran.
                                </p>

                                <a
                                    href="admin/jenis_pelanggaran.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- CETAK -->

                    <div class="col-md-4">

                        <div class="card card-menu menu-1">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Cetak / Export
                                </h5>

                                <p class="card-text">
                                    Cetak dan export data.
                                </p>

                                <a
                                    href="admin/cetak_export.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>

                </div>


            <?php } ?>


            <?php if ($role == 'guru') { ?>


                <div class="row g-3 mt-3">


                    <!-- CATAT PELANGGARAN -->

                    <div class="col-md-6">

                        <div class="card card-menu menu-1">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Catat Pelanggaran
                                </h5>

                                <p class="card-text">
                                    Mencatat pelanggaran siswa.
                                </p>

                                <a
                                    href="guru/catat_pelanggaran.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- TINDAKAN -->

                    <div class="col-md-6">

                        <div class="card card-menu menu-2">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Tindakan
                                </h5>

                                <p class="card-text">
                                    Mengelola tindakan pelanggaran.
                                </p>

                                <a
                                    href="guru/tindakan.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- LAPORAN -->

                    <div class="col-md-6">

                        <div class="card card-menu menu-3">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Laporan
                                </h5>

                                <p class="card-text">
                                    Melihat laporan pelanggaran.
                                </p>

                                <a
                                    href="guru/laporan.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- RIWAYAT -->

                    <div class="col-md-6">

                        <div class="card card-menu menu-4">

                            <div class="card-body">

                                <h5 class="card-title">
                                    Riwayat
                                </h5>

                                <p class="card-text">
                                    Melihat riwayat pelanggaran.
                                </p>

                                <a
                                    href="guru/riwayat.php"
                                    class="btn btn-light">

                                    Buka

                                </a>

                            </div>

                        </div>

                    </div>

                </div>


            <?php } ?>


        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
</script>

</body>

</html>