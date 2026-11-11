
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

/* =========================================
   PEMERIKSAAN AKSES
========================================= */

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

/* =========================================
   FUNGSI
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
   TAHUN AJARAN AKTIF
========================================= */

$tahun_ajaran_id = 0;

$stmt_tahun = mysqli_prepare(
    $koneksi,
    "SELECT id
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY tanggal_mulai DESC, id DESC
     LIMIT 1"
);

if (!$stmt_tahun) {
    exit("Data tahun ajaran gagal diproses.");
}

mysqli_stmt_execute($stmt_tahun);

$hasil_tahun = mysqli_stmt_get_result($stmt_tahun);
$data_tahun = mysqli_fetch_assoc($hasil_tahun);

$tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

mysqli_stmt_close($stmt_tahun);

/* =========================================
   DATA REKAP POIN SELURUH GURU
   TANPA FILTER GURU_ID
========================================= */

$daftar_rekap = [];
$pesan_error = '';

if ($tahun_ajaran_id > 0) {

    $sql = "
        SELECT
            siswa_id,
            nama_siswa,
            kelas_id,
            nama_kelas,
            COUNT(id) AS jumlah_pelanggaran,
            COALESCE(SUM(poin), 0) AS total_poin
        FROM t_pelanggaran_siswa
        WHERE tahun_ajaran_id = ?
        GROUP BY
            siswa_id,
            nama_siswa,
            kelas_id,
            nama_kelas
        ORDER BY
            total_poin DESC,
            jumlah_pelanggaran DESC,
            nama_siswa ASC
    ";

    $stmt_rekap = mysqli_prepare($koneksi, $sql);

    if ($stmt_rekap) {

        mysqli_stmt_bind_param(
            $stmt_rekap,
            "i",
            $tahun_ajaran_id
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

} else {

    $pesan_error = "Belum ada tahun ajaran yang aktif.";

}

/* =========================================
   STATISTIK SELURUH SISWA
========================================= */

$total_siswa = count($daftar_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($daftar_rekap as $data) {

    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];

    $total_poin += (int)$data['total_poin'];

}

/* =========================================
   ACTIVE SIDEBAR
========================================= */

$halamanAktifSidebar = basename($_SERVER['PHP_SELF'] ?? '');

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin - Sistem Pelanggaran</title>

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

        /* KONTEN UTAMA */

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

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .045);
            overflow: hidden;
        }

        .card-body {
            padding: 20px;
            min-width: 0;
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

        /* STATISTIK */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            min-width: 0;
            min-height: 126px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .045);
            transition: .2s ease;
        }

        .summary-card:hover {
            border-color: #c8def5;
        }

        .summary-label {
            color: var(--muted);
            font-size: 13px;
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
            font-size: 12px;
            margin-top: 7px;
        }

        /* DATA REKAP */

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

        /* TABEL */

        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 8px;
            -webkit-overflow-scrolling: touch;
        }

        #tabelRekap {
            width: 100% !important;
            min-width: 850px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 14px;
            vertical-align: middle;
        }

        #tabelRekap thead th {
            padding: 14px 12px;
            background: #edf4fb;
            color: #315b82;
            border-color: #dce7f1;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelRekap tbody td {
            padding: 13px 11px;
            border-color: #e2eaf3;
            vertical-align: middle;
            line-height: 1.45;
        }

        #tabelRekap tbody tr {
            height: 55px;
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
            color: #344b63;
        }

        .nama-kelas {
            color: #344b63;
            white-space: nowrap;
        }

        /* BADGE POIN */

        .badge-poin {
            display: inline-block;
            min-width: 38px;
            padding: 6px 10px;
            border-radius: 7px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }

        /* DATATABLES */

        .dt-container {
            width: 100%;
            min-width: 0;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
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

        .dt-container .dt-info {
            color: #526b86;
            font-size: 13px;
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

        /* DATA KOSONG */

        .empty-state {
            padding: 35px 15px;
            color: var(--muted);
            text-align: center;
            background: #f8fbff;
            border: 1px dashed var(--line);
            border-radius: 9px;
        }

        .empty-state h5 {
            color: #234e78;
            font-size: 16px;
            font-weight: 600;
        }

        .empty-state p {
            margin: 5px 0 0;
            font-size: 13px;
        }

        .alert {
            border-radius: 8px;
            font-size: 13px;
        }

        .footer-note {
            color: var(--muted);
            font-size: 12px;
            margin-top: 17px;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {

            .main {
                padding-left: 20px;
                padding-right: 20px;
            }

            .summary-grid {
                gap: 12px;
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

            #tabelRekap {
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

        <a href="../dashboard.php"
           class="menu-link <?= $halamanAktifSidebar === 'dashboard.php' ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="catat_pelanggaran.php"
           class="menu-link <?= $halamanAktifSidebar === 'catat_pelanggaran.php' ? 'active' : ''; ?>">
            Catat Pelanggaran
        </a>

        <a href="tindakan.php"
           class="menu-link <?= $halamanAktifSidebar === 'tindakan.php' ? 'active' : ''; ?>">
            Tindakan
        </a>

        <a href="riwayat.php"
           class="menu-link <?= $halamanAktifSidebar === 'riwayat.php' ? 'active' : ''; ?>">
            Riwayat
        </a>

        <a href="rekap_poin.php"
           class="menu-link <?= $halamanAktifSidebar === 'rekap_poin.php' ? 'active' : ''; ?>">
            Rekap Poin
        </a>

        <a href="../about.php"
           class="menu-link <?= $halamanAktifSidebar === 'about.php' ? 'active' : ''; ?>">
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

        <!-- JUDUL HALAMAN -->

        <section class="card mb-4">

            <div class="card-body">

                <h1 class="judul mb-1">
                    Rekap Poin Siswa
                </h1>

                <p class="subjudul mb-0">
                    Ringkasan jumlah pelanggaran dan total poin seluruh siswa berdasarkan catatan semua guru.
                </p>

            </div>

        </section>

        <?php if ($pesan_error === ''): ?>

            <!-- STATISTIK -->

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
                        Seluruh catatan pelanggaran siswa
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
                        Akumulasi poin dari seluruh guru
                    </div>

                </div>

            </div>

        <?php endif; ?>

        <?php if ($pesan_error !== ''): ?>

            <div class="alert alert-warning mb-4">
                <?= e($pesan_error); ?>
            </div>

        <?php else: ?>

            <!-- TABEL REKAP -->

            <section class="card">

                <div class="card-body">

                    <h2 class="section-title">
                        Data Rekap Poin
                    </h2>

                    <p class="section-description">
                        Daftar akumulasi poin pelanggaran setiap siswa dari seluruh guru pada tahun ajaran aktif.
                    </p>

                    <?php if (!empty($daftar_rekap)): ?>

                        <div class="table-responsive">

                            <table id="tabelRekap"
                                   class="table table-bordered table-hover align-middle">

                                <thead>

                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jumlah Pelanggaran</th>
                                        <th>Total Poin</th>
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

                                            <td>
                                                <?= number_format(
                                                    (int)$data['jumlah_pelanggaran'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>
                                            </td>

                                            <td>

                                                <span class="badge-poin">
                                                    <?= number_format(
                                                        (int)$data['total_poin'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ); ?>
                                                </span>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                        <div class="empty-state">

                            <h5>Belum Ada Data Rekap Poin</h5>

                            <p>
                                Data pelanggaran siswa pada tahun ajaran aktif akan ditampilkan di sini.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        <?php endif; ?>

        <p class="footer-note">
            SIKET — Sistem Informasi Ketertiban Siswa
        </p>

    </div>

</main>

<!-- JAVASCRIPT -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

$(document).ready(function () {

    const tabel = $('#tabelRekap');

    if (tabel.length) {

        tabel.DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [],

            autoWidth: false,

            language: {

                search: 'Cari:',

                searchPlaceholder: '',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty: 'Tidak ada data rekap poin',

                infoFiltered: '(disaring dari _MAX_ data)',

                zeroRecords: 'Data rekap poin tidak ditemukan',

                emptyTable: 'Belum ada data rekap poin.',

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
