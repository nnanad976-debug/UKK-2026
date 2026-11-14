
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

mysqli_set_charset($koneksi, 'utf8mb4');

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/* ================= FUNGSI TINGKAT OTOMATIS ================= */

function tingkatPelanggaran(int $poin): string
{
    if ($poin >= 35) {
        return 'Berat';
    }

    if ($poin >= 11) {
        return 'Sedang';
    }

    return 'Ringan';
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

/* ================= NOTIFIKASI ================= */

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

/* ================= PROSES TAMBAH DATA ================= */

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

    } elseif ($poin === false || $poin < 1) {
        $pesan = 'Poin harus berupa angka dan minimal 1.';
        $jenis_pesan = 'danger';

    } elseif ($status === false || !in_array($status, [0, 1], true)) {
        $pesan = 'Status tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        /* CEK KATEGORI AKTIF */

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

            $tingkat = tingkatPelanggaran((int)$poin);

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_pelanggaran
                (
                    pelanggaran_kategori_id,
                    kode,
                    nama,
                    poin,
                    tingkat,
                    deskripsi,
                    status_aktif
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                $pesan = 'Data gagal disimpan. Pastikan kolom tingkat tersedia di tabel t_pelanggaran.';
                $jenis_pesan = 'danger';

            } else {

                mysqli_stmt_bind_param(
                    $stmt,
                    'ississi',
                    $kategori_id,
                    $kode,
                    $nama,
                    $poin,
                    $tingkat,
                    $deskripsi,
                    $status
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    header("Location: jenis_pelanggaran.php?berhasil=1");
                    exit;

                } else {
                    $pesan = 'Data gagal disimpan. Periksa kembali struktur tabel database.';
                    $jenis_pesan = 'danger';

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

/* ================= AMBIL KATEGORI AKTIF ================= */

$query_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$query_kategori) {
    die('Gagal mengambil data kategori pelanggaran.');
}

/* ================= AMBIL JENIS PELANGGARAN AKTIF ================= */
/*
   Hanya tampilkan jenis pelanggaran yang:
   1. Status jenis pelanggarannya aktif.
   2. Kategorinya masih aktif.
   3. Tidak menampilkan data dari kategori yang dinonaktifkan.
*/

$query_data = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.tingkat,
        p.deskripsi,
        p.status_aktif,
        k.nama AS nama_kategori
    FROM t_pelanggaran p
    INNER JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
    WHERE p.status_aktif = 1
      AND k.status_aktif = 1
      AND p.id = (
          SELECT MIN(p2.id)
          FROM t_pelanggaran p2
          WHERE p2.nama = p.nama
            AND p2.poin = p.poin
            AND p2.pelanggaran_kategori_id = p.pelanggaran_kategori_id
            AND p2.status_aktif = 1
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

    <title>Kelola Jenis Pelanggaran</title>

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

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f3f7fc;
            color: #2f435a;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
        }

        a { text-decoration: none; }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
            min-width: 282px;
            max-width: 282px;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar .brand {
            padding: 0 15px 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 22px;
        }

        .sidebar .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .sidebar .brand-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .sidebar .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }

        .sidebar .menu {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sidebar .menu-link {
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.5;
            transition: background-color .15s ease, color .15s ease;
        }

        .sidebar .menu-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .sidebar .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .sidebar .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .sidebar .logout .menu-link { color: #d9534f; }

        .sidebar .logout .menu-link:hover {
            background: #fff0ef;
            color: #c03935;
        }

        .main {
            margin-left: 282px;
            width: calc(100% - 282px);
            min-height: 100vh;
            padding: 24px 28px 40px;
        }

        .container {
            width: 100%;
            max-width: none !important;
            margin: 0 auto;
            padding: 0 !important;
        }

        .judul, h4 { color: #234e78; }

        .judul {
            font-size: 28px;
            font-weight: 600;
        }

        h4 {
            font-size: 20px;
            font-weight: 600;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
        }

        .card-body { padding: 24px; }

        .keterangan {
            color: #71849a;
            font-size: 14px;
        }

        .form-label {
            color: #405873;
            font-weight: 600;
            font-size: 16px;
        }

        .form-control, .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
            color: #344b63;
        }

        .form-control::placeholder {
            color: #9aaabd;
            font-size: 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

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

        .btn-sm {
            padding: 5px 11px;
            font-size: 13px;
        }

        .alert {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            border-radius: 8px;
        }

        .status-aktif, .status-nonaktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
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

        .tingkat-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 78px;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .2px;
            border: 1px solid transparent;
            white-space: nowrap;
            line-height: 1.2;
        }

        .tingkat-ringan {
            color: #26734d;
            background: #edf8f1;
            border-color: #ccebd8;
        }

        .tingkat-sedang {
            color: #946600;
            background: #fff8e5;
            border-color: #f2e1a8;
        }

        .tingkat-berat {
            color: #b33c45;
            background: #fff0f0;
            border-color: #f2cccc;
        }

        #hasilTingkat {
            display: inline-block;
            min-width: 78px;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            border: 1px solid transparent;
        }

        .hasil-ringan {
            color: #26734d;
            background: #edf8f1;
            border-color: #ccebd8 !important;
        }

        .hasil-sedang {
            color: #946600;
            background: #fff8e5;
            border-color: #f2e1a8 !important;
        }

        .hasil-berat {
            color: #b33c45;
            background: #fff0f0;
            border-color: #f2cccc !important;
        }

        .table {
            color: #344b63;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table thead th {
            background: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            font-size: 14px;
            padding: 13px 12px;
            white-space: nowrap;
        }

        .table tbody td {
            border-color: #e5edf5;
            padding: 12px;
            font-size: 14px;
        }

        .table-hover tbody tr:hover > * {
            --bs-table-accent-bg: #f4f8fc;
        }

        .aksi {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .aksi form { margin: 0; }

        #tabelJenis td:last-child { white-space: nowrap; }

        .dt-container {
            width: 100%;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #344b63;
        }

        .dt-container .dt-layout-row { margin: 12px 0; }

        .dt-container .dt-layout-row:first-child {
            margin-top: 22px;
            margin-bottom: 8px;
        }

        .dt-container .dt-layout-row:last-child {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            min-height: 40px;
            margin-top: 15px;
        }

        .dt-container .dt-layout-cell {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .dt-container .dt-length, .dt-container .dt-search {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dt-container .dt-length label, .dt-container .dt-search label {
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
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
        }

        .dt-container .dt-search input {
            width: 190px;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            margin-left: 0 !important;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        .dt-container .dt-layout-table { margin-top: 8px; }

        .dt-container .dt-info {
            font-size: 13px;
            color: #5e7695;
            padding-top: 0 !important;
        }

        .dt-container .dt-paging {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-height: 40px;
            flex-shrink: 0;
        }

        .dt-container .dt-paging .pagination {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .dt-container .dt-paging .page-item { margin: 0; }

        .dt-container .dt-paging .page-link {
            min-width: 36px;
            height: 39px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6px 11px;
            margin-left: -1px;
            border: 1px solid #d8e1e9;
            border-radius: 0;
            background: #fff;
            color: #0d6efd;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            box-shadow: none;
            white-space: nowrap;
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

        .table-responsive { width: 100%; }

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                height: auto;
                min-height: auto;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .sidebar .logout { margin-top: 14px; }

            .dt-container .dt-layout-row:last-child { flex-wrap: wrap; }
        }

        @media (max-width: 576px) {
            .card-body { padding: 17px; }
            .judul { font-size: 24px; }
            .dt-container .dt-layout-row { flex-wrap: wrap; }
            .dt-container .dt-search input { width: 140px; }

            .dt-container .dt-layout-row:last-child { justify-content: center; }

            .dt-container .dt-info {
                width: 100%;
                text-align: center;
            }

            .dt-container .dt-paging {
                justify-content: center;
                width: 100%;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <?php include "layout/sidebar.php"; ?>
    </aside>

    <main class="main">
        <div class="container py-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                <a href="../dashboard.php" class="btn btn-secondary">
                    &larr; Kembali ke Dashboard
                </a>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="tombolTambah"
                    style="<?= $bukaForm ? 'display:none;' : ''; ?>">
                    + Tambah Jenis Pelanggaran
                </button>

            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="judul mb-1">Kelola Jenis Pelanggaran</h2>
                    <p class="text-muted mb-0">
                        Tambah dan kelola jenis pelanggaran siswa beserta poinnya.
                    </p>
                </div>
            </div>

            <?php if ($pesan !== ''): ?>
                <div
                    class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show"
                    role="alert">

                    <?= e($pesan); ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup">
                    </button>
                </div>
            <?php endif; ?>

            <div
                id="formTambahJenis"
                style="<?= $bukaForm ? '' : 'display:none;'; ?>">

                <div class="card mb-4">
                    <div class="card-body">

                        <h4 class="mb-4">Tambah Jenis Pelanggaran</h4>

                        <form method="POST" action="jenis_pelanggaran.php" id="formJenis">

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

                                    <?php while ($kategori = mysqli_fetch_assoc($query_kategori)): ?>
                                        <option
                                            value="<?= (int)$kategori['id']; ?>"
                                            <?= (string)$old['pelanggaran_kategori_id'] === (string)$kategori['id'] ? 'selected' : ''; ?>>
                                            <?= e($kategori['nama']); ?>
                                        </option>
                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Pelanggaran</label>

                                <input
                                    type="text"
                                    name="kode"
                                    id="kode"
                                    class="form-control"
                                    placeholder="Contoh: PLG-052"
                                    maxlength="30"
                                    value="<?= e($old['kode']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Pelanggaran</label>

                                <input
                                    type="text"
                                    name="nama"
                                    id="nama"
                                    class="form-control"
                                    placeholder="Masukkan nama pelanggaran"
                                    maxlength="150"
                                    value="<?= e($old['nama']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="poin" class="form-label">Poin Pelanggaran</label>

                                <input
                                    type="number"
                                    name="poin"
                                    id="poin"
                                    class="form-control"
                                    placeholder="Masukkan jumlah poin"
                                    min="1"
                                    step="1"
                                    value="<?= e($old['poin']); ?>"
                                    required>

                                <small class="text-muted d-block mt-2">
                                    Tingkat ditentukan otomatis berdasarkan jumlah poin.
                                </small>

                                <div class="mt-3">
                                    <span class="text-muted me-2">Tingkat otomatis:</span>
                                    <strong id="hasilTingkat">Belum ditentukan</strong>
                                </div>

                                <small class="text-muted d-block mt-2">
                                    1–10 poin: Ringan | 11–34 poin: Sedang | 35 poin ke atas: Berat
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>

                                <textarea
                                    name="deskripsi"
                                    id="deskripsi"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Masukkan deskripsi pelanggaran"><?= e($old['deskripsi']); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="status_aktif" class="form-label">Status</label>

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

                            <button type="submit" name="simpan" class="btn btn-primary">
                                Simpan Jenis Pelanggaran
                            </button>

                            <button type="button" class="btn btn-secondary" id="tombolBatal">
                                Batal
                            </button>

                        </form>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <h4 class="mb-1">Data Jenis Pelanggaran</h4>

                    <p class="keterangan mb-3">
                        Menampilkan jenis pelanggaran yang aktif dari kategori aktif.
                    </p>

                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-hover align-middle"
                            id="tabelJenis"
                            style="width:100%;">

                            <thead>
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Kode</th>
                                    <th>Kategori</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Tingkat</th>
                                    <th>Status</th>
                                    <th style="width:140px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php $no = 1; ?>

                                <?php while ($data = mysqli_fetch_assoc($query_data)): ?>

                                    <?php
                                    $tingkat = $data['tingkat'] ?? '';

                                    if ($tingkat === 'Berat') {
                                        $classTingkat = 'tingkat-berat';
                                    } elseif ($tingkat === 'Sedang') {
                                        $classTingkat = 'tingkat-sedang';
                                    } else {
                                        $classTingkat = 'tingkat-ringan';
                                    }
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= e($data['kode']); ?></td>

                                        <td><?= e($data['nama_kategori'] ?? '-'); ?></td>

                                        <td>
                                            <div class="fw-semibold">
                                                <?= e($data['nama']); ?>
                                            </div>

                                            <?php if (!empty($data['deskripsi'])): ?>
                                                <small class="text-muted">
                                                    <?= e($data['deskripsi']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= (int)$data['poin']; ?></td>

                                        <td>
                                            <span class="tingkat-label <?= $classTingkat; ?>">
                                                <?= e($tingkat); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="status-aktif">Aktif</span>
                                        </td>

                                        <td>
                                            <div class="aksi">

                                                <a
                                                    href="edit_jenis_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_jenis_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                                                    Hapus
                                                </a>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

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
        document.addEventListener('DOMContentLoaded', function () {

            const formTambah = document.getElementById('formTambahJenis');
            const tombolTambah = document.getElementById('tombolTambah');
            const tombolBatal = document.getElementById('tombolBatal');
            const formJenis = document.getElementById('formJenis');

            tombolTambah.addEventListener('click', function () {
                formTambah.style.display = 'block';
                tombolTambah.style.display = 'none';
            });

            tombolBatal.addEventListener('click', function () {
                formJenis.reset();

                formTambah.style.display = 'none';
                tombolTambah.style.display = 'inline-block';

                perbaruiTingkat();
            });

            const inputKode = document.getElementById('kode');

            inputKode.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });

            const inputPoin = document.getElementById('poin');
            const hasilTingkat = document.getElementById('hasilTingkat');

            function perbaruiTingkat() {

                const poin = Number(inputPoin.value);

                hasilTingkat.className = '';

                if (
                    inputPoin.value.trim() === '' ||
                    !Number.isInteger(poin) ||
                    poin < 1
                ) {
                    hasilTingkat.textContent = 'Belum ditentukan';
                    hasilTingkat.style.color = '#7890ad';
                    hasilTingkat.style.background = '#f1f5f9';
                    hasilTingkat.style.borderColor = '#dce5ee';
                    return;
                }

                hasilTingkat.style.background = '';
                hasilTingkat.style.borderColor = '';

                if (poin >= 35) {
                    hasilTingkat.textContent = 'Berat';
                    hasilTingkat.classList.add('hasil-berat');

                } else if (poin >= 11) {
                    hasilTingkat.textContent = 'Sedang';
                    hasilTingkat.classList.add('hasil-sedang');

                } else {
                    hasilTingkat.textContent = 'Ringan';
                    hasilTingkat.classList.add('hasil-ringan');
                }
            }

            inputPoin.addEventListener('input', perbaruiTingkat);
            perbaruiTingkat();

            if (typeof DataTable !== 'undefined') {

                new DataTable('#tabelJenis', {
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    pagingType: 'full_numbers',
                    paging: true,
                    ordering: true,
                    searching: true,
                    autoWidth: false,
                    renderer: 'bootstrap',

                    language: {
                        emptyTable: 'Belum ada data jenis pelanggaran aktif.',
                        zeroRecords: 'Data yang dicari tidak ditemukan.',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Menampilkan 0 data',
                        infoFiltered: '(disaring dari _MAX_ total data)',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        search: 'Cari:',
                        searchPlaceholder: 'Cari jenis pelanggaran...',

                        paginate: {
                            first: '«',
                            last: '»',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        },

                        aria: {
                            paginate: {
                                first: 'Halaman pertama',
                                last: 'Halaman terakhir',
                                next: 'Halaman berikutnya',
                                previous: 'Halaman sebelumnya'
                            }
                        }
                    },

                    columnDefs: [
                        {
                            targets: 7,
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

        });
    </script>

</body>
</html>
