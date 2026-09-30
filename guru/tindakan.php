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
// SIMPAN TINDAKAN
// =========================

if (isset($_POST['simpan'])) {

    $id = $_POST['id'];
    $tindakan = $_POST['tindakan'];
    $status = $_POST['status'];

    $query_update = mysqli_query(
        $koneksi,
        "UPDATE t_pelanggaran_siswa
         SET
            tindakan='$tindakan',
            status='$status'
         WHERE id='$id'
         AND guru_id='$guru_id'"
    );

    if ($query_update) {

        echo "Tindakan berhasil disimpan!";

    } else {

        echo "Tindakan gagal disimpan!";

    }

}


// =========================
// DATA PELANGGARAN
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        keterangan,
        tindakan,
        status
     FROM t_pelanggaran_siswa
     WHERE guru_id='$guru_id'
     ORDER BY tanggal DESC, id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Tindakan Pelanggaran</title>

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

    <div class="mb-3">

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
                Tindakan Pelanggaran
            </h2>

            <p class="text-muted mb-0">
                Kelola tindakan dan status pelanggaran siswa.
            </p>

        </div>

    </div>


    <!-- DATA PELANGGARAN -->

    <div class="card shadow-sm">

        <div class="card-body">

            <h4 class="mb-3">
                Data Pelanggaran
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

                            <th>Keterangan</th>

                            <th>Tindakan</th>

                            <th>Status</th>

                            <th>Aksi</th>

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

                                <?= $data['tindakan'] ?: '-'; ?>

                            </td>

                            <td>

                                <?= $data['status']; ?>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTindakan<?= $data['id']; ?>">

                                    Tindakan

                                </button>

                            </td>

                        </tr>


                        <!-- MODAL TINDAKAN -->

                        <div
                            class="modal fade"
                            id="modalTindakan<?= $data['id']; ?>"
                            tabindex="-1">

                            <div class="modal-dialog">

                                <div class="modal-content">


                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Tindakan Pelanggaran
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>


                                    <form method="POST">

                                        <div class="modal-body">


                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $data['id']; ?>">


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Siswa
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="<?= $data['nama_siswa']; ?>"
                                                    readonly>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Pelanggaran
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="<?= $data['nama_pelanggaran']; ?>"
                                                    readonly>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Tindakan
                                                </label>

                                                <textarea
                                                    name="tindakan"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Masukkan tindakan yang diberikan"
                                                    required><?= $data['tindakan']; ?></textarea>

                                            </div>


                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Status
                                                </label>

                                                <select
                                                    name="status"
                                                    class="form-select"
                                                    required>

                                                    <option
                                                        value="Diproses"
                                                        <?= $data['status'] == 'Diproses' ? 'selected' : ''; ?>>

                                                        Diproses

                                                    </option>

                                                    <option
                                                        value="Ditindaklanjuti"
                                                        <?= $data['status'] == 'Ditindaklanjuti' ? 'selected' : ''; ?>>

                                                        Ditindaklanjuti

                                                    </option>

                                                    <option
                                                        value="Selesai"
                                                        <?= $data['status'] == 'Selesai' ? 'selected' : ''; ?>>

                                                        Selesai

                                                    </option>

                                                </select>

                                            </div>


                                        </div>


                                        <div class="modal-footer">

                                            <button
                                                type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">

                                                Batal

                                            </button>

                                            <button
                                                type="submit"
                                                name="simpan"
                                                class="btn btn-primary">

                                                Simpan

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

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