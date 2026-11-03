
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

/* =========================================
   CEK HAK AKSES GURU
========================================= */
if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

/* =========================================
   FUNGSI OUTPUT
========================================= */
function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

/* =========================================
   DATA GURU
========================================= */
$user_id = (int)($_SESSION['user_id'] ?? 0);

$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id = ?
     LIMIT 1"
);

if (!$stmt_guru) {
    exit("Data guru gagal diproses.");
}

mysqli_stmt_bind_param($stmt_guru, "i", $user_id);
mysqli_stmt_execute($stmt_guru);

$hasil_guru = mysqli_stmt_get_result($stmt_guru);
$data_guru = mysqli_fetch_assoc($hasil_guru);

mysqli_stmt_close($stmt_guru);

if (!$data_guru) {
    exit("Data guru tidak ditemukan. Silakan hubungi Admin.");
}

$guru_id = (int)$data_guru['id'];
$nama_guru = $data_guru['nama'];

/* =========================================
   DATA REKAP POIN
========================================= */
$daftar_rekap = [];
$pesan_error = '';

$sql = "
    SELECT
        nama_siswa,
        nama_kelas,
        COUNT(id) AS jumlah_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin
    FROM t_pelanggaran_siswa
    WHERE guru_id = ?
      AND status IN ('Ditindaklanjuti', 'Selesai')
    GROUP BY nama_siswa, nama_kelas
    ORDER BY total_poin DESC, nama_siswa ASC
";

$stmt_rekap = mysqli_prepare($koneksi, $sql);

if ($stmt_rekap) {

    mysqli_stmt_bind_param(
        $stmt_rekap,
        "i",
        $guru_id
    );

    if (mysqli_stmt_execute($stmt_rekap)) {

        $hasil_rekap = mysqli_stmt_get_result($stmt_rekap);

        while ($data = mysqli_fetch_assoc($hasil_rekap)) {
            $daftar_rekap[] = $data;
        }

    } else {

        $pesan_error = "Data rekap poin gagal dimuat.";

    }

    mysqli_stmt_close($stmt_rekap);

} else {

    $pesan_error = "Data rekap poin gagal diproses.";

}

