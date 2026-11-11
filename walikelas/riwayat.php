
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   PEMERIKSAAN AKSES WALI KELAS
========================================= */

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (
    $role !== 'guru' ||
    empty($_SESSION['is_wali_kelas'])
) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
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
   IDENTITAS WALI KELAS LOGIN
========================================= */

$guru_id = (int)($_SESSION['guru_id'] ?? 0);

if ($guru_id <= 0) {

    $user_id = (int)($_SESSION['user_id'] ?? 0);

    if ($user_id > 0) {

        $stmt = $koneksi->prepare("
            SELECT id
            FROM t_guru
            WHERE user_id = ?
            LIMIT 1
        ");

        if ($stmt) {

            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $data_guru = $stmt->get_result()->fetch_assoc();

            $guru_id = (int)($data_guru['id'] ?? 0);

            $stmt->close();
        }
    }
}

if ($guru_id <= 0) {
    http_response_code(403);
    exit("Identitas Wali Kelas tidak ditemukan.");
}

$nama = $_SESSION['nama'] ?? 'Wali Kelas';

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$tahun_ajaran_id = 0;

$stmt = $koneksi->prepare("
    SELECT id
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY tanggal_mulai DESC, id DESC
    LIMIT 1
");

if ($stmt) {

    $stmt->execute();

    $data_tahun = $stmt->get_result()->fetch_assoc();

    $tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);

    $stmt->close();
}

/* =========================================
   AMBIL RIWAYAT WALI KELAS LOGIN

   HANYA DATA YANG DICATAT OLEH
   WALI KELAS YANG SEDANG LOGIN

   HANYA STATUS SELESAI
========================================= */

$daftar_riwayat = [];

if ($guru_id > 0 && $tahun_ajaran_id > 0) {

    $sql_riwayat = "
        SELECT
            id,
            tanggal,
            nama_siswa,
            nama_kelas,
            nama_pelanggaran,
            poin,
            nama_guru,
            keterangan,
            tindakan,
            status
        FROM t_pelanggaran_siswa
        WHERE tahun_ajaran_id = ?
          AND guru_id = ?
          AND status = 'Selesai'
        ORDER BY tanggal DESC, id DESC
    ";

    $stmt = $koneksi->prepare($sql_riwayat);

    if (!$stmt) {
        exit("Query riwayat gagal: " . e($koneksi->error));
    }

    $stmt->bind_param(
        "ii",
        $tahun_ajaran_id,
        $guru_id
    );

    $stmt->execute();

    $hasil = $stmt->get_result();

    while ($data = $hasil->fetch_assoc()) {
        $daftar_riwayat[] = $data;
    }

    $stmt->close();
}

/* =========================================
   SIDEBAR AKTIF
========================================= */

$halamanAktif = basename($_SERVER['PHP_SELF'] ?? '');

function menuAktif(string $file, string $halamanAktif): string
{
    return $file === $halamanAktif ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat - Sistem Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >

    <style>

        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f6f9fd;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32,62,104,.07);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* =================================
           SIDEBAR
        ================================= */

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
            font-size: 18px;
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
            font-weight: 500;
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

        /* =================================
           KONTEN UTAMA
        ================================= */

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

        /* =================================
           HEADER HALAMAN
        ================================= */

        .page-head {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 24px 25px;
            margin-bottom: 22px;
            box-shadow: 0 3px 12px rgba(32,62,104,.04);
        }

        .page-head h3 {
            color: var(--blue-dark);
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 7px;
        }

        .page-head p {
            color: var(--muted);
            font-size: 13px;
            margin: 0;
        }

        /* =================================
           PANEL
        ================================= */

        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
            min-width: 0;
        }

        .panel-header {
            margin-bottom: 20px;
        }

        .panel-header h4 {
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .panel-header p {
            color: var(--muted);
            font-size: 12px;
            margin: 0;
        }

        /* =================================
           TABEL
        ================================= */

        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 9px;
            -webkit-overflow-scrolling: touch;
        }

        #tabelRiwayat {
            width: 100% !important;
            min-width: 1200px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 13px;
            vertical-align: middle;
        }

        #tabelRiwayat thead th {
            padding: 14px 12px;
            background: #f5f8fc;
            color: #7890ad;
            border-color: var(--line);
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelRiwayat tbody td {
            padding: 13px 12px;
            border-color: #e2eaf3;
            vertical-align: middle;
            line-height: 1.5;
        }

        #tabelRiwayat tbody tr:hover > * {
            background-color: #fafcff;
        }

        #tabelRiwayat th:first-child,
        #tabelRiwayat td:first-child,
        #tabelRiwayat th:nth-child(6),
        #tabelRiwayat td:nth-child(6) {
            text-align: center;
        }

        /* =================================
           STATUS DAN POIN
        ================================= */

        .status-badge {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-selesai {
            background: #e4f3e9;
            color: #34734b;
        }

        .badge-poin {
            display: inline-block;
            min-width: 38px;
            padding: 6px 10px;
            border-radius: 7px;
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 700;
            text-align: center;
        }

        /* =================================
           DATATABLES
        ================================= */

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
            box-shadow: 0 0 0 .2rem rgba(87,143,190,.14);
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

        /* =================================
           DATA KOSONG
        ================================= */

        .empty-state {
            padding: 35px 15px;
            color: var(--muted);
            text-align: center;
            background: #f8fbff;
            border: 1px dashed var(--line);
            border-radius: 9px;
        }

        .empty-state h5 {
            color: var(--blue-dark);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .empty-state p {
            margin: 0;
            font-size: 13px;
        }

        /* =================================
           FOOTER
        ================================= */

        .page-footer {
            color: #91a5bd;
            font-size: 11px;
            text-align: center;
            margin-top: 25px;
        }

        /* =================================
           RESPONSIVE
        ================================= */

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
                padding: 22px 18px 30px;
            }
        }

        @media (max-width: 650px) {

            .main {
                padding: 20px 14px 25px;
            }

            .page-head {
                padding: 19px;
            }

            .page-head h3 {
                font-size: 23px;
            }

            .panel {
                padding: 17px;
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

<!-- =====================================
     SIDEBAR WALI KELAS
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

        <a
            href="../dashboard.php"
            class="menu-link"
        >
            Dashboard
        </a>

        <a
            href="catat_pelanggaran.php"
            class="menu-link"
        >
            Catat Pelanggaran
        </a>

        <a
            href="riwayat.php"
            class="menu-link active"
            aria-current="page"
        >
            Riwayat
        </a>

        <a
            href="data_siswa.php"
            class="menu-link"
        >
            Data Siswa
        </a>

        <a
            href="rekap_poin.php"
            class="menu-link"
        >
            Rekap Poin Siswa
        </a>

        <a
            href="rekap_pelanggaran.php"
            class="menu-link"
        >
            Rekap Pelanggaran
        </a>

        <a
            href="cetak_rekap.php"
            class="menu-link"
        >
            Laporan
        </a>

        <a
            href="../about.php"
            class="menu-link"
        >
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

        <!-- HEADER HALAMAN -->

        <section class="page-head">

            <h3>Riwayat Pelanggaran</h3>

            <p>
                Lihat riwayat pelanggaran yang telah selesai ditindaklanjuti dan dicatat oleh Anda.
            </p>

        </section>

        <!-- PANEL RIWAYAT -->

        <section class="panel">

            <div class="panel-header">

                <h4>Data Riwayat Pelanggaran</h4>

                <p>
                    Daftar pelanggaran yang telah selesai ditindaklanjuti dan dicatat oleh Wali Kelas yang sedang login.
                </p>

            </div>

            <?php if (!empty($daftar_riwayat)): ?>

                <div class="table-responsive">

                    <table
                        id="tabelRiwayat"
                        class="table table-bordered table-hover align-middle"
                    >

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Poin</th>
                                <th>Guru yang Mencatat</th>
                                <th>Keterangan</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($daftar_riwayat as $index => $data): ?>

                                <?php
                                $tanggal = !empty($data['tanggal'])
                                    ? date('d-m-Y', strtotime($data['tanggal']))
                                    : '-';
                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1; ?>
                                    </td>

                                    <td>
                                        <?= e($tanggal); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_siswa']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_kelas'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_pelanggaran']); ?>
                                    </td>

                                    <td>
                                        <span class="badge-poin">
                                            <?= (int)$data['poin']; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= e($data['nama_guru'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= e($data['keterangan'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            trim((string)($data['tindakan'] ?? '')) ?: '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="status-badge status-selesai">
                                            Selesai
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-state">

                    <h5>Belum Ada Riwayat Pelanggaran</h5>

                    <p>
                        Pelanggaran yang Anda catat dan sudah berstatus Selesai akan ditampilkan di sini.
                    </p>

                </div>

            <?php endif; ?>

        </section>

        <footer class="page-footer">
            © 2026 Sistem Pelanggaran - SMK Muhammadiyah
        </footer>

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

    const tabel = $('#tabelRiwayat');

    if (tabel.length) {

        tabel.DataTable({

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [],

            autoWidth: false,

            language: {

                search: 'Cari:',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat',

                infoEmpty: 'Tidak ada data riwayat',

                infoFiltered: '(disaring dari _MAX_ data)',

                zeroRecords: 'Data riwayat tidak ditemukan',

                emptyTable: 'Belum ada riwayat pelanggaran.',

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
