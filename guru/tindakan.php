
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

$user_id = (int)($_SESSION['user_id'] ?? 0);

$pesan = '';
$jenis_pesan = '';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// AMBIL DATA GURU
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$guru_id = (int)($data_guru['id'] ?? 0);

// PROSES SIMPAN TINDAKAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $id = (int)($_POST['id'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $status_valid = [
        'Diproses',
        'Ditindaklanjuti'
    ];

    if ($guru_id <= 0) {
        $pesan = 'Data guru tidak ditemukan.';
        $jenis_pesan = 'danger';

    } elseif (
        $id <= 0 ||
        $tindakan === '' ||
        !in_array($status, $status_valid, true)
    ) {
        $pesan = 'Lengkapi tindakan dan pilih status yang benar.';
        $jenis_pesan = 'danger';

    } else {

        // PASTIKAN DATA MILIK GURU YANG LOGIN
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_siswa
             WHERE id = ?
             AND guru_id = ?
             AND COALESCE(status, '') <> 'Selesai'
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "ii", $id, $guru_id);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$cek) {
            $pesan = 'Data pelanggaran tidak ditemukan atau sudah selesai.';
            $jenis_pesan = 'danger';

        } else {

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_pelanggaran_siswa
                 SET tindakan = ?, status = ?
                 WHERE id = ?
                 AND guru_id = ?
                 AND COALESCE(status, '') <> 'Selesai'"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssii",
                $tindakan,
                $status,
                $id,
                $guru_id
            );

            $berhasil = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($berhasil) {
                header("Location: tindakan.php?berhasil=1");
                exit;
            } else {
                $pesan = 'Gagal menyimpan tindakan.';
                $jenis_pesan = 'danger';
            }
        }
    }
}

// PROSES TOMBOL SELESAI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selesai'])) {

    $id = (int)($_POST['id'] ?? 0);

    if ($guru_id <= 0 || $id <= 0) {

        $pesan = 'Data pelanggaran tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        // CEK KEPEMILIKAN DATA DAN STATUS
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_siswa
             WHERE id = ?
             AND guru_id = ?
             AND COALESCE(status, '') <> 'Selesai'
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "ii", $id, $guru_id);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$cek) {

            $pesan = 'Data pelanggaran tidak ditemukan atau sudah selesai.';
            $jenis_pesan = 'danger';

        } else {

            // UBAH STATUS MENJADI SELESAI
            // TINDAKAN YANG SUDAH DIISI TETAP DISIMPAN
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_pelanggaran_siswa
                 SET status = 'Selesai'
                 WHERE id = ?
                 AND guru_id = ?
                 AND COALESCE(status, '') <> 'Selesai'"
            );

            mysqli_stmt_bind_param($stmt, "ii", $id, $guru_id);

            $berhasil = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($berhasil) {
                header("Location: tindakan.php?selesai=1");
                exit;
            } else {
                $pesan = 'Gagal menyelesaikan pelanggaran.';
                $jenis_pesan = 'danger';
            }
        }
    }
}

// NOTIFIKASI
if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Tindakan dan status berhasil disimpan!';
    $jenis_pesan = 'success';
}

if (($_GET['selesai'] ?? '') === '1') {
    $pesan = 'Pelanggaran berhasil diselesaikan dan dipindahkan ke Riwayat!';
    $jenis_pesan = 'success';
}

// AMBIL DATA PELANGGARAN YANG BELUM SELESAI
$daftar_pelanggaran = [];

