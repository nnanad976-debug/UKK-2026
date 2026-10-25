```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

// Hak akses Guru
if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

// Fungsi keamanan output
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Mengambil identitas Guru yang sedang login
$user_id = (int) ($_SESSION['user_id'] ?? 0);

$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
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

$guru_id = (int) $data_guru['id'];
$nama_guru = $data_guru['nama'];

// Filter siswa dari halaman Rekap Poin
$filter_nama = trim($_GET['nama_siswa'] ?? '');
$filter_kelas = trim($_GET['nama_kelas'] ?? '');

$daftar_riwayat = [];
$pesan_error = '';

// Query riwayat milik guru yang sedang login
$sql = "
    SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        keterangan,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    WHERE guru_id = ?
      AND status IN ('Ditindaklanjuti', 'Selesai')
";

if ($filter_nama !== '') {
    $sql .= " AND nama_siswa = ?";

    if ($filter_kelas !== '') {
        $sql .= " AND nama_kelas = ?";
    }
}

$sql .= " ORDER BY tanggal DESC, id DESC";

$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    if ($filter_nama !== '' && $filter_kelas !== '') {
        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $guru_id,
            $filter_nama,
            $filter_kelas
        );
    } elseif ($filter_nama !== '') {
        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $guru_id,
            $filter_nama
        );
    } else {
        mysqli_stmt_bind_param($stmt, "i", $guru_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $hasil = mysqli_stmt_get_result($stmt);

        while ($data = mysqli_fetch_assoc($hasil)) {
            $daftar_riwayat[] = $data;
        }
    } else {
        $pesan_error = "Data riwayat gagal dimuat.";
    }

    mysqli_stmt_close($stmt);
} else {
    $pesan_error = "Data riwayat gagal diproses.";
}

function formatTanggal($tanggal): string {
    if (empty($tanggal)) {
        return '-';
    }

    $timestamp = strtotime($tanggal);

    if ($timestamp === false) {
        return e($tanggal);
    }

    return date('d-m-Y', $timestamp);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pelanggaran - SIKET</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css"
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
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
        }

        .sidebar {
            width: 242px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 12px 16px;
            background: #fff;
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .brand {
            padding: 0 12px 24px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 20px;
        }

        .brand h5 {
            color: var(--blue-dark);
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .brand p {
            color: var(--muted);
            font-size: 12px;
            margin: 0;
        }

        .menu-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 12px;
            margin-bottom: 5px;
            border-radius: 8px;
            color: #536b89;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .sidebar .nav-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .sidebar .nav-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 700;
        }

        .sidebar-bottom {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding-top: 12px;
        }

        .sidebar .nav-link.logout {
            color: #d9534f;
        }

        .sidebar .nav-link.logout:hover {
            background: #fff0ef;
        }

        .main {
            width: calc(100% - 242px);
            min-height: 100vh;
            margin-left: 242px;
            padding: 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .judul {
            color: var(--blue-dark);
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .subjudul {
            color: var(--muted);
            font-size: 14px;
            margin: 0;
        }

        .btn-primary {
            background: var(--blue);
            border-color: var(--blue);
        }

        .btn-primary:hover {
            background: #1d65b2;
            border-color: #1d65b2;
        }

        .btn-outline-primary {
            color: var(--blue);
            border-color: var(--blue);
        }

        .btn-outline-primary:hover {
            background: var(--blue);
            border-color: var(--blue);
        }

        .card-data {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(32, 62, 104, .04);
        }

        .filter-info {
            background: var(--blue-soft);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 13px 15px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .table thead th {
            background: #f1f6fd;
            color: var(--blue-dark);
            white-space: nowrap;
            font-size: 13px;
            vertical-align: middle;
        }

        .table tbody td {
            color: #435b79;
            font-size: 13px;
            vertical-align: middle;
        }

        .badge-poin {
            display: inline-block;
            min-width: 38px;
            padding: 6px 9px;
            background: var(--blue-soft);
            color: var(--blue);
            border-radius: 6px;
            font-weight: 700;
        }

        .status-selesai {
            display: inline-block;
            background: #e6f7ec;
            color: #21834a;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-ditindaklanjuti {
            display: inline-block;
            background: #fff3d8;
            color: #946200;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .empty-state {
            text-align: center;
            padding: 35px 15px;
            color: var(--muted);
        }

        .empty-state h5 {
            color: var(--blue-dark);
            font-size: 17px;
            margin-bottom: 8px;
        }

        .footer-note {
            color: var(--muted);
            font-size: 12px;
            margin-top: 18px;
        }

        @media (max-width: 700px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .sidebar-bottom {
                margin-top: 12px;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 16px;
            }

            .card-data {
                padding: 14px;
            }

            .judul {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR GURU -->
<aside class="sidebar">
    <div class="brand">
        <h5>Sistem Pelanggaran</h5>
        <p>SMK Muhammadiyah</p>
    </div>

    <div class="menu-label">MENU GURU</div>

    <nav>
        <a href="../dashboard.php" class="nav-link">
            Dashboard
        </a>

        <a href="catat_pelanggaran.php" class="nav-link">
            Catat Pelanggaran
        </a>

        <a href="tindakan.php" class="nav-link">
            Tindakan
        </a>

        <a href="riwayat.php" class="nav-link active">
            Riwayat
        </a>

        <a href="rekap_poin.php" class="nav-link">
            Rekap Poin
        </a>

        <a href="../about.php" class="nav-link">
            About Me
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="../logout.php" class="nav-link logout"
           onclick="return confirm('Apakah kamu yakin ingin keluar?')">
            Logout
        </a>
    </div>
</aside>

<!-- KONTEN UTAMA -->
<main class="main">

    <div class="page-header">
        <div>
            <?php if ($filter_nama !== ''): ?>
                <h2 class="judul">Riwayat Pelanggaran Siswa</h2>
                <p class="subjudul">
                    Riwayat pelanggaran untuk siswa yang dipilih.
                </p>
            <?php else: ?>
                <h2 class="judul">Riwayat Pelanggaran</h2>
                <p class="subjudul">
                    Daftar pelanggaran yang telah ditindaklanjuti atau diselesaikan.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($filter_nama !== ''): ?>
            <a href="riwayat.php" class="btn btn-outline-primary">
                Tampilkan Semua Riwayat
            </a>
        <?php endif; ?>
    </div>

    <?php if ($filter_nama !== ''): ?>
        <div class="filter-info">
            <strong>Siswa:</strong> <?= e($filter_nama) ?>

            <?php if ($filter_kelas !== ''): ?>
                <span class="mx-2">|</span>
                <strong>Kelas:</strong> <?= e($filter_kelas) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($pesan_error !== ''): ?>
        <div class="alert alert-danger">
            <?= e($pesan_error) ?>
        </div>
    <?php else: ?>

        <section class="card-data">
            <div class="table-responsive">
                <table id="tabelRiwayat"
                       class="table table-bordered table-hover align-middle w-100">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($daftar_riwayat as $index => $data): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?= e(formatTanggal($data['tanggal'])) ?>
                                </td>

                                <td>
                                    <?= e($data['nama_siswa']) ?>
                                </td>

                                <td>
                                    <?= e($data['nama_kelas']) ?>
                                </td>

                                <td>
                                    <?= e($data['nama_pelanggaran']) ?>
                                </td>

                                <td>
                                    <span class="badge-poin">
                                        <?= (int) $data['poin'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?= trim((string)($data['keterangan'] ?? '')) !== ''
                                        ? e($data['keterangan'])
                                        : '-' ?>
                                </td>

                                <td>
                                    <?= trim((string)($data['tindakan'] ?? '')) !== ''
                                        ? e($data['tindakan'])
                                        : '-' ?>
                                </td>

                                <td>
                                    <?php if (($data['status'] ?? '') === 'Selesai'): ?>
                                        <span class="status-selesai">
                                            Selesai
                                        </span>
                                    <?php else: ?>
                                        <span class="status-ditindaklanjuti">
                                            <?= e($data['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <?php if (count($daftar_riwayat) === 0): ?>
                <div class="empty-state">
                    <h5>Belum Ada Riwayat Pelanggaran</h5>

                    <?php if ($filter_nama !== ''): ?>
                        <p class="mb-3">
                            Belum ada riwayat berstatus Ditindaklanjuti
                            atau Selesai untuk siswa ini.
                        </p>

                        <a href="riwayat.php" class="btn btn-sm btn-outline-primary">
                            Lihat Semua Riwayat
                        </a>
                    <?php else: ?>
                        <p class="mb-0">
                            Data akan ditampilkan setelah pelanggaran
                            ditindaklanjuti atau diselesaikan.
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

    <?php endif; ?>

    <p class="footer-note">
        SIKET &mdash; Sistem Informasi Ketertiban Siswa
    </p>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        const tabel = $('#tabelRiwayat');

        if (tabel.length && tabel.find('tbody tr').length > 0) {
            new DataTable('#tabelRiwayat', {
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data yang ditampilkan',
                    zeroRecords: 'Data yang dicari tidak ditemukan',
                    emptyTable: 'Belum ada data riwayat',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        }
    });
</script>

</body>
</html>
```