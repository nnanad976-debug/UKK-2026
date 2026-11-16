
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   AKSES WALI KELAS
========================================= */

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

/* =========================================
   FUNGSI
========================================= */

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* =========================================
   IDENTITAS WALI KELAS
========================================= */

$user_id = (int)($_SESSION['user_id'] ?? 0);
$guru_id = (int)($_SESSION['guru_id'] ?? 0);
$nama_wali = $_SESSION['nama'] ?? 'Wali Kelas';

if ($guru_id <= 0 && $user_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    if (!$stmt) {
        exit("Data guru gagal diproses.");
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $guru = mysqli_fetch_assoc($hasil);

    if ($guru) {
        $guru_id = (int)$guru['id'];
        $nama_wali = $guru['nama'];
    }

    mysqli_stmt_close($stmt);
}

if ($guru_id <= 0) {
    http_response_code(403);
    exit("Data wali kelas tidak ditemukan.");
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$tahun_ajaran_id = 0;
$nama_tahun = '';
$pesan_error = '';

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY tanggal_mulai DESC, id DESC
     LIMIT 1"
);

if (!$stmt) {
    exit("Data tahun ajaran gagal diproses.");
}

mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$tahun = mysqli_fetch_assoc($hasil);

if ($tahun) {
    $tahun_ajaran_id = (int)$tahun['id'];
    $nama_tahun = $tahun['nama'];
} else {
    $pesan_error = "Belum ada tahun ajaran yang aktif.";
}

mysqli_stmt_close($stmt);

/* =========================================
   REKAP POIN SELURUH SISWA
========================================= */

$daftar_rekap = [];

if ($pesan_error === '') {
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

    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        $pesan_error = "Data rekap poin gagal diproses.";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);

        if (mysqli_stmt_execute($stmt)) {
            $hasil = mysqli_stmt_get_result($stmt);

            while ($data = mysqli_fetch_assoc($hasil)) {
                $daftar_rekap[] = $data;
            }
        } else {
            $pesan_error = "Data rekap poin gagal dimuat.";
        }

        mysqli_stmt_close($stmt);
    }
}

/* =========================================
   STATISTIK
========================================= */

$total_siswa = count($daftar_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($daftar_rekap as $data) {
    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];
    $total_poin += (int)$data['total_poin'];
}

