
<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../config/koneksi.php';

if (strtolower(trim((string)($_SESSION['role'] ?? ''))) !== 'admin') {
    http_response_code(403);
    exit('Anda tidak memiliki akses.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_set_charset($koneksi, 'utf8mb4');

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirectStatus(string $status): void
{
    header('Location: siswa.php?status=' . rawurlencode($status));
    exit;
}

$pesan = '';
$jenisPesan = '';
$tampilkanForm = false;

$old = [
    'nis' => '',
    'nisn' => '',
    'nama' => '',
    'jenis_kelamin' => '',
    'tanggal_lahir' => '',
    'alamat' => '',
    'status_aktif' => '1',
];

$statusPesan = [
    'tambah_sukses' => ['Data siswa berhasil disimpan.', 'success'],
    'edit_sukses' => ['Data siswa berhasil diperbarui.', 'success'],
    'hapus_sukses' => ['Data siswa berhasil dihapus.', 'success'],
    'data_terkait' => ['Siswa tidak bisa dihapus karena masih memiliki data terkait.', 'warning'],
    'hapus_gagal' => ['Data siswa gagal dihapus. Periksa data terkait atau database.', 'danger'],
    'id_tidak_valid' => ['ID siswa tidak valid atau data tidak ditemukan.', 'danger'],
    'nis_duplikat' => ['NIS sudah digunakan oleh siswa lain. Gunakan NIS yang berbeda.', 'danger'],
    'nisn_duplikat' => ['NISN sudah digunakan oleh siswa lain. Gunakan NISN yang berbeda.', 'danger'],
];

/* =========================
   PESAN NOTIFIKASI
========================= */

if (isset($_GET['status']) && isset($statusPesan[$_GET['status']])) {
    [$pesan, $jenisPesan] = $statusPesan[$_GET['status']];
}

/* =========================
   PROSES TAMBAH SISWA
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    foreach ($old as $key => $value) {
        $old[$key] = trim((string)($_POST[$key] ?? $value));
    }

    $tampilkanForm = true;

    $nis = $old['nis'];
    $nisn = $old['nisn'];
    $nama = $old['nama'];
    $jenisKelamin = $old['jenis_kelamin'];
    $tanggalLahir = $old['tanggal_lahir'];
    $alamat = $old['alamat'];

    $statusAktif = filter_var(
        $old['status_aktif'],
        FILTER_VALIDATE_INT
    );

    /* VALIDASI TANGGAL LAHIR */

    $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggalLahir);
    $tanggalErrors = DateTime::getLastErrors();

    $tanggalValid = (
        $tanggalObj instanceof DateTime &&
        $tanggalObj->format('Y-m-d') === $tanggalLahir &&
        (
            $tanggalErrors === false ||
            (
                $tanggalErrors['warning_count'] === 0 &&
                $tanggalErrors['error_count'] === 0
            )
        )
    );

    if (
        $nis === '' ||
        $nisn === '' ||
        $nama === '' ||
        !in_array($jenisKelamin, ['L', 'P'], true) ||
        !$tanggalValid ||
        $alamat === '' ||
        !in_array($statusAktif, [0, 1], true)
    ) {
        $pesan = 'Semua data wajib diisi dengan benar. Periksa kembali tanggal lahir dan status siswa.';
        $jenisPesan = 'danger';

    } else {

        try {

            /* CEK DUPLIKASI NIS DAN NISN */

            $stmt = mysqli_prepare(
                $koneksi,
                'SELECT nis, nisn
                 FROM t_siswa
                 WHERE nis = ? OR nisn = ?
                 LIMIT 1'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'ss',
                $nis,
                $nisn
            );

            mysqli_stmt_execute($stmt);

            $hasilCek = mysqli_stmt_get_result($stmt);
            $duplikat = mysqli_fetch_assoc($hasilCek);

            mysqli_stmt_close($stmt);

            if ($duplikat) {

                if ((string)$duplikat['nis'] === $nis) {
                    $pesan = 'NIS sudah digunakan oleh siswa lain. Gunakan NIS yang berbeda.';
                } else {
                    $pesan = 'NISN sudah digunakan oleh siswa lain. Gunakan NISN yang berbeda.';
                }

                $jenisPesan = 'danger';

            } else {

                /* SIMPAN DATA SISWA */

                $stmt = mysqli_prepare(
                    $koneksi,
                    'INSERT INTO t_siswa
                    (
                        nis,
                        nisn,
                        nama,
                        jenis_kelamin,
                        tanggal_lahir,
                        alamat,
                        status_aktif
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?)'
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'ssssssi',
                    $nis,
                    $nisn,
                    $nama,
                    $jenisKelamin,
                    $tanggalLahir,
                    $alamat,
                    $statusAktif
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                redirectStatus('tambah_sukses');
            }

        } catch (mysqli_sql_exception $err) {

            $pesan = 'Data siswa gagal disimpan. Pastikan struktur tabel t_siswa sesuai dan NIS/NISN belum digunakan.';
            $jenisPesan = 'danger';
        }
    }
}

/* =========================
   AMBIL DATA SISWA
========================= */

try {

    $query = mysqli_query(
        $koneksi,
        'SELECT
            id,
            nis,
            nisn,
            nama,
            jenis_kelamin,
            tanggal_lahir,
            alamat,
            status_aktif
         FROM t_siswa
         ORDER BY id DESC'
    );

} catch (mysqli_sql_exception $err) {

    $query = false;
    $pesan = 'Data siswa gagal dimuat. Periksa tabel dan kolom pada t_siswa.';
    $jenisPesan = 'danger';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kelola Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f7fc;
            color: #2f435a;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .brand {
            padding: 0 15px 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 22px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .menu-link {
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
            transition: .2s ease;
        }

        .menu-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .logout .menu-link {
            color: #d9534f;
        }

        .logout .menu-link:hover {
            background: #fff0ef;
            color: #c03935;
        }

        /* KONTEN UTAMA */

        .main {
            margin-left: 282px;
            min-height: 100vh;
            padding: 24px 28px 40px;
        }

        .container {
            width: 100%;
            max-width: none !important;
            margin: 0 auto;
            padding: 0 !important;
        }

        .judul,
        h4 {
            color: #234e78;
        }

        .judul {
            font-size: 26px;
            font-weight: 700;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
        }

        .card-body {
            padding: 24px;
        }

        /* FORM */

        .form-label {
            color: #405873;
            font-weight: 600;
            font-size: 16px;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
            font-size: 16px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        /* TOMBOL */

        .btn {
            border-radius: 7px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            padding: 8px 15px;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
        }

        .btn-secondary {
            --bs-btn-bg: #e8f0f8;
            --bs-btn-border-color: #d8e5f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
        }

        .btn-danger {
            --bs-btn-color: #fff;
            --bs-btn-bg: #cf8888;
            --bs-btn-border-color: #cf8888;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #bd7777;
            --bs-btn-hover-border-color: #bd7777;
        }

        /* TOMBOL AKSI TABEL */

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            line-height: 1.4;
            border-radius: 6px;
        }

        /* TABEL */

        .table {
            color: #344b63;
            vertical-align: middle;
            margin-bottom: 0;
            font-size: 16px;
        }

        .table thead th {
            background: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
        }

        .table tbody td {
            border-color: #e5edf5;
            padding: 11px 12px;
        }

        /* STATUS */

        .status-aktif,
        .status-nonaktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-aktif {
            background: #e4f3e9;
            color: #34734b;
        }

        .status-nonaktif {
            background: #fce9e9;
            color: #a34f4f;
        }

        /* KETERANGAN */

        .keterangan {
            color: #71849a;
            font-size: 16px;
        }

        /* DATATABLES */

        .dt-container .dt-layout-row {
            margin: 12px 0;
        }

        .dt-container .dt-layout-row:first-child {
            margin-top: 22px;
            margin-bottom: 8px;
        }

        .dt-container .dt-layout-cell {
            display: flex;
            align-items: center;
        }

        .dt-container .dt-length,
        .dt-container .dt-search {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dt-container .dt-length label,
        .dt-container .dt-search label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 13px;
            white-space: nowrap;
        }

        .dt-container .dt-length select {
            width: 55px !important;
            min-width: 55px;
            height: 38px;
            padding: 6px 5px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: #344b63;
            font-size: 14px;
        }

        .dt-container .dt-search input {
            width: 175px;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            margin-left: 0 !important;
            font-size: 14px;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        .table-responsive {
            width: 100%;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .main {
                margin-left: 0;
                padding: 18px 14px 30px;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 17px;
            }

            .judul {
                font-size: 23px;
            }

            .dt-container .dt-search input {
                width: 140px;
            }

            .dt-container .dt-layout-row {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>
</aside>

<main class="main">

    <div class="container py-4">

        <!-- TOMBOL BAGIAN ATAS -->

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <?php if (!$tampilkanForm): ?>
                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnToggleSiswa"
                    aria-expanded="false"
                    aria-controls="formTambahSiswa"
                >
                    + Tambah Data Siswa
                </button>
            <?php endif; ?>

        </div>

        <!-- JUDUL HALAMAN -->

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Kelola Siswa</h2>
                <p class="text-muted mb-0">
                    Tambah dan kelola data siswa.
                </p>
            </div>
        </div>

        <!-- NOTIFIKASI -->

        <?php if ($pesan !== ''): ?>

            <div class="alert alert-<?= e($jenisPesan); ?> alert-dismissible fade show" role="alert">

                <?= e($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>

            </div>

        <?php endif; ?>

        <!-- FORM TAMBAH SISWA -->

        <div
            class="card mb-4 <?= $tampilkanForm ? '' : 'd-none'; ?>"
            id="formTambahSiswa"
        >

            <div class="card-body">

                <h4 class="mb-4">Tambah Data Siswa</h4>

                <form method="POST" action="siswa.php" autocomplete="off">

                    <div class="mb-3">

                        <label for="nis" class="form-label">
                            NIS
                        </label>

                        <input
                            type="text"
                            id="nis"
                            name="nis"
                            class="form-control"
                            placeholder="Masukkan NIS siswa"
                            maxlength="30"
                            value="<?= e($old['nis']); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="nisn" class="form-label">
                            NISN
                        </label>

                        <input
                            type="text"
                            id="nisn"
                            name="nisn"
                            class="form-control"
                            placeholder="Masukkan NISN siswa"
                            maxlength="30"
                            value="<?= e($old['nisn']); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="nama" class="form-label">
                            Nama Siswa
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama siswa"
                            maxlength="150"
                            value="<?= e($old['nama']); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="jenis_kelamin" class="form-label">
                            Jenis Kelamin
                        </label>

                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option
                                value="L"
                                <?= $old['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>
                            >
                                Laki-laki
                            </option>

                            <option
                                value="P"
                                <?= $old['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tanggal_lahir" class="form-label">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            id="tanggal_lahir"
                            name="tanggal_lahir"
                            class="form-control"
                            value="<?= e($old['tanggal_lahir']); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="alamat" class="form-label">
                            Alamat
                        </label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat siswa"
                            required
                        ><?= e($old['alamat']); ?></textarea>

                    </div>

                    <div class="mb-4">

                        <label for="status_aktif" class="form-label">
                            Status
                        </label>

                        <select
                            id="status_aktif"
                            name="status_aktif"
                            class="form-select"
                            required
                        >

                            <option
                                value="1"
                                <?= $old['status_aktif'] === '1' ? 'selected' : ''; ?>
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                <?= $old['status_aktif'] === '0' ? 'selected' : ''; ?>
                            >
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                    <!-- TOMBOL FORM -->

                    <div class="d-flex flex-wrap gap-2">

                        <button
                            type="submit"
                            name="simpan"
                            value="1"
                            class="btn btn-primary"
                        >
                            Tambah Siswa
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="btnBatalSiswa"
                        >
                            Batal
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- TABEL DATA SISWA -->

        <div class="card">

            <div class="card-body">

                <h4 class="mb-1">Data Siswa</h4>

                <p class="keterangan mb-3">
                    Daftar seluruh siswa yang terdaftar.
                </p>

                <div class="table-responsive">

                    <table
                        id="tabelSiswa"
                        class="table table-bordered table-hover align-middle w-100"
                    >

                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th style="width:150px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($query && mysqli_num_rows($query) > 0): ?>

                                <?php while ($siswa = mysqli_fetch_assoc($query)): ?>

                                    <tr>

                                        <td></td>

                                        <td>
                                            <?= e($siswa['nis']); ?>
                                        </td>

                                        <td>
                                            <?= e($siswa['nisn']); ?>
                                        </td>

                                        <td>
                                            <?= e($siswa['nama']); ?>
                                        </td>

                                        <td>
                                            <?php
                                            if ($siswa['jenis_kelamin'] === 'L') {
                                                echo 'Laki-laki';
                                            } elseif ($siswa['jenis_kelamin'] === 'P') {
                                                echo 'Perempuan';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?= e($siswa['tanggal_lahir']); ?>
                                        </td>

                                        <td>
                                            <?= e($siswa['alamat']); ?>
                                        </td>

                                        <td>

                                            <?php if ((int)$siswa['status_aktif'] === 1): ?>

                                                <span class="status-aktif">
                                                    Aktif
                                                </span>

                                            <?php else: ?>

                                                <span class="status-nonaktif">
                                                    Tidak Aktif
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <div class="d-flex flex-wrap gap-2">

                                                <a
                                                    href="edit_siswa.php?id=<?= (int)$siswa['id']; ?>"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_siswa.php?id=<?= (int)$siswa['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data siswa ini?')"
                                                >
                                                    Hapus
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php elseif ($query): ?>

                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Belum ada data siswa.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <tr>
                                    <td colspan="9" class="text-center text-danger py-4">
                                        Data siswa gagal dimuat.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>

<!-- JAVASCRIPT -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       BUKA DAN BATAL FORM
    ========================= */

    const tombolTambah = document.getElementById('btnToggleSiswa');
    const tombolBatal = document.getElementById('btnBatalSiswa');
    const form = document.getElementById('formTambahSiswa');

    if (tombolTambah && form) {

        tombolTambah.addEventListener('click', function () {

            form.classList.remove('d-none');
            tombolTambah.classList.add('d-none');

            tombolTambah.setAttribute('aria-expanded', 'true');

            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }

    if (tombolBatal && form) {

        tombolBatal.addEventListener('click', function () {

            form.classList.add('d-none');

            if (tombolTambah) {
                tombolTambah.classList.remove('d-none');
                tombolTambah.setAttribute('aria-expanded', 'false');
            }

        });

    }

    /* =========================
       DATATABLES
    ========================= */

    if (window.jQuery && $.fn.DataTable) {

        $('#tabelSiswa').DataTable({

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            ordering: true,

            searching: true,

            paging: true,

            autoWidth: false,

            order: [[1, 'desc']],

            columnDefs: [
                {
                    targets: [0, 8],
                    orderable: false,
                    searchable: false
                }
            ],

            language: {

                search: 'Cari:',

                searchPlaceholder: 'Cari nama atau NIS...',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa',

                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 siswa',

                infoFiltered: '(difilter dari _MAX_ total siswa)',

                zeroRecords: 'Data siswa tidak ditemukan',

                emptyTable: 'Belum ada data siswa',

                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }

            },

            drawCallback: function () {

                const api = this.api();
                const info = api.page.info();

                api.column(0, {
                    page: 'current',
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function (cell, i) {

                    cell.textContent = info.start + i + 1;

                });

            }

        });

    }

});
</script>

</body>
</html>
