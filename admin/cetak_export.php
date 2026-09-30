<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {

    echo "Anda tidak memiliki akses";
    exit;

}

$siswa_id = $_GET['siswa_id'] ?? '';

$siswa = mysqli_query(
    $koneksi,
    "SELECT * FROM t_siswa
     WHERE id='$siswa_id'"
);

$data_siswa = mysqli_fetch_assoc($siswa);

$query_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif=1
     ORDER BY nama"
);

$kelas = '-';

if ($siswa_id != '') {

    $q_kelas = mysqli_query(
        $koneksi,
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

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_siswa
     WHERE siswa_id='$siswa_id'
     ORDER BY tanggal DESC"
);

$total = mysqli_query(
    $koneksi,
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

        .kartu {
            background-color: #ffffff;
            border-radius: 8px;
        }

        .judul-kartu {
            background-color: #1f6479;
            color: white;
        }

        .table thead th {
            background-color: #8eaabd;
            color: white;
        }

        .table-bordered > :not(caption) > * > * {
            border-color: #d6d6d6;
        }

        .identitas {
            background-color: #f1f5f7;
            font-weight: 600;
        }

        .total-poin {
            background-color: #e8dddd;
            font-weight: bold;
        }

        @media print {

            body {
                background-color: white;
            }

            .no-print {
                display: none !important;
            }

            .kartu {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }

        }

    </style>

</head>

<body>

<div class="container py-4">

    <!-- BAGIAN ATAS -->

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">

        <a
            href="../dashboard.php"
            class="btn btn-secondary">

            Kembali ke Dashboard

        </a>

    </div>


    <div class="card shadow-sm border-0 mb-4 no-print">

        <div class="card-body">

            <h2 class="mb-1">
                Cetak Kartu Pelanggaran Siswa
            </h2>

            <p class="text-muted mb-4">
                Pilih siswa untuk melihat kartu pelanggaran.
            </p>


            <form method="GET">

                <div class="row align-items-end">

                    <div class="col-md-8">

                        <label class="form-label">
                            Pilih Siswa
                        </label>

                        <select
                            name="siswa_id"
                            class="form-select"
                            required>

                            <option value="">
                                -- Pilih Siswa --
                            </option>

                            <?php while ($s = mysqli_fetch_assoc($query_siswa)) { ?>

                                <option
                                    value="<?= $s['id']; ?>"
                                    <?= $siswa_id == $s['id'] ? 'selected' : ''; ?>>

                                    <?= $s['nis']; ?> - <?= $s['nama']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>


                    <div class="col-md-4 mt-3 mt-md-0">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Tampilkan

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <?php if ($data_siswa) { ?>


    <!-- KARTU PELANGGARAN -->

    <div class="card kartu shadow-sm border-0">

        <!-- JUDUL -->

        <div class="card-header judul-kartu text-center py-4">

            <h2 class="mb-1">
                KARTU PELANGGARAN SISWA
            </h2>

            <strong>
                SISTEM INFORMASI PELANGGARAN SISWA
            </strong>

        </div>


        <div class="card-body p-4">


            <!-- IDENTITAS -->

            <h5 class="mb-3">
                Identitas Siswa
            </h5>

            <div class="table-responsive">

                <table class="table table-bordered mb-4">

                    <tr>

                        <td class="identitas" width="15%">
                            NIS
                        </td>

                        <td width="35%">
                            <?= $data_siswa['nis']; ?>
                        </td>

                        <td class="identitas" width="15%">
                            NISN
                        </td>

                        <td width="35%">
                            <?= $data_siswa['nisn']; ?>
                        </td>

                    </tr>


                    <tr>

                        <td class="identitas">
                            Nama
                        </td>

                        <td colspan="3">
                            <?= $data_siswa['nama']; ?>
                        </td>

                    </tr>


                    <tr>

                        <td class="identitas">
                            Jenis Kelamin
                        </td>

                        <td>

                            <?= $data_siswa['jenis_kelamin'] == 'L'
                                ? 'Laki-laki'
                                : 'Perempuan'; ?>

                        </td>

                        <td class="identitas">
                            Kelas
                        </td>

                        <td>
                            <?= $kelas; ?>
                        </td>

                    </tr>


                    <tr>

                        <td class="identitas">
                            Alamat
                        </td>

                        <td colspan="3">
                            <?= $data_siswa['alamat']; ?>
                        </td>

                    </tr>

                </table>

            </div>


            <!-- RIWAYAT -->

            <h5 class="mb-3">
                Riwayat Pelanggaran
            </h5>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead>

                        <tr>

                            <th width="8%" class="text-center">
                                No
                            </th>

                            <th width="18%">
                                Tanggal
                            </th>

                            <th>
                                Pelanggaran
                            </th>

                            <th width="12%" class="text-center">
                                Poin
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    while ($p = mysqli_fetch_assoc($pelanggaran)) {

                    ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= $p['tanggal']; ?>
                            </td>

                            <td>

                                <?= $p['nama_pelanggaran']; ?>

                                <?php if ($p['keterangan']) { ?>

                                    <br>

                                    <small class="text-muted">
                                        <?= $p['keterangan']; ?>
                                    </small>

                                <?php } ?>

                            </td>

                            <td class="text-center">
                                <?= $p['poin']; ?>
                            </td>

                        </tr>

                    <?php } ?>


                        <tr class="total-poin">

                            <td
                                colspan="3"
                                class="text-end">

                                TOTAL POIN

                            </td>

                            <td class="text-center">

                                <?= $total_poin; ?>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- TANDA TANGAN -->

            <div class="row mt-5">

                <div class="col-6 text-center">

                    Mengetahui,<br>

                    Wali Kelas

                    <br><br><br><br>

                    (____________________)

                </div>


                <div class="col-6 text-center">

                    Guru<br>

                    Pencatat Pelanggaran

                    <br><br><br><br>

                    (____________________)

                </div>

            </div>


            <!-- CETAK -->

            <div class="text-center mt-4 no-print">

                <button
                    onclick="window.print()"
                    class="btn btn-primary">

                    Cetak

                </button>

            </div>


        </div>

    </div>


    <?php } ?>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
</script>

</body>

</html>