<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {

    echo "Anda tidak memiliki akses";

    exit;
}


/* =========================
   PROSES SIMPAN
========================= */

if (isset($_POST['simpan'])) {

    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $guru_id = $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_wali_kelas
        (
            tahun_ajaran_id,
            kelas_id,
            guru_id,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES
        (
            '$tahun_ajaran_id',
            '$kelas_id',
            '$guru_id',
            '$tanggal_mulai',
            '$tanggal_selesai',
            1
        )"
    );

    if ($query_simpan) {

        echo "Data berhasil disimpan!";

    } else {

        echo "Data gagal disimpan!";

    }
}


/* =========================
   DATA TAHUN AJARAN
========================= */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran
     WHERE nama = '2026/2027'
     LIMIT 1"
);


/* =========================
   DATA KELAS
========================= */

$query_kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY id ASC"
);


/* =========================
   DATA GURU
========================= */

$query_guru = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);


/* =========================
   DATA WALI KELAS
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT
        wk.id,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        g.nip,
        g.nama AS nama_guru,
        wk.tanggal_mulai,
        wk.tanggal_selesai,
        wk.status_aktif

    FROM t_wali_kelas wk

    INNER JOIN t_tahun_ajaran ta
        ON wk.tahun_ajaran_id = ta.id

    INNER JOIN t_kelas k
        ON wk.kelas_id = k.id

    INNER JOIN t_guru g
        ON wk.guru_id = g.id

    ORDER BY wk.id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Kelola Wali Kelas</title>

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

    <h2>Kelola Wali Kelas</h2>

    <p class="text-muted">
        Kelola data wali kelas.
    </p>

    <hr>


    <!-- TAMBAH WALI KELAS -->

    <h3>Tambah Wali Kelas</h3>

    <form method="POST">


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

                <?php while ($data = mysqli_fetch_assoc($query_tahun)) { ?>

                    <option value="<?= $data['id']; ?>">

                        <?= $data['nama']; ?>

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

                <?php while ($data = mysqli_fetch_assoc($query_kelas)) { ?>

                    <option value="<?= $data['id']; ?>">

                        <?= $data['nama']; ?> -
                        <?= $data['tingkat']; ?> -
                        <?= $data['jurusan']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- GURU -->

        <div class="mb-3">

            <label class="form-label">
                Guru
            </label>

            <select
                name="guru_id"
                class="form-select"
                required>

                <option value="">
                    -- Pilih Guru --
                </option>

                <?php while ($data = mysqli_fetch_assoc($query_guru)) { ?>

                    <option value="<?= $data['id']; ?>">

                        <?= $data['nip']; ?> -
                        <?= $data['nama']; ?>

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
                class="form-control">

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


    <!-- DATA WALI KELAS -->

    <h3>Data Wali Kelas</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Tahun Ajaran</th>
                    <th>Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
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
                        <?= $data['tahun_ajaran']; ?>
                    </td>

                    <td>
                        <?= $data['nama_kelas']; ?>
                    </td>

                    <td>
                        <?= $data['tingkat']; ?>
                    </td>

                    <td>
                        <?= $data['jurusan']; ?>
                    </td>

                    <td>
                        <?= $data['nip']; ?>
                    </td>

                    <td>
                        <?= $data['nama_guru']; ?>
                    </td>

                    <td>
                        <?= $data['tanggal_mulai']; ?>
                    </td>

                    <td>
                        <?= $data['tanggal_selesai']; ?>
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