/* =========================================
   RINGKASAN TIGA KOTAK
========================================= */
$total_siswa = count($daftar_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($daftar_rekap as $data) {

    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];

    $total_poin += (int)$data['total_poin'];

}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin - Sistem Pelanggaran</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- DATATABLES -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >

    <style>

        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f5f8fc;
            --white: #ffffff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        /* =====================================
           SIDEBAR - SAMA DENGAN RIWAYAT
        ===================================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 242px;
            height: 100vh;
            padding: 25px 12px 16px;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-right: 1px solid var(--line);
            z-index: 1000;
        }

        .brand {
            padding: 0 13px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            color: var(--blue-dark);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 11px;
        }

        .menu-title {
            margin: 0 13px 10px;
            color: #91a5bd;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            min-height: 40px;
            padding: 10px 13px;
            border-radius: 9px;
            color: #5e7695;
            font-size: 12px;
            font-weight: 400;
            transition: .2s ease;
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
            color: #c03935;
        }

        /* =====================================
           KONTEN UTAMA
        ===================================== */

        .main {
            width: calc(100% - 242px);
            min-height: 100vh;
            margin-left: 242px;
            padding: 20px 24px 35px;
        }

        .page-container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        /* =====================================
           KOTAK JUDUL
           SAMA SEPERTI RIWAYAT
        ===================================== */

        .page-header {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 19px 18px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .04);
        }

        .judul {
            color: #234e78;
            font-size: 25px;
            line-height: 1.3;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .subjudul {
            color: var(--muted);
            font-size: 12px;
            margin: 0;
        }

        /* =====================================
           TIGA KOTAK STATISTIK
           TETAP DIPERTAHANKAN
        ===================================== */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .summary-card {
            min-width: 0;
            min-height: 126px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 19px 21px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .04);
        }

        .summary-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
            margin-bottom: 9px;
        }

        .summary-value {
            color: var(--blue-dark);
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .summary-description {
            color: #91a5bd;
            font-size: 11px;
            margin-top: 7px;
        }

        /* =====================================
           KOTAK DATA REKAP
           SAMA SEPERTI RIWAYAT
        ===================================== */

        .content-card {
            min-width: 0;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 18px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .04);
        }

        .section-title {
            color: #234e78;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .section-description {
            color: var(--muted);
            font-size: 11px;
            margin: 0 0 21px;
        }

        /* =====================================
           TABEL REKAP
        ===================================== */

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 7px;
        }

        #tabelRekap {
            width: 100% !important;
            min-width: 800px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            vertical-align: middle;
        }

        #tabelRekap thead th {
            padding: 12px 10px;
            background: #edf4fb;
            color: #315b82;
            border-color: #dce7f1;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelRekap tbody td {
            padding: 12px 10px;
            background: #ffffff;
            color: #344b63;
            border-color: #e2eaf3;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            vertical-align: middle;
            line-height: 1.5;
        }

        #tabelRekap tbody tr {
            height: 49px;
        }

        #tabelRekap tbody tr:hover > * {
            background-color: #f5f9fe;
        }

        #tabelRekap th:first-child,
        #tabelRekap td:first-child,
        #tabelRekap th:nth-child(4),
        #tabelRekap td:nth-child(4),
        #tabelRekap th:nth-child(5),
        #tabelRekap td:nth-child(5) {
            text-align: center;
        }

        .nama-siswa {
            color: #344b63 !important;
            font-weight: 400 !important;
        }

        .nama-kelas {
            color: #344b63 !important;
            white-space: nowrap;
        }

        .badge-poin {
            display: inline-block;
            min-width: 36px;
            padding: 6px 9px;
            background: var(--blue-soft);
            color: var(--blue);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        /* =====================================
           TOMBOL RIWAYAT
        ===================================== */

        .btn-riwayat {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 5px 10px;
            border: 1px solid #b9d5f1;
            border-radius: 6px;
            background: #f4f9ff;
            color: #2878cf;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            font-weight: 400;
            white-space: nowrap;
            transition: .2s ease;
        }

        .btn-riwayat:hover {
            background: #2878cf;
            border-color: #2878cf;
            color: #ffffff;
        }

        /* =====================================
           DATATABLES
           FONT MENGIKUTI RIWAYAT
        ===================================== */

        .dt-container {
            width: 100%;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
        }

        .dt-container .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin: 12px 0;
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
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            font-weight: 400;
            white-space: nowrap;
        }

        .dt-container .dt-length select,
        .dt-container .dt-search input {
            height: 34px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #ffffff;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            outline: none;
        }

        .dt-container .dt-length select {
            width: 56px;
            padding: 5px 7px;
        }

        .dt-container .dt-search input {
            width: 190px;
            margin-left: 0 !important;
            padding: 7px 10px;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        /* =====================================
           INFORMASI JUMLAH DATA
        ===================================== */

        .dt-container .dt-info {
            color: #526b86;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            font-weight: 400;
            padding-top: 4px;
        }

        /* =====================================
           PAGINATION MENYATU
           SAMA SEPERTI SCREENSHOT RIWAYAT
        ===================================== */

        .dt-container .dt-paging {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0 !important;
        }

        .dt-container .dt-paging nav {
            display: inline-flex;
            align-items: center;
            gap: 0 !important;
        }

        .dt-container .dt-paging .dt-paging-button {
            position: relative;
            min-width: 35px;
            min-height: 35px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;

            margin: 0 !important;
            margin-left: -1px !important;

            padding: 6px 11px !important;

            border: 1px solid #dee2e6 !important;
            border-radius: 0 !important;

            background: #e9ecef !important;
            color: #526b86 !important;

            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px !important;
            font-weight: 400 !important;

            box-shadow: none !important;
            transition: .15s ease;
        }

        .dt-container .dt-paging .dt-paging-button:first-child {
            margin-left: 0 !important;
            border-top-left-radius: 5px !important;
            border-bottom-left-radius: 5px !important;
        }

        .dt-container .dt-paging .dt-paging-button:last-child {
            border-top-right-radius: 5px !important;
            border-bottom-right-radius: 5px !important;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            position: relative;
            z-index: 2;
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 400 !important;
        }

        .dt-container .dt-paging .dt-paging-button:hover:not(.disabled):not(.current) {
            background: #dfe6ee !important;
            border-color: #ced6df !important;
            color: #344b63 !important;
        }

        .dt-container .dt-paging .dt-paging-button.disabled {
            background: #e9ecef !important;
            color: #8996a5 !important;
            cursor: default !important;
            opacity: 1 !important;
        }

        /* =====================================
           DATA KOSONG DAN PESAN
        ===================================== */

        .empty-state {
            padding: 30px 15px;
            text-align: center;
            color: var(--muted);
        }

        .empty-state h5 {
            color: var(--blue-dark);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 12px;
            margin-bottom: 0;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
        }

        .footer-note {
            color: var(--muted);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            margin-top: 17px;
        }

        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 1100px) {

            .main {
                padding-left: 19px;
                padding-right: 19px;
            }

            .summary-grid {
                gap: 11px;
            }

            .summary-card {
                padding: 17px;
            }

        }

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
                padding: 20px 16px 30px;
            }

        }

        @media (max-width: 650px) {

            .main {
                padding: 16px 12px 25px;
            }

            .judul {
                font-size: 23px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 11px;
            }

            .summary-card {
                min-height: 112px;
                padding: 18px;
            }

            .summary-value {
                font-size: 27px;
            }

            .content-card {
                padding: 15px;
            }

            .dt-container .dt-layout-cell {
                width: 100%;
            }

            .dt-container .dt-search input {
                width: 150px;
            }

        }

    </style>

</head>

