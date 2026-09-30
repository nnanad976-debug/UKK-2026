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
// SIMPAN PELANGGARAN
// =========================

if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $kelas_id = $_POST['kelas_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];


    // DATA SISWA

    $query_siswa_data = mysqli_query(
        $koneksi,
        "SELECT nama
         FROM t_siswa
         WHERE id='$siswa_id'
         LIMIT 1"
    );

    $data_siswa = mysqli_fetch_assoc($query_siswa_data);

    $nama_siswa = $data_siswa['nama'] ?? '';


    // DATA PELANGGARAN

    $query_pelanggaran = mysqli_query(
        $koneksi,
        "SELECT
            id,
            nama,
            poin,
            pelanggaran_kategori_id
         FROM t_pelanggaran
         WHERE id='$pelanggaran_id'
         LIMIT 1"
    );

    $data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran);

    $nama_pelanggaran = $data_pelanggaran['nama'] ?? '';
    $poin = $data_pelanggaran['poin'] ?? 0;
    $pelanggaran_kategori_id =
        $data_pelanggaran['pelanggaran_kategori_id'] ?? 0;


    // DATA KELAS

    $query_kelas_data = mysqli_query(
        $koneksi,
        "SELECT nama, tingkat, jurusan
         FROM t_kelas
         WHERE id='$kelas_id'
         LIMIT 1"
    );

    $data_kelas = mysqli_fetch_assoc($query_kelas_data);

    $nama_kelas = '';

    if ($data_kelas) {

        $nama_kelas =
            $data_kelas['nama'] . ' - ' .
            $data_kelas['tingkat'] . ' - ' .
            $data_kelas['jurusan'];

    }


    // SIMPAN

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_pelanggaran_siswa
        (
            siswa_id,
            nama_siswa,
            pelanggaran_id,
            nama_pelanggaran,
            pelanggaran_kategori_id,
            nama_kategori,
            tanggal,
            keterangan,
            poin,
            guru_id,
            nama_guru,
            tindakan,
            status,
            kelas_id,
            nama_kelas
        )
        VALUES
        (
            '$siswa_id',
            '$nama_siswa',
            '$pelanggaran_id',
            '$nama_pelanggaran',
            '$pelanggaran_kategori_id',
            '',
            '$tanggal',
            '$keterangan',
            '$poin',
            '$guru_id',
            '$nama_guru',
            '',
            'Diproses',
            '$kelas_id',
            '$nama_kelas'
        )"
    );


    if ($query_simpan) {

        echo "Pelanggaran berhasil dicatat!";

    } else {

        echo "Pelanggaran gagal dicatat!";

    }

}


// =========================
// DATA SISWA
// =========================

$query_siswa = mysqli_query(
    $koneksi,
    "SELECT DISTINCT
        s.id,
        s.nis,
        s.nama
     FROM t_siswa s
     INNER JOIN t_kelas_siswa ks
        ON s.id = ks.siswa_id
     WHERE s.status_aktif = 1
     ORDER BY s.nama ASC"
);


// =========================
// DATA KELAS
// =========================

$query_kelas = mysqli_query(
    $koneksi,
    "SELECT
        MIN(id) AS id,
        tingkat,
        jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     GROUP BY tingkat, jurusan
     ORDER BY
        FIELD(jurusan, 'RPL', 'TKJ', 'BD', 'TKR', 'TSM'),
        FIELD(tingkat, 'X', 'XI', 'XII')"
);


// =========================
// DATA PELANGGARAN
// =========================

$query_jenis = mysqli_query(
    $koneksi,
    "SELECT
        id,
        kode,
        nama,
        poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);


// =========================
// RIWAYAT GURU
// =========================

$query_riwayat = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_siswa
     WHERE guru_id='$guru_id'
     ORDER BY tanggal DESC, id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Catat Pelanggaran</title>

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

        main {
            background-color: #ffffff;
            min-height: 100vh;
        }

        .judul {
            color: #1f6479;
        }

    </style>

</head>

<body>

<div class="container py-4">

    <!-- KEMBALI -->

    <a
        href="../dashboard.php"
        class="btn btn-secondary mb-3">

        Kembali ke Dashboard

    </a>


    <!-- JUDUL -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h2 class="judul">
                Catat Pelanggaran
            </h2>

            <p class="text-muted mb-0">
                Catat pelanggaran siswa.
            </p>

        </div>

    </div>


    <!-- FORM -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h4 class="mb-4">
                Tambah Pelanggaran
            </h4>


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

                        <?php while ($s = mysqli_fetch_assoc($query_siswa)) { ?>

                            <option value="<?= $s['id']; ?>">

                                <?= $s['nis']; ?> -
                                <?= $s['nama']; ?>

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

                        <?php while ($k = mysqli_fetch_assoc($query_kelas)) { ?>

                            <option value="<?= $k['id']; ?>">

                                <?= $k['tingkat']; ?> -
                                <?= $k['jurusan']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- PELANGGARAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Jenis Pelanggaran
                    </label>

                    <select
                        name="pelanggaran_id"
                        class="form-select"
                        required>

                        <option value="">
                            -- Pilih Pelanggaran --
                        </option>

                        <?php while ($p = mysqli_fetch_assoc($query_jenis)) { ?>

                            <option value="<?= $p['id']; ?>">

                                <?= $p['kode']; ?> -
                                <?= $p['nama']; ?>
                                (<?= $p['poin']; ?> poin)

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- TANGGAL -->

                <div class="mb-3">

                    <label class="form-label">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= date('Y-m-d'); ?>"
                        required>

                </div>


                <!-- KETERANGAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="4"
                        placeholder="Masukkan keterangan pelanggaran"
                        required></textarea>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary">

                    Simpan Pelanggaran

                </button>

            </form>

        </div>

    </div>


    <!-- DATA PELANGGARAN -->

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="mb-3">
                Riwayat Pelanggaran
            </h4>


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
                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query_riwayat)) {

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

</body>

</html>