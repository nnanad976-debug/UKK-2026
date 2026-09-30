<?php

include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    if ($status_aktif == 1) {
        mysqli_query(
            $koneksi,
            "UPDATE t_tahun_ajaran
             SET status_aktif = 0"
        );
    }

    $query_simpan = mysqli_query(
        $koneksi,
        "INSERT INTO t_tahun_ajaran
        (
            nama,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES
        (
            '$nama',
            '$tanggal_mulai',
            '$tanggal_selesai',
            '$status_aktif'
        )"
    );

    if ($query_simpan) {
        echo "Data tahun ajaran berhasil disimpan!";
    } else {
        echo "Data tahun ajaran gagal disimpan!";
    }
}

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_tahun_ajaran
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Kelola Tahun Ajaran</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- HANYA MENGUBAH WARNA BOOTSTRAP -->
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

    <a href="../dashboard.php"class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <h2>Kelola Tahun Ajaran</h2>

    <p class="text-muted">
        Kelola data tahun ajaran.
    </p>

    <hr>

    <h3>Tambah Tahun Ajaran</h3>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">
                Tahun Ajaran
            </label>

            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Contoh: 2026/2027"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Tanggal Mulai
            </label>

            <input
                type="date"
                name="tanggal_mulai"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Tanggal Selesai
            </label>

            <input
                type="date"
                name="tanggal_selesai"
                class="form-control"
                required>

        </div>

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

        <button
            type="submit"
            name="simpan"
            class="btn btn-primary">

            Tambah Tahun Ajaran

        </button>

    </form>

    <hr class="my-4">

    <h3>Data Tahun Ajaran</h3>

    <div class="table-responsive">

        <table class="table table-bordered mt-3">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Tahun Ajaran</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while ($tahun = mysqli_fetch_assoc($query)) {

            ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <?= $tahun['nama']; ?>
                    </td>

                    <td>
                        <?= $tahun['tanggal_mulai']; ?>
                    </td>

                    <td>
                        <?= $tahun['tanggal_selesai']; ?>
                    </td>

                    <td>

                        <?php

                        if ($tahun['status_aktif'] == 1) {

                            echo "Aktif";

                        } else {

                            echo "Tidak Aktif";

                        }

                        ?>

                    </td>

                    <td>

                        <a
                            href="edit_tahun_ajaran.php?id=<?= $tahun['id']; ?>"
                            class="btn btn-sm btn-primary">

                            Edit

                        </a>

                        <a
                            href="hapus_tahun_ajaran.php?id=<?= $tahun['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">

                            Hapus

                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
</script>

</body>
</html>