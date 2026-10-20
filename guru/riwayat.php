<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'guru') {
    exit("Anda tidak memiliki akses");
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil data guru yang sedang login.
$stmt_guru = mysqli_prepare($koneksi, "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1");
if (!$stmt_guru) {
    exit("Terjadi kesalahan saat menyiapkan data guru.");
}
mysqli_stmt_bind_param($stmt_guru, "i", $user_id);
mysqli_stmt_execute($stmt_guru);
$result_guru = mysqli_stmt_get_result($stmt_guru);
$data_guru = mysqli_fetch_assoc($result_guru);
mysqli_stmt_close($stmt_guru);

$guru_id = (int) ($data_guru['id'] ?? 0);
$nama_guru = $data_guru['nama'] ?? ($_SESSION['nama'] ?? 'Guru');

// Riwayat hanya menampilkan pelanggaran yang dicatat oleh guru yang login.
$query = false;
if ($guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, tanggal, nama_siswa, nama_kelas, nama_pelanggaran,
                poin, keterangan, tindakan, status
         FROM t_pelanggaran_siswa
         WHERE guru_id = ?
         ORDER BY tanggal DESC, id DESC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $guru_id);
        mysqli_stmt_execute($stmt);
        $query = mysqli_stmt_get_result($stmt);
    }
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
            --page: #f6f9fd;
            --white: #fff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * { box-sizing: border-box; }
        html { min-height: 100%; }
        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
        }
        a { text-decoration: none; }
        .layout { min-height: 100vh; }

        /* Sidebar dipertahankan dengan ukuran dan gaya semula. */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 242px;
            height: 100vh;
            overflow-y: auto;
            background: var(--white);
            border-right: 1px solid var(--line);
            padding: 25px 12px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .brand {
            padding: 0 13px 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 22px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }
        .brand-subtitle { font-size: 12px; color: var(--muted); }
        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 13px 10px;
        }
        .menu { display: flex; flex-direction: column; gap: 3px; }
        .menu-link {
            min-height: 39px;
            display: flex;
            align-items: center;
            padding: 10px 13px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
            transition: .2s ease;
        }
        .menu-link:hover { background: var(--blue-soft); color: var(--blue); }
        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }
        .logout { margin-top: auto; padding-top: 14px; border-top: 1px solid var(--line); }
        .logout .menu-link { color: #d9534f; }
        .logout .menu-link:hover { background: #fff0ef; color: #c03935; }

        .main { margin-left: 242px; min-height: 100vh; padding: 24px 24px 40px; }
        .container-page { width: 100%; max-width: none; margin: 0; padding: 0; }
        .judul, h4 { color: #234e78; }
        .card {
            background: #fff;
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05);
        }
        .card-body { padding: 24px; }
        .keterangan { color: #71849a; font-size: 13px; }
        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
            border-radius: 7px;
            padding: 8px 15px;
        }
        .btn-secondary {
            --bs-btn-bg: #e8f0f8;
            --bs-btn-border-color: #d8e5f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
            border-radius: 7px;
        }
        .table { color: #344b63; vertical-align: middle; margin-bottom: 0; }
        .table thead th {
            background: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
        }
        .table tbody td { border-color: #e5edf5; padding: 11px 12px; }
        .table-hover tbody tr:hover > * { --bs-table-bg-state: #f4f8fc; }
        .badge-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-diproses { color: #946b16; background: #fff3cd; }
        .status-ditindaklanjuti { color: #245c9b; background: #e4efff; }
        .status-selesai { color: #34734b; background: #e4f3e9; }
        .status-lain { color: #5e7695; background: #edf2f7; }

        .dt-container .dt-layout-row { margin: 12px 0; }
        .dt-container .dt-layout-row:first-child { margin-top: 22px; margin-bottom: 8px; }
        .dt-container .dt-layout-cell { display: flex; align-items: center; }
        .dt-container .dt-length, .dt-container .dt-search { display: flex; align-items: center; gap: 8px; }
        .dt-container .dt-length label, .dt-container .dt-search label {
            display: flex; align-items: center; gap: 8px; margin: 0; font-size: 13px; white-space: nowrap;
        }
        .dt-container .dt-length select {
            width: 58px !important; min-width: 58px; height: 38px; padding: 6px 5px;
            border: 1px solid #d7e3ef; border-radius: 7px; background: #fff; color: #344b63;
        }
        .dt-container .dt-search input {
            width: 190px; height: 38px; padding: 7px 12px; border: 1px solid #d7e3ef;
            border-radius: 7px; margin-left: 0 !important;
        }
        .dt-container .dt-length select:focus, .dt-container .dt-search input:focus {
            outline: none; border-color: #8eb5d8; box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }
        .dt-container .dt-layout-table { margin-top: 8px; }
        .dt-container .dt-paging .dt-paging-button { border-radius: 6px !important; }

        @media (max-width: 1000px) {
            .sidebar { width: 220px; }
            .main { margin-left: 220px; padding: 20px; }
        }
        @media (max-width: 700px) {
            .sidebar { position: relative; width: 100%; height: auto; min-height: auto; bottom: auto; }
            .main { margin-left: 0; padding: 18px 14px 30px; }
            .logout { margin-top: 14px; }
            .card-body { padding: 17px; }
            .dt-container .dt-layout-row { gap: 12px; }
            .dt-container .dt-layout-cell { justify-content: flex-start; width: 100%; }
            .dt-container .dt-search input { width: 160px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">SMK Muhammadiyah</div>
        </div>

        <div class="menu-title">Menu Utama</div>
        <nav class="menu">
            <a href="../dashboard.php" class="menu-link">Dashboard</a>
            <a href="catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
            <a href="tindakan.php" class="menu-link">Tindakan</a>
            <a href="riwayat.php" class="menu-link active" aria-current="page">Riwayat</a>
            <a href="rekap_poin.php" class="menu-link">Rekap Poin</a>
            <a href="../about.php" class="menu-link">About Me</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="menu-link">Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="container-page">
            <div class="mb-3">
                <a href="../dashboard.php" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="judul mb-1">Riwayat Pelanggaran</h2>
                    <p class="text-muted mb-0">Riwayat pelanggaran siswa yang telah dicatat oleh <?= e($nama_guru); ?>.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-1">Data Riwayat Pelanggaran</h4>
                    <p class="keterangan mb-3">Daftar pelanggaran yang telah dicatat.</p>

                    <div class="table-responsive">
                        <table id="tabelRiwayat" class="table table-bordered table-hover align-middle w-100">
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
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                    <?php
                                        $status = trim((string)($data['status'] ?? ''));
                                        $status_class = 'status-lain';
                                        if (strcasecmp($status, 'Diproses') === 0) {
                                            $status_class = 'status-diproses';
                                        } elseif (strcasecmp($status, 'Ditindaklanjuti') === 0) {
                                            $status_class = 'status-ditindaklanjuti';
                                        } elseif (strcasecmp($status, 'Selesai') === 0) {
                                            $status_class = 'status-selesai';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= e($data['tanggal']); ?></td>
                                        <td><?= e($data['nama_siswa']); ?></td>
                                        <td><?= e($data['nama_kelas']); ?></td>
                                        <td><?= e($data['nama_pelanggaran']); ?></td>
                                        <td><?= (int)($data['poin'] ?? 0); ?></td>
                                        <td><?= e(($data['keterangan'] ?? '') ?: '-'); ?></td>
                                        <td><?= e(($data['tindakan'] ?? '') ?: '-'); ?></td>
                                        <td><span class="badge-status <?= e($status_class); ?>"><?= e($status ?: 'Belum ditentukan'); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada riwayat pelanggaran.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#tabelRiwayat').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        scrollX: true,
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat',
            infoEmpty: 'Tidak ada riwayat pelanggaran',
            infoFiltered: '(difilter dari _MAX_ data)',
            zeroRecords: 'Riwayat pelanggaran tidak ditemukan',
            emptyTable: 'Belum ada riwayat pelanggaran',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya',
                first: 'Pertama',
                last: 'Terakhir'
            }
        }
    });
});
</script>
</body>
</html>