<body>

    <!-- =====================================
         SIDEBAR GURU
    ===================================== -->

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-title">
                Sistem Pelanggaran
            </div>

            <div class="brand-subtitle">
                SMK Muhammadiyah
            </div>

        </div>

        <div class="menu-title">
            Menu Utama
        </div>

        <nav class="menu">

            <a href="../dashboard.php" class="menu-link">
                Dashboard
            </a>

            <a href="catat_pelanggaran.php" class="menu-link">
                Catat Pelanggaran
            </a>

            <a href="tindakan.php" class="menu-link">
                Tindakan
            </a>

            <a href="riwayat.php" class="menu-link">
                Riwayat
            </a>

            <a
                href="rekap_poin.php"
                class="menu-link active"
                aria-current="page"
            >
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

    <!-- =====================================
         KONTEN UTAMA
    ===================================== -->

    <main class="main">

        <div class="page-container">

            <!-- KOTAK JUDUL -->

            <div class="page-header">

                <h1 class="judul">
                    Rekap Poin Siswa
                </h1>

                <p class="subjudul">
                    Ringkasan jumlah pelanggaran dan total poin siswa.
                </p>

            </div>

            <!-- =====================================
                 TIGA KOTAK STATISTIK
            ===================================== -->

            <?php if ($pesan_error === ''): ?>

                <div class="summary-grid">

                    <div class="summary-card">

                        <div class="summary-label">
                            Jumlah Siswa
                        </div>

                        <div class="summary-value">
                            <?= number_format($total_siswa, 0, ',', '.'); ?>
                        </div>

                        <div class="summary-description">
                            Siswa dengan catatan pelanggaran
                        </div>

                    </div>

                    <div class="summary-card">

                        <div class="summary-label">
                            Jumlah Pelanggaran
                        </div>

                        <div class="summary-value">
                            <?= number_format($total_pelanggaran, 0, ',', '.'); ?>
                        </div>

                        <div class="summary-description">
                            Pelanggaran yang sudah ditindaklanjuti
                        </div>

                    </div>

                    <div class="summary-card">

                        <div class="summary-label">
                            Total Poin
                        </div>

                        <div class="summary-value">
                            <?= number_format($total_poin, 0, ',', '.'); ?>
                        </div>

                        <div class="summary-description">
                            Akumulasi poin seluruh siswa
                        </div>

                    </div>

                </div>

            <?php endif; ?>

            <!-- PESAN ERROR -->

            <?php if ($pesan_error !== ''): ?>

                <div class="alert alert-warning mb-4">
                    <?= e($pesan_error); ?>
                </div>

            <?php else: ?>

                <!-- KOTAK DATA REKAP -->

                <section class="content-card">

                    <h2 class="section-title">
                        Data Rekap Poin
                    </h2>

                    <p class="section-description">
                        Daftar akumulasi poin pelanggaran siswa berdasarkan catatan Anda.
                    </p>

                    <div class="table-responsive">

                        <table
                            id="tabelRekap"
                            class="table table-bordered table-hover align-middle"
                        >

                            <thead>

                                <tr>

                                    <th>No.</th>

                                    <th>Nama Siswa</th>

                                    <th>Kelas</th>

                                    <th>Jumlah Pelanggaran</th>

                                    <th>Total Poin</th>

                                    <th>Riwayat</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($daftar_rekap as $index => $data): ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>

                                        <td class="nama-siswa">
                                            <?= e($data['nama_siswa']); ?>
                                        </td>

                                        <td class="nama-kelas">
                                            <?= e($data['nama_kelas'] ?: '-'); ?>
                                        </td>

                                        <td class="text-center">
                                            <?= number_format(
                                                (int)$data['jumlah_pelanggaran'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>
                                        </td>

                                        <td class="text-center">

                                            <span class="badge-poin">
                                                <?= number_format(
                                                    (int)$data['total_poin'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>
                                            </span>

                                        </td>

                                        <td>

                                            <a
                                                href="riwayat.php?<?= e(http_build_query([
                                                    'nama_siswa' => $data['nama_siswa'],
                                                    'nama_kelas' => $data['nama_kelas']
                                                ])); ?>"
                                                class="btn-riwayat"
                                            >
                                                Lihat Riwayat
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </section>

            <?php endif; ?>

            <!-- FOOTER -->

            <p class="footer-note">
                SIKET — Sistem Informasi Ketertiban Siswa
            </p>

        </div>

    </main>

    <!-- =====================================
         JAVASCRIPT
    ===================================== -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

    <script>

        $(document).ready(function () {

            $('#tabelRekap').DataTable({

                pageLength: 10,

                lengthMenu: [10, 25, 50, 100],

                order: [],

                autoWidth: false,

                scrollX: true,

                language: {

                    search: "Cari:",

                    searchPlaceholder: "",

                    lengthMenu: "Tampilkan _MENU_ data",

                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",

                    infoFiltered: "(disaring dari _MAX_ data)",

                    zeroRecords: "Data yang dicari tidak ditemukan",

                    emptyTable: "Belum ada data rekap poin",

                    paginate: {

                        first: "«",

                        last: "»",

                        previous: "Sebelumnya",

                        next: "Berikutnya"

                    }

                }

            });

        });

    </script>

</body>

</html>
