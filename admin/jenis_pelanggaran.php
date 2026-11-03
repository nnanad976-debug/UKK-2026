
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$pesan = '';
$jenis_pesan = 'success';
$bukaForm = false;

$old = [
    'pelanggaran_kategori_id' => '',
    'kode' => '',
    'nama' => '',
    'poin' => '',
    'deskripsi' => '',
    'status_aktif' => '1'
];

/* NOTIFIKASI */
if (isset($_GET['berhasil'])) {
    $pesan = 'Data jenis pelanggaran berhasil ditambahkan.';
} elseif (isset($_GET['diubah'])) {
    $pesan = 'Data jenis pelanggaran berhasil diperbarui.';
} elseif (isset($_GET['dihapus'])) {
    $pesan = 'Data jenis pelanggaran berhasil dihapus.';
} elseif (isset($_GET['gagal_hapus'])) {
    $pesan = 'Data gagal dihapus karena masih digunakan.';
    $jenis_pesan = 'danger';
}

/* PROSES TAMBAH DATA */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $bukaForm = true;

    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = strtoupper(trim($_POST['kode'] ?? ''));
    $nama = trim($_POST['nama'] ?? '');
    $poin = filter_var($_POST['poin'] ?? '', FILTER_VALIDATE_INT);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = filter_var($_POST['status_aktif'] ?? 1, FILTER_VALIDATE_INT);

    $old = [
        'pelanggaran_kategori_id' => $kategori_id,
        'kode' => $kode,
        'nama' => $nama,
        'poin' => $poin === false ? '' : $poin,
        'deskripsi' => $deskripsi,
        'status_aktif' => $status === false ? '1' : $status
    ];

    if ($kategori_id <= 0 || $kode === '' || $nama === '') {
        $pesan = 'Kategori, kode, dan nama pelanggaran wajib diisi.';
        $jenis_pesan = 'danger';

    } elseif ($poin === false || $poin < 0) {
        $pesan = 'Poin harus berupa angka dan tidak boleh kurang dari 0.';
        $jenis_pesan = 'danger';

    } elseif ($status === false || !in_array($status, [0, 1], true)) {
        $pesan = 'Status tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        /* CEK KATEGORI */
        $cekKategori = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_pelanggaran_kategori
             WHERE id = ? AND status_aktif = 1"
        );

        if (!$cekKategori) {
            $pesan = 'Gagal memeriksa kategori pelanggaran.';
            $jenis_pesan = 'danger';

        } else {
            mysqli_stmt_bind_param($cekKategori, 'i', $kategori_id);
            mysqli_stmt_execute($cekKategori);

            $hasilKategori = mysqli_stmt_get_result($cekKategori);

            if (!$hasilKategori || mysqli_num_rows($hasilKategori) === 0) {
                $pesan = 'Kategori tidak ditemukan atau tidak aktif.';
                $jenis_pesan = 'danger';
            }

            mysqli_stmt_close($cekKategori);
        }

        /* CEK KODE DUPLIKAT */
        if ($pesan === '') {

            $cekKode = mysqli_prepare(
                $koneksi,
                "SELECT id
                 FROM t_pelanggaran
                 WHERE kode = ?
                 LIMIT 1"
            );

            if (!$cekKode) {
                $pesan = 'Gagal memeriksa kode pelanggaran.';
                $jenis_pesan = 'danger';

            } else {
                mysqli_stmt_bind_param($cekKode, 's', $kode);
                mysqli_stmt_execute($cekKode);

                $hasilKode = mysqli_stmt_get_result($cekKode);

                if ($hasilKode && mysqli_num_rows($hasilKode) > 0) {
                    $pesan = 'Kode pelanggaran sudah digunakan.';
                    $jenis_pesan = 'danger';
                }

                mysqli_stmt_close($cekKode);
            }
        }

        /* SIMPAN DATA */
        if ($pesan === '') {

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_pelanggaran
                (
                    pelanggaran_kategori_id,
                    kode,
                    nama,
                    poin,
                    deskripsi,
                    status_aktif
                )
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                $pesan = 'Data gagal disimpan. Silakan coba kembali.';
                $jenis_pesan = 'danger';

            } else {

                mysqli_stmt_bind_param(
                    $stmt,
                    'issisi',
                    $kategori_id,
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi,
                    $status
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    header("Location: jenis_pelanggaran.php?berhasil=1");
                    exit;

                } else {
                    $pesan = 'Data gagal disimpan. Silakan coba kembali.';
                    $jenis_pesan = 'danger';
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

/* AMBIL KATEGORI AKTIF */
$query_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/* AMBIL DATA JENIS PELANGGARAN */
$query_data = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        k.nama AS nama_kategori
    FROM t_pelanggaran p
    LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
    WHERE p.id = (
        SELECT MIN(p2.id)
        FROM t_pelanggaran p2
        WHERE p2.nama = p.nama
          AND p2.poin = p.poin
          AND p2.pelanggaran_kategori_id = p.pelanggaran_kategori_id
    )
    ORDER BY p.id DESC"
);

if (!$query_data) {
    die('Gagal mengambil data jenis pelanggaran.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Jenis Pelanggaran - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --white: #ffffff;
            --text: #2f435a;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        a {
            text-decoration: none;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 282px;
            min-width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-right: 1px solid var(--line);
            z-index: 1000;
            overflow-y: auto;
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            color: var(--blue-dark);
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 12px;
        }

        .menu-title {
            margin: 0 14px 11px;
            color: #91a5bd;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            min-height: 42px;
            padding: 11px 14px;
            border-radius: 9px;
            color: #5e7695;
            font-size: 13px;
            transition: .2s;
        }

        .menu-link:hover {
            background: #f2f7fd;
            color: var(--blue);
        }

        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--blue);
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
        }

        /* ================= KONTEN ================= */

        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
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

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
        }

        .card-body {
            padding: 22px;
        }

        .keterangan {
            color: #71849a;
            font-size: 14px;
        }

        /* ================= FORM ================= */

        .form-label {
            color: #405873;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        textarea.form-control {
            min-height: 100px;
        }

        /* ================= TOMBOL ================= */

        .btn {
            border-radius: 7px;
            font-size: 13px;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
            padding: 8px 15px;
        }

        .btn-secondary {
            --bs-btn-bg: #e8f0f8;
            --bs-btn-border-color: #d8e5f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
            padding: 8px 15px;
        }

        .btn-danger {
            --bs-btn-color: #fff;
            --bs-btn-bg: #cf8888;
            --bs-btn-border-color: #cf8888;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #bd7777;
            --bs-btn-hover-border-color: #bd7777;
        }

        .btn-sm {
            padding: 5px 11px;
        }

        /* ================= STATUS ================= */

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

        /* ================= TABEL ================= */

        .table {
            color: #344b63;
            vertical-align: middle;
            margin-bottom: 0;
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

        .table-hover tbody tr:hover > * {
            --bs-table-accent-bg: #f4f8fc;
        }

        .aksi {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .aksi form {
            margin: 0;
        }

        #tabelJenis td:last-child {
            white-space: nowrap;
        }

        /* ================= DATATABLES ================= */

        .dt-container .dt-layout-row {
            margin: 12px 0;
        }

        .dt-container .dt-layout-row:first-child {
            margin-top: 22px;
            margin-bottom: 8px;
        }

        /* Baris bawah: info kiri dan pagination kanan */
        .dt-container .dt-layout-row:last-child {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
        }

        .dt-container .dt-layout-cell {
            display: flex;
            align-items: center;
            min-width: 0;
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
            width: 60px !important;
            min-width: 60px;
            height: 38px;
            padding: 6px 5px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: #344b63;
        }

        .dt-container .dt-search input {
            width: 190px;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            margin-left: 0 !important;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        .dt-container .dt-layout-table {
            margin-top: 8px;
        }

        .dt-container .dt-info {
            font-size: 13px;
            color: #5e7695;
            padding-top: 0 !important;
        }

        .dt-container .dt-paging {
            display: flex;
            justify-content: flex-end;
        }

        .dt-container .dt-paging .pagination {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0;
            margin: 0;
        }

        .dt-container .dt-paging .page-item {
            margin: 0;
        }

        .dt-container .dt-paging .page-link {
            min-width: 36px;
            height: 39px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6px 12px;
            margin-left: -1px;
            border: 1px solid #d8e1e9;
            border-radius: 0;
            background: #fff;
            color: #0d6efd;
            font-size: 14px;
            box-shadow: none;
        }

        .dt-container .dt-paging .page-item:first-child .page-link {
            border-radius: 6px 0 0 6px;
        }

        .dt-container .dt-paging .page-item:last-child .page-link {
            border-radius: 0 6px 6px 0;
        }

        .dt-container .dt-paging .page-item.active .page-link {
            position: relative;
            z-index: 2;
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .dt-container .dt-paging .page-item.disabled .page-link {
            background: #f1f3f5;
            border-color: #d8e1e9;
            color: #64748b;
            pointer-events: none;
        }

        .dt-container .dt-paging .page-link:hover {
            background: #eaf3ff;
            border-color: #d8e1e9;
            color: #0d6efd;
        }

        .dt-container .dt-paging .page-item.active .page-link:hover {
            background: #0d6efd;
            color: #fff;
        }

        .table-responsive {
            width: 100%;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 100%;
                height: auto;
                min-height: auto;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .logout {
                margin-top: 14px;
            }

            .dt-container .dt-layout-row:last-child {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 17px;
            }

            .dt-container .dt-layout-row {
                flex-wrap: wrap;
            }

            .dt-container .dt-search input {
                width: 140px;
            }

            .dt-container .dt-layout-row:last-child {
                justify-content: center;
            }

            .dt-container .dt-info {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <?php include "layout/sidebar.php"; ?>
</aside>

<!-- KONTEN UTAMA -->
<main class="main">
    <div class="container py-2">

        <!-- TOMBOL ATAS -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <button
                type="button"
                class="btn btn-primary"
                id="tombolTambah"
                data-bs-toggle="collapse"
                data-bs-target="#formTambahJenis"
                aria-expanded="<?= $bukaForm ? 'true' : 'false'; ?>"
                aria-controls="formTambahJenis">

                + Tambah Data Jenis Pelanggaran
            </button>

        </div>

        <!-- JUDUL HALAMAN -->
        <div class="card mb-4">
            <div class="card-body">

                <h2 class="judul mb-1">Jenis Pelanggaran</h2>

                <p class="text-muted mb-0">
                    Tambah dan kelola data jenis pelanggaran siswa.
                </p>

            </div>
        </div>

        <!-- FORM TAMBAH -->
        <div
            class="collapse <?= $bukaForm ? 'show' : ''; ?>"
            id="formTambahJenis">

            <div class="card mb-4">
                <div class="card-body">

                    <h4 class="mb-4">Tambah Jenis Pelanggaran</h4>

                    <?php if ($pesan !== '' && $jenis_pesan === 'danger'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">

                            <?= e($pesan); ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Tutup">
                            </button>

                        </div>
                    <?php endif; ?>

                    <form
                        method="POST"
                        action="jenis_pelanggaran.php"
                        id="formJenis"
                        autocomplete="off">

                        <div class="mb-3">
                            <label for="pelanggaran_kategori_id" class="form-label">
                                Kategori Pelanggaran
                            </label>

                            <select
                                name="pelanggaran_kategori_id"
                                id="pelanggaran_kategori_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Kategori --</option>

                                <?php if ($query_kategori): ?>
                                    <?php while ($kategori = mysqli_fetch_assoc($query_kategori)): ?>

                                        <option
                                            value="<?= e($kategori['id']); ?>"
                                            <?= (string)$old['pelanggaran_kategori_id'] === (string)$kategori['id'] ? 'selected' : ''; ?>>

                                            <?= e($kategori['nama']); ?>

                                        </option>

                                    <?php endwhile; ?>
                                <?php endif; ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kode" class="form-label">
                                Kode Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="kode"
                                id="kode"
                                class="form-control"
                                placeholder="Contoh: PLG001"
                                value="<?= e($old['kode']); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                placeholder="Masukkan nama pelanggaran"
                                value="<?= e($old['nama']); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="poin" class="form-label">
                                Poin Pelanggaran
                            </label>

                            <input
                                type="number"
                                name="poin"
                                id="poin"
                                class="form-control"
                                min="0"
                                placeholder="Masukkan jumlah poin"
                                value="<?= e($old['poin']); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                name="deskripsi"
                                id="deskripsi"
                                class="form-control"
                                placeholder="Masukkan deskripsi pelanggaran"><?= e($old['deskripsi']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select
                                name="status_aktif"
                                id="status_aktif"
                                class="form-select"
                                required>

                                <option
                                    value="1"
                                    <?= (string)$old['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option
                                    value="0"
                                    <?= (string)$old['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <button
                            type="submit"
                            name="simpan"
                            value="1"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#formTambahJenis">
                            Tutup Form
                        </button>

                    </form>

                </div>
            </div>
        </div>

        <!-- NOTIFIKASI BERHASIL -->
        <?php if ($pesan !== '' && $jenis_pesan === 'success'): ?>

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <?= e($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup">
                </button>

            </div>

        <?php endif; ?>

        <!-- TABEL DATA -->
        <div class="card">
            <div class="card-body">

                <h4 class="mb-1">Data Jenis Pelanggaran</h4>

                <p class="keterangan mb-3">
                    Daftar jenis pelanggaran siswa berdasarkan kategori dan poin.
                </p>

                <div class="table-responsive">

                    <table
                        id="tabelJenis"
                        class="table table-bordered table-hover align-middle w-100">

                        <thead>
                            <tr>
                                <th style="width:60px">No</th>
                                <th>Kode</th>
                                <th>Kategori</th>
                                <th>Nama Pelanggaran</th>
                                <th>Poin</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($query_data) > 0): ?>

                                <?php while ($data = mysqli_fetch_assoc($query_data)): ?>

                                    <tr>
                                        <td></td>

                                        <td>
                                            <?= e($data['kode']); ?>
                                        </td>

                                        <td>
                                            <?= e($data['nama_kategori'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= e($data['nama']); ?>
                                        </td>

                                        <td>
                                            <?= e($data['poin']); ?>
                                        </td>

                                        <td>
                                            <?= e($data['deskripsi'] ?: '-'); ?>
                                        </td>

                                        <td>
                                            <?php if ((int)$data['status_aktif'] === 1): ?>

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
                                            <div class="aksi">

                                                <a
                                                    href="edit_jenis_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <form
                                                    action="hapus_jenis_pelanggaran.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int)$data['id']; ?>">

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-danger">
                                                        Hapus
                                                    </button>

                                                </form>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    /* DATATABLES */
    $('#tabelJenis').DataTable({

        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,
        searching: true,
        paging: true,
        autoWidth: false,

        // Pagination lengkap: first, previous, numbers, next, last.
        pagingType: 'full_numbers',

        order: [[1, 'desc']],

        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari jenis pelanggaran...',
            lengthMenu: 'Tampilkan _MENU_ data',

            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
            infoFiltered: '(difilter dari _MAX_ total data)',

            zeroRecords: 'Data jenis pelanggaran tidak ditemukan',
            emptyTable: 'Belum ada data jenis pelanggaran',

            paginate: {
                first: '«',
                previous: 'Sebelumnya',
                next: 'Berikutnya',
                last: '»'
            }
        },

        columnDefs: [
            {
                targets: [0, 7],
                orderable: false,
                searchable: false
            }
        ],

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

    /* BUKA DAN TUTUP FORM */
    const formTambah = document.getElementById('formTambahJenis');
    const tombolTambah = document.getElementById('tombolTambah');

    formTambah.addEventListener('shown.bs.collapse', function () {

        tombolTambah.textContent = '− Tutup Form Tambah Jenis Pelanggaran';

        formTambah.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    });

    formTambah.addEventListener('hidden.bs.collapse', function () {

        tombolTambah.textContent = '+ Tambah Data Jenis Pelanggaran';

    });

    if (formTambah.classList.contains('show')) {

        tombolTambah.textContent = '− Tutup Form Tambah Jenis Pelanggaran';

    }

});
</script>

</body>
</html>
