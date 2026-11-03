
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

$user_id = (int)($_SESSION['user_id'] ?? 0);

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil data guru yang sedang login
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$guru_id = (int)($data_guru['id'] ?? 0);

// Ambil riwayat pelanggaran berdasarkan guru
$daftar_riwayat = [];

if ($guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, tanggal, nama_siswa, nama_kelas,
                nama_pelanggaran, poin, keterangan,
                tindakan, status
         FROM t_pelanggaran_siswa
         WHERE guru_id = ?
         ORDER BY tanggal DESC, id DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $guru_id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);

    while ($data = mysqli_fetch_assoc($hasil)) {
        $daftar_riwayat[] = $data;
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pelanggaran</title>

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

        /* SIDEBAR - SAMA DENGAN HALAMAN TINDAKAN */
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

        /* TABEL */
        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 8px;
            -webkit-overflow-scrolling: touch;
        }

        #tabelRiwayat {
            width: 100% !important;
            min-width: 1050px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 14px;
            vertical-align: middle;
        }

        #tabelRiwayat thead th {
            padding: 14px 12px;
            background: #edf4fb;
            color: #315b82;
            border-color: #dce7f1;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelRiwayat tbody td {
            padding: 13px 11px;
            border-color: #e2eaf3;
            vertical-align: middle;
            line-height: 1.45;
        }

        #tabelRiwayat tbody tr:hover > * {
            background-color: #f5f9fe;
        }

        #tabelRiwayat th:first-child,
        #tabelRiwayat td:first-child,
        #tabelRiwayat th:nth-child(6),
        #tabelRiwayat td:nth-child(6) {
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

        .status-belum {
            background: #f0f2f5;
            color: #748195;
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

            #tabelRiwayat {
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

        <a href="tindakan.php" class="menu-link">
            Tindakan
        </a>

        <a href="riwayat.php" class="menu-link active">
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

        <section class="card mb-4">
            <div class="card-body">
                <h1 class="judul mb-1">
                    Riwayat Pelanggaran
                </h1>

                <p class="subjudul mb-0">
                    Lihat riwayat pelanggaran siswa yang telah Anda catat.
                </p>
            </div>
        </section>

        <section class="card">
            <div class="card-body">

                <h2 class="section-title">
                    Data Riwayat Pelanggaran
                </h2>

                <p class="section-description">
                    Daftar seluruh pelanggaran yang telah dicatat oleh Anda.
                </p>

                <?php if (!empty($daftar_riwayat)): ?>

                    <div class="table-responsive">
                        <table id="tabelRiwayat"
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
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($daftar_riwayat as $index => $data): ?>

                                    <?php
                                    $status = trim($data['status'] ?? '');
                                    $status_class = 'status-belum';

                                    if ($status === 'Diproses') {
                                        $status_class = 'status-diproses';
                                    } elseif ($status === 'Ditindaklanjuti') {
                                        $status_class = 'status-ditindaklanjuti';
                                    } elseif ($status === 'Selesai') {
                                        $status_class = 'status-selesai';
                                    }

                                    $tanggal = !empty($data['tanggal'])
                                        ? date('d-m-Y', strtotime($data['tanggal']))
                                        : '-';
                                    ?>

                                    <tr>
                                        <td><?= $index + 1; ?></td>

                                        <td><?= e($tanggal); ?></td>

                                        <td><?= e($data['nama_siswa']); ?></td>

                                        <td><?= e($data['nama_kelas'] ?: '-'); ?></td>

                                        <td><?= e($data['nama_pelanggaran']); ?></td>

                                        <td>
                                            <span class="badge-poin">
                                                <?= (int)$data['poin']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= e($data['keterangan'] ?: '-'); ?>
                                        </td>

                                        <td>
                                            <?= e(trim((string)($data['tindakan'] ?? '')) ?: '-'); ?>
                                        </td>

                                        <td>
                                            <span class="status-badge <?= e($status_class); ?>">
                                                <?= e($status ?: 'Belum diproses'); ?>
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
                            Data pelanggaran yang Anda catat akan ditampilkan di sini.
                        </p>
                    </div>

                <?php endif; ?>

            </div>
        </section>

    </div>
</main>

<!-- JAVASCRIPT -->
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