if ($guru_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, tanggal, nama_siswa, nama_kelas,
                nama_pelanggaran, poin, keterangan,
                tindakan, status
         FROM t_pelanggaran_siswa
         WHERE guru_id = ?
         AND COALESCE(status, '') <> 'Selesai'
         ORDER BY tanggal DESC, id DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $guru_id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);

    while ($data = mysqli_fetch_assoc($hasil)) {
        $daftar_pelanggaran[] = $data;
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tindakan Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            background: #fff;
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

        /* KONTEN */
        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin: 0 auto;
        }

        .judul {
            color: #234e78;
            font-size: 30px;
            line-height: 1.25;
            font-weight: 650;
        }

        .subjudul {
            color: #7890ad;
            font-size: 14px;
            line-height: 1.5;
        }

        /* CARD */
        .card {
            background: #fff;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .045);
            overflow: hidden;
        }

        .card-body {
            padding: 20px;
            min-width: 0;
        }

        .section-title {
            color: #234e78;
            font-size: 21px;
            line-height: 1.3;
            font-weight: 650;
            margin-bottom: 5px;
        }

        .section-description {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 22px;
        }

        /* TOMBOL */
        .btn {
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 13px;
        }

        .btn-primary {
            --bs-btn-bg: #2878cf;
            --bs-btn-border-color: #2878cf;
            --bs-btn-hover-bg: #2168b4;
            --bs-btn-hover-border-color: #2168b4;
        }

        .btn-secondary {
            --bs-btn-bg: #eaf3ff;
            --bs-btn-border-color: #dce9f8;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
        }

        .btn-selesai {
            background: #e4f3e9;
            border: 1px solid #cce8d5;
            color: #34734b;
        }

        .btn-selesai:hover {
            background: #34734b;
            border-color: #34734b;
            color: #fff;
        }

        .back-button {
            margin-bottom: 17px;
        }

        .back-button .btn {
            font-size: 13px;
            padding: 8px 15px;
        }

        /* AKSI TABEL */
        .aksi-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .aksi-wrapper .btn {
            padding: 7px 11px;
            font-size: 12px;
        }

        /* TABEL */
        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 8px;
            -webkit-overflow-scrolling: touch;
        }

        #tabelTindakan {
            width: 100% !important;
            min-width: 1100px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 14px;
            vertical-align: middle;
        }

        #tabelTindakan thead th {
            padding: 14px 12px;
            background: #edf4fb;
            color: #315b82;
            border-color: #dce7f1;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelTindakan tbody td {
            padding: 13px 11px;
            border-color: #e2eaf3;
            vertical-align: middle;
            line-height: 1.45;
        }

        #tabelTindakan tbody tr:hover > * {
            background-color: #f5f9fe;
        }

        #tabelTindakan th:first-child,
        #tabelTindakan td:first-child,
        #tabelTindakan th:nth-child(6),
        #tabelTindakan td:nth-child(6),
        #tabelTindakan th:last-child,
        #tabelTindakan td:last-child {
            text-align: center;
        }

        /* STATUS */
        .status-badge {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-diproses {
            background: #fff2d8;
            color: #966b16;
        }

        .status-ditindaklanjuti {
            background: #e5f0ff;
            color: #2868ad;
        }

        .status-selesai {
            background: #e4f3e9;
            color: #34734b;
        }

        /* DATATABLES */
        .dt-container {
            width: 100%;
            min-width: 0;
            color: var(--text);
        }

        .dt-container .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 15px 0;
        }

        .dt-container .dt-layout-cell {
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
            color: #526b86;
            font-size: 13px;
            white-space: nowrap;
        }

        .dt-container .dt-length select,
        .dt-container .dt-search input {
            height: 38px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            font-size: 13px;
            outline: none;
        }

        .dt-container .dt-length select {
            width: 70px;
            padding: 5px 8px;
        }

        .dt-container .dt-search input {
            width: 205px;
            margin-left: 0 !important;
            padding: 7px 10px;
        }

        .dt-container .dt-search input:focus,
        .dt-container .dt-length select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        .dt-container .dt-paging {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 5px !important;
            font-size: 13px;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            background: var(--blue) !important;
            border-color: var(--blue) !important;
            color: #fff !important;
        }

        .dt-container .dt-info {
            color: #526b86;
            font-size: 13px;
        }

        /* MODAL */
        .modal-content {
            overflow: hidden;
            border: 1px solid #dce9f8;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(35, 78, 120, .12);
        }

        .modal-header {
            padding: 17px 20px;
            background: #f5f9fe;
            border-bottom-color: #e1eaf4;
        }

        .modal-title {
            color: #234e78;
            font-size: 19px;
            font-weight: 600;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 14px 20px;
            border-top-color: #e1eaf4;
        }

        .form-label {
            color: #405c7b;
            font-size: 13px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            font-size: 14px;
            padding: 9px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        .alert {
            border-radius: 9px;
            font-size: 13px;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .logout {
                margin-top: 14px;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 20px 18px 30px;
            }
        }

        @media (max-width: 650px) {
            .main {
                padding: 16px 12px 25px;
            }

            .card-body {
                padding: 16px;
            }

            .judul {
                font-size: 25px;
            }

            #tabelTindakan {
                font-size: 13px;
            }

            .dt-container .dt-layout-row {
                flex-wrap: wrap;
            }

            .dt-container .dt-layout-cell {
                width: 100%;
            }

            .dt-container .dt-search {
                justify-content: flex-start;
            }

            .dt-container .dt-search input {
                width: 150px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">

        <a href="../dashboard.php" class="menu-link">
            Dashboard
        </a>

        <a href="catat_pelanggaran.php" class="menu-link">
            Catat Pelanggaran
        </a>

        <a href="tindakan.php" class="menu-link active">
            Tindakan
        </a>

        <a href="riwayat.php" class="menu-link">
            Riwayat
        </a>

        <a href="rekap_poin.php" class="menu-link">
            Rekap Poin
        </a>

        <a href="../about.php" class="menu-link">
            About Me
        </a>

    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">
            Logout
        </a>
    </div>

</aside>

<!-- KONTEN UTAMA -->
<main class="main">

    <div class="page-container">

        <div class="back-button">
            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <!-- JUDUL HALAMAN -->
        <section class="card mb-4">
            <div class="card-body">

                <h1 class="judul mb-1">
                    Tindakan Pelanggaran
                </h1>

                <p class="subjudul mb-0">
                    Kelola tindakan dan status pelanggaran siswa.
                </p>

            </div>
        </section>

        <!-- NOTIFIKASI -->
        <?php if ($pesan !== ''): ?>

            <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show mb-4"
                 role="alert">

                <?= e($pesan); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup">
                </button>

            </div>

        <?php endif; ?>

        <!-- DATA PELANGGARAN -->
        <section class="card">

            <div class="card-body">

                <h2 class="section-title">
                    Data Pelanggaran
                </h2>

                <p class="section-description">
                    Daftar pelanggaran yang belum selesai ditindaklanjuti oleh Anda.
                </p>

                <?php if (!empty($daftar_pelanggaran)): ?>

                    <div class="table-responsive">

                        <table id="tabelTindakan"
                               class="table table-bordered table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
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

                                <?php foreach ($daftar_pelanggaran as $index => $data): ?>

                                    <?php
                                    $id_data = (int)$data['id'];

                                    $status_data = trim($data['status'] ?? '');

                                    if ($status_data === '') {
                                        $status_data = 'Diproses';
                                    }

                                    $class_status = 'status-diproses';

                                    if ($status_data === 'Ditindaklanjuti') {
                                        $class_status = 'status-ditindaklanjuti';
                                    }

                                    $tanggal = !empty($data['tanggal'])
                                        ? date('Y-m-d', strtotime($data['tanggal']))
                                        : '-';
                                    ?>

                                    <tr>

                                        <td><?= $index + 1; ?></td>

                                        <td><?= e($tanggal); ?></td>

                                        <td><?= e($data['nama_siswa']); ?></td>

                                        <td><?= e($data['nama_kelas'] ?: '-'); ?></td>

                                        <td><?= e($data['nama_pelanggaran']); ?></td>

                                        <td><?= (int)$data['poin']; ?></td>

                                        <td><?= e($data['keterangan'] ?: '-'); ?></td>

                                        <td>
                                            <?= e(trim((string)($data['tindakan'] ?? '')) ?: '-'); ?>
                                        </td>

                                        <td>
                                            <span class="status-badge <?= e($class_status); ?>">
                                                <?= e($status_data); ?>
                                            </span>
                                        </td>

                                        <td>

                                            <div class="aksi-wrapper">

                                                <!-- TOMBOL TINDAKAN -->
                                                <button
                                                    type="button"
                                                    class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTindakan<?= $id_data; ?>">

                                                    Tindakan

                                                </button>

                                                <!-- TOMBOL SELESAI -->
                                                <form method="POST"
                                                      action="tindakan.php"
                                                      class="form-selesai"
                                                      onsubmit="return confirm('Apakah Anda yakin pelanggaran siswa ini sudah selesai ditindaklanjuti? Data akan dipindahkan ke halaman Riwayat.');">

                                                    <input type="hidden"
                                                           name="id"
                                                           value="<?= $id_data; ?>">

                                                    <button
                                                        type="submit"
                                                        name="selesai"
                                                        value="1"
                                                        class="btn btn-selesai btn-sm">

                                                        Selesai

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="text-center py-5">

                        <h5 class="mb-2" style="color:#234e78;">
                            Semua Pelanggaran Sudah Selesai
                        </h5>

                        <p class="text-muted mb-0" style="font-size:13px;">
                            Tidak ada pelanggaran yang perlu ditindaklanjuti saat ini.
                            Data yang sudah selesai dapat dilihat di halaman Riwayat.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </div>

</main>

<!-- MODAL TINDAKAN -->
<?php foreach ($daftar_pelanggaran as $data): ?>

    <?php
    $id_data = (int)$data['id'];

    $status_data = trim($data['status'] ?? '');

    if (!in_array($status_data, [
        'Diproses',
        'Ditindaklanjuti'
    ], true)) {
        $status_data = 'Diproses';
    }
    ?>

    <div class="modal fade"
         id="modalTindakan<?= $id_data; ?>"
         tabindex="-1"
         aria-labelledby="judulModal<?= $id_data; ?>"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="tindakan.php">

                    <div class="modal-header">

                        <h5 class="modal-title"
                            id="judulModal<?= $id_data; ?>">

                            Tindakan Pelanggaran

                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup">
                        </button>

                    </div>

                    <div class="modal-body">

                        <input type="hidden"
                               name="id"
                               value="<?= $id_data; ?>">

                        <div class="mb-3">

                            <label class="form-label">
                                Tanggal
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= e($data['tanggal']); ?>"
                                   readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Siswa
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= e($data['nama_siswa']); ?>"
                                   readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Kelas
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= e($data['nama_kelas'] ?: '-'); ?>"
                                   readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Pelanggaran
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= e($data['nama_pelanggaran']); ?>"
                                   readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label"
                                   for="tindakan<?= $id_data; ?>">

                                Tindakan

                            </label>

                            <textarea
                                id="tindakan<?= $id_data; ?>"
                                name="tindakan"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan tindakan yang diberikan"
                                required><?= e($data['tindakan'] ?? ''); ?></textarea>

                        </div>

                        <div class="mb-2">

                            <label class="form-label"
                                   for="status<?= $id_data; ?>">

                                Status

                            </label>

                            <select
                                id="status<?= $id_data; ?>"
                                name="status"
                                class="form-select"
                                required>

                                <option value="Diproses"
                                    <?= $status_data === 'Diproses' ? 'selected' : ''; ?>>

                                    Diproses

                                </option>

                                <option value="Ditindaklanjuti"
                                    <?= $status_data === 'Ditindaklanjuti' ? 'selected' : ''; ?>>

                                    Ditindaklanjuti

                                </option>

                            </select>

                            <small class="text-muted d-block mt-2">
                                Jika penanganan sudah selesai, gunakan tombol Selesai pada tabel.
                            </small>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                                name="simpan"
                                value="1"
                                class="btn btn-primary">

                            Simpan Tindakan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

<?php endforeach; ?>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    const tabel = $('#tabelTindakan');

    if (tabel.length) {

        tabel.DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [],

            autoWidth: false,

            scrollX: false,

            language: {

                search: 'Cari:',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pelanggaran',

                infoEmpty: 'Tidak ada data pelanggaran',

                infoFiltered: '(disaring dari _MAX_ data)',

                zeroRecords: 'Data pelanggaran tidak ditemukan',

                emptyTable: 'Belum ada data pelanggaran.',

                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }

            }

        });

    }

});
</script>

</body>
</html>