$halamanAktifSidebar = basename($_SERVER['PHP_SELF'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin Siswa - Sistem Pelanggaran</title>

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
            --shadow: 0 8px 22px rgba(32, 62, 104, .06);
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select,
        table {
            font-family: inherit;
        }

        /* =================================
           SIDEBAR - STANDAR WALI KELAS
        ================================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
            border-right: 1px solid var(--line);
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            margin-bottom: 5px;
            color: var(--blue-dark);
            font-size: 19px;
            font-weight: 700;
            line-height: 1.4;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
            line-height: 1.4;
        }

        .menu-title {
            margin: 0 14px 11px;
            color: #91a3b9;
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
            font-weight: 400;
            line-height: 1.4;
            background: transparent;
        }

        .menu-link:hover {
            color: var(--blue);
            background: var(--blue-soft);
        }

        .menu-link.active {
            color: var(--blue);
            background: var(--blue-soft);
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
            color: #c03935;
            background: #fff0ef;
        }

        /* =================================
           KONTEN UTAMA
        ================================= */

        .main {
            width: calc(100% - 282px);
            min-width: 0;
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

        .page-heading {
            padding: 24px 25px;
            margin-bottom: 22px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 11px;
            box-shadow: var(--shadow);
        }

        .page-heading h1 {
            margin: 0 0 7px;
            color: var(--blue-dark);
            font-size: 26px;
            font-weight: 700;
            line-height: 1.35;
        }

        .page-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
        }

        /* =================================
           INFORMASI
        ================================= */

        .class-info {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 18px;
            padding: 13px 15px;
            margin-bottom: 20px;
            background: #f5f9ff;
            border: 1px solid var(--line);
            border-radius: 9px;
        }

        .class-info-item {
            color: #617b98;
            font-size: 12px;
        }

        .class-info-item strong {
            color: var(--blue-dark);
            font-weight: 600;
        }

        /* =================================
           STATISTIK
        ================================= */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .summary-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            min-height: 126px;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .summary-label {
            margin-bottom: 9px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
        }

        .summary-value {
            color: var(--blue-dark);
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .summary-description {
            margin-top: 7px;
            color: #91a5bd;
            font-size: 12px;
        }

        /* =================================
           PANEL TABEL
        ================================= */

        .panel {
            min-width: 0;
            padding: 25px;
            margin-bottom: 22px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .panel-header {
            margin-bottom: 22px;
        }

        .panel-header h2 {
            margin: 0 0 6px;
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
        }

        .panel-header p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
        }

        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #tabelRekap {
            width: 100% !important;
            min-width: 760px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 13px;
            vertical-align: middle;
        }

        #tabelRekap thead th {
            padding: 14px 12px;
            color: #315b82;
            background: #edf4fb;
            border-color: #dce7f1;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelRekap tbody td {
            padding: 13px 11px;
            border-color: #e2eaf3;
            line-height: 1.45;
            vertical-align: middle;
        }

        #tabelRekap tbody tr {
            height: 53px;
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
            font-weight: 500;
        }

        .nama-kelas {
            color: #344b63;
            white-space: nowrap;
        }

        .badge-poin {
            display: inline-block;
            min-width: 38px;
            padding: 6px 10px;
            color: var(--blue);
            background: var(--blue-soft);
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }

        .badge-jumlah {
            color: #526b86;
            font-weight: 600;
        }

        /* =================================
           DATATABLES
        ================================= */

        .dt-container {
            width: 100%;
            min-width: 0;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
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
            font-weight: 400;
            white-space: nowrap;
        }

        .dt-container .dt-length select,
        .dt-container .dt-search input {
            height: 38px;
            color: var(--text);
            background: #fff;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            font-family: inherit;
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
            font-family: inherit;
            font-size: 13px;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            color: #fff !important;
            background: var(--blue) !important;
            border-color: var(--blue) !important;
        }

        /* =================================
           DATA KOSONG DAN FOOTER
        ================================= */

        .empty-state {
            padding: 35px 15px;
            color: var(--muted);
            background: #f8fbff;
            border: 1px dashed var(--line);
            border-radius: 9px;
            text-align: center;
        }

        .empty-state h5 {
            margin: 0;
            color: var(--blue-dark);
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
            margin-top: 17px;
            color: var(--muted);
            font-size: 12px;
            text-align: right;
        }

        /* =================================
           RESPONSIVE
        ================================= */

        @media (max-width: 1100px) {
            .main {
                padding-right: 20px;
                padding-left: 20px;
            }

            .summary-grid {
                gap: 12px;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
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

            .page-heading {
                padding: 20px;
            }

            .page-heading h1 {
                font-size: 23px;
            }

            .panel {
                padding: 17px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 11px;
            }

            .summary-card {
                min-height: 110px;
                padding: 18px;
            }

            .summary-value {
                font-size: 27px;
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

        <a href="../dashboard.php"
           class="menu-link <?= $halamanAktifSidebar === 'dashboard.php' ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="catat_pelanggaran.php"
           class="menu-link <?= $halamanAktifSidebar === 'catat_pelanggaran.php' ? 'active' : ''; ?>">
            Catat Pelanggaran
        </a>

        <a href="riwayat.php"
           class="menu-link <?= $halamanAktifSidebar === 'riwayat.php' ? 'active' : ''; ?>">
            Riwayat
        </a>

        <a href="data_siswa.php"
           class="menu-link <?= $halamanAktifSidebar === 'data_siswa.php' ? 'active' : ''; ?>">
            Data Siswa
        </a>

        <a href="rekap_poin.php"
           class="menu-link <?= $halamanAktifSidebar === 'rekap_poin.php' ? 'active' : ''; ?>">
            Rekap Poin Siswa
        </a>

        <a href="rekap_pelanggaran.php"
           class="menu-link <?= $halamanAktifSidebar === 'rekap_pelanggaran.php' ? 'active' : ''; ?>">
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php"
           class="menu-link <?= $halamanAktifSidebar === 'cetak_rekap.php' ? 'active' : ''; ?>">
            Laporan
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

        <header class="page-heading">
            <h1>Rekap Poin Siswa</h1>
            <p>
                Ringkasan jumlah pelanggaran dan total poin seluruh siswa
                berdasarkan catatan dari semua guru dan wali kelas.
            </p>
        </header>

        <?php if ($pesan_error !== ''): ?>

            <div class="alert alert-warning">
                <?= e($pesan_error); ?>
            </div>

        <?php else: ?>

            <div class="class-info">

                <div class="class-info-item">
                    Wali Kelas:
                    <strong><?= e($nama_wali); ?></strong>
                </div>

                <div class="class-info-item">
                    Tahun Ajaran:
                    <strong><?= e($nama_tahun); ?></strong>
                </div>

                <div class="class-info-item">
                    Data:
                    <strong>Seluruh Kelas</strong>
                </div>

            </div>

            <!-- STATISTIK -->

            <div class="summary-grid">

                <div class="summary-card">
                    <div class="summary-label">Jumlah Siswa</div>
                    <div class="summary-value">
                        <?= number_format($total_siswa, 0, ',', '.'); ?>
                    </div>
                    <div class="summary-description">
                        Siswa dengan catatan pelanggaran
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Jumlah Pelanggaran</div>
                    <div class="summary-value">
                        <?= number_format($total_pelanggaran, 0, ',', '.'); ?>
                    </div>
                    <div class="summary-description">
                        Catatan dari seluruh pencatat
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Total Poin</div>
                    <div class="summary-value">
                        <?= number_format($total_poin, 0, ',', '.'); ?>
                    </div>
                    <div class="summary-description">
                        Akumulasi poin seluruh siswa
                    </div>
                </div>

            </div>

            <!-- TABEL REKAP -->

            <section class="panel">

                <div class="panel-header">
                    <h2>Data Rekap Poin</h2>
                    <p>
                        Daftar akumulasi poin setiap siswa dari seluruh kelas
                        berdasarkan tahun ajaran aktif.
                    </p>
                </div>

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
                                        <td><?= $index + 1; ?></td>

                                        <td class="nama-siswa">
                                            <?= e($data['nama_siswa']); ?>
                                        </td>

                                        <td class="nama-kelas">
                                            <?= e($data['nama_kelas'] ?: '-'); ?>
                                        </td>

                                        <td>
                                            <span class="badge-jumlah">
                                                <?= number_format(
                                                    (int)$data['jumlah_pelanggaran'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>
                                            </span>
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
                            Belum ada catatan pelanggaran siswa pada
                            tahun ajaran aktif.
                        </p>
                    </div>

                <?php endif; ?>

            </section>

        <?php endif; ?>

        <div class="footer-note">
            SIPSI — Sistem Informasi Pelanggaran Siswa
        </div>

    </div>

</main>

<!-- JAVASCRIPT -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {

    const tabel = document.getElementById('tabelRekap');

    if (!tabel) {
        return;
    }

    if ($.fn.DataTable.isDataTable(tabel)) {
        return;
    }

    $('#tabelRekap').DataTable({
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

});
</script>

</body>
</html>
