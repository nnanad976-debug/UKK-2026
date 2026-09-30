<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'guru') {

    echo "Anda tidak memiliki akses";
    exit;

}

$user_id = $_SESSION['user_id'];


// =========================
// DATA GURU
// =========================

$query_guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id='$user_id'
     LIMIT 1"
);

$data_guru = mysqli_fetch_assoc($query_guru);

$guru_id = $data_guru['id'] ?? 0;
$nama_guru = $data_guru['nama'] ?? $_SESSION['nama'];


// =========================
// DATA LAPORAN
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT
        ps.id,
        ps.tanggal,
        ps.nama_siswa,
        ps.nama_kelas,
        ps.nama_pelanggaran,
        ps.poin,
        ps.keterangan,
        ps.tindakan,
        ps.status
     FROM t_pelanggaran_siswa ps
     WHERE ps.guru_id='$guru_id'
     ORDER BY ps.tanggal DESC, ps.id DESC"
);


// =========================
// TOTAL POIN
// =========================

$query_total = mysqli_query(
    $koneksi,
    "SELECT SUM(poin) AS total
     FROM t_pelanggaran_siswa
     WHERE guru_id='$guru_id'"
);

$data_total = mysqli_fetch_assoc($query_total);

$total_poin = $data_total['total'] ?? 0;


// =========================
// TOTAL PELANGGARAN
// =========================

$query_jumlah = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_pelanggaran_siswa
     WHERE guru_id='$guru_id'"
);

$data_jumlah = mysqli_fetch_assoc($query_jumlah);

$total_pelanggaran = $data_jumlah['total'] ?? 0;

?>

<!DOCTYPE html>
<html>

<head>

    <title>Laporan Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background-color: #e8dddd;
        }

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

        .judul {
            color: #1f6479;
        }

        .card {
            border: none;
        }

        .table thead th {
            background-color: #8eaabd;
            color: white;
        }

    </style>

</head>

<body>

<div class="container py-4">


    <!-- KEMBALI -->

    <div class="mb-3 no-print">

        <a
            href="../dashboard.php"
            class="btn btn-secondary">

            Kembali ke Dashboard

        </a>

    </div>


    <!-- JUDUL -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h2 class="judul">
                Laporan Pelanggaran
            </h2>

            <p class="text-muted mb-0">

                Laporan pelanggaran siswa yang dicatat oleh
                <?= $nama_guru; ?>.

            </p>

        </div>

    </div>


    <!-- RINGKASAN -->

    <div class="row g-3 mb-4">


        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Pelanggaran
                    </h6>

                    <h3>
                        <?= $total_pelanggaran; ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Poin
                    </h6>

                    <h3>
                        <?= $total_poin; ?>
                    </h3>

                </div>

            </div>

        </div>


    </div>


    <!-- DATA LAPORAN -->

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h4 class="mb-0">
                    Data Laporan Pelanggaran
                </h4>

                <button
                    onclick="window.print()"
                    class="btn btn-primary no-print">

                    Cetak Laporan

                </button>

            </div>


            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th>Siswa</th>

                            <th>Kelas</th>

                            <th>Pelanggaran</th>

                            <th>Poin</th>

                            <th>Keterangan</th>

                            <th>Tindakan</th>

                            <th>Status</th>

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
                                <?= $data['tanggal']; ?>
                            </td>

                            <td>
                                <?= $data['nama_siswa']; ?>
                            </td>

                            <td>
                                <?= $data['nama_kelas']; ?>
                            </td>

                            <td>
                                <?= $data['nama_pelanggaran']; ?>
                            </td>

                            <td>
                                <?= $data['poin']; ?>
                            </td>

                            <td>
                                <?= $data['keterangan']; ?>
                            </td>

                            <td>
                                <?= $data['tindakan']; ?>
                            </td>

                            <td>
                                <?= $data['status']; ?>
                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
</script>

<style>

@media print {

    body {
        background-color: white;
    }

    .no-print {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
    }

}

</style>

</body>

</html>