<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {

    echo "Anda tidak memiliki akses";

    exit;
}


if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    mysqli_query($koneksi, "

        INSERT INTO t_kelas_siswa

        (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif)

        VALUES

        ('$siswa_id', '$tahun_ajaran_id', '$kelas_id',
         '$tanggal_mulai', '$tanggal_selesai', 1)

    ");

    header("Location: penempatan_siswa.php");

    exit;
}


/* =========================
   DATA SISWA
========================= */

$siswa = mysqli_query($koneksi, "

    SELECT *

    FROM t_siswa

    WHERE status_aktif = 1

    ORDER BY nama ASC

");


/* =========================
   TAHUN AJARAN: TERLAMA → TERBARU
========================= */

$tahun = mysqli_query($koneksi, "

    SELECT *

    FROM t_tahun_ajaran

    ORDER BY

        CAST(LEFT(nama, 4) AS UNSIGNED) ASC

");


/* =========================
   DATA KELAS
========================= */

$kelas = mysqli_query($koneksi, "

    SELECT 

        MIN(id) AS id,

        tingkat,

        jurusan

    FROM t_kelas

    WHERE status_aktif = 1

    GROUP BY tingkat, jurusan

    ORDER BY

        FIELD(jurusan, 'RPL', 'TKJ', 'BD', 'TKR', 'TSM'),

        FIELD(tingkat, 'X', 'XI', 'XII')

");


/* =========================
   DATA PENEMPATAN
========================= */

$data = mysqli_query($koneksi, "

    SELECT

        ks.id,

        s.nis,

        s.nama AS nama_siswa,

        ta.nama AS tahun_ajaran,

        k.tingkat,

        k.jurusan,

        ks.tanggal_mulai,

        ks.tanggal_selesai,

        ks.status_aktif

    FROM t_kelas_siswa ks

    JOIN t_siswa s

        ON ks.siswa_id = s.id

    JOIN t_tahun_ajaran ta

        ON ks.tahun_ajaran_id = ta.id

    JOIN t_kelas k

        ON ks.kelas_id = k.id

    ORDER BY ks.id DESC

");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Penempatan Siswa</title>

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

    <h2>Penempatan Siswa</h2>

    <p class="text-muted">
        Kelola penempatan siswa berdasarkan tahun ajaran dan kelas.
    </p>

    <hr>


    <!-- TAMBAH PENEMPATAN -->

    <h3>Tambah Penempatan</h3>

    <form method="POST">


        <!-- SISWA -->

        <div class="mb-3">

            <label class="form-label">
                Siswa
            </label>

            <select
                name="siswa_id"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Siswa --
                </option>

                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                    <option value="<?= $s['id']; ?>">

                        <?= $s['nis']; ?> -
                        <?= $s['nama']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- TAHUN AJARAN -->

        <div class="mb-3">

            <label class="form-label">
                Tahun Ajaran
            </label>

            <select
                name="tahun_ajaran_id"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Tahun Ajaran --
                </option>

                <?php while ($t = mysqli_fetch_assoc($tahun)) { ?>

                    <option value="<?= $t['id']; ?>">

                        <?= $t['nama']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- KELAS -->

        <div class="mb-3">

            <label class="form-label">
                Kelas
            </label>

            <select
                name="kelas_id"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Kelas --
                </option>

                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                    <option value="<?= $k['id']; ?>">

                        <?= $k['tingkat']; ?>
                        <?= $k['jurusan']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- TANGGAL MULAI -->

        <div class="mb-3">

            <label class="form-label">
                Tanggal Mulai
            </label>

            <input
                type="date"
                name="tanggal_mulai"
                value="2026-07-01"
                class="form-control"
                required>

        </div>


        <!-- TANGGAL SELESAI -->

        <div class="mb-3">

            <label class="form-label">
                Tanggal Selesai
            </label>

            <input
                type="date"
                name="tanggal_selesai"
                value="2027-06-30"
                class="form-control"
                required>

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            name="simpan"
            class="btn btn-primary">

            Simpan

        </button>

    </form>


    <hr class="my-4">


    <!-- DATA PENEMPATAN -->

    <h3>Data Penempatan Siswa</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Tahun Ajaran</th>
                    <th>Kelas</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while ($d = mysqli_fetch_assoc($data)) {

            ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <?= $d['nis']; ?>
                    </td>

                    <td>
                        <?= $d['nama_siswa']; ?>
                    </td>

                    <td>
                        <?= $d['tahun_ajaran']; ?>
                    </td>

                    <td>

                        <?= $d['tingkat']; ?>
                        <?= $d['jurusan']; ?>

                    </td>

                    <td>
                        <?= $d['tanggal_mulai']; ?>
                    </td>

                    <td>
                        <?= $d['tanggal_selesai']; ?>
                    </td>

                    <td>

                        <?= $d['status_aktif'] == 1
                            ? 'Aktif'
                            : 'Tidak Aktif'; ?>

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