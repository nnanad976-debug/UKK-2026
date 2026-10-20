
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    exit("Anda tidak memiliki akses");
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$pesan = '';
$jenis_pesan = '';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* =========================
   DATA GURU
========================= */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$guru_id = (int)($data_guru['id'] ?? 0);

/* =========================
   SIMPAN TINDAKAN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $status_valid = ['Diproses', 'Ditindaklanjuti', 'Selesai'];

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
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_siswa
             SET tindakan = ?, status = ?
             WHERE id = ? AND guru_id = ?"
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
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_pelanggaran_siswa
                 WHERE id = ? AND guru_id = ? LIMIT 1"
            );

            mysqli_stmt_bind_param($stmt, "ii", $id, $guru_id);
            mysqli_stmt_execute($stmt);
            $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($cek) {
                header("Location: tindakan.php?berhasil=1");
                exit;
            }
        }

        $pesan = 'Data pelanggaran tidak ditemukan atau gagal disimpan.';
        $jenis_pesan = 'danger';
    }
}

if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Tindakan dan status berhasil disimpan!';
    $jenis_pesan = 'success';
}

/* =========================
   DATA PELANGGARAN
========================= */
$daftar_pelanggaran = [];

if ($guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, tanggal, nama_siswa, nama_kelas,
                nama_pelanggaran, poin, keterangan, tindakan, status
         FROM t_pelanggaran_siswa
         WHERE guru_id = ?
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
            --page: #f5f8fc;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce7f4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 242px;
            height: 100vh;
            padding: 24px 12px 14px;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-right: 1px solid var(--line);
            z-index: 1000;
            overflow-y: auto;
        }

        .brand {
            padding: 0 13px 19px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            margin-bottom: 5px;
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
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
            gap: 4px;
        }

        .menu-link {
            min-height: 39px;
            display: flex;
            align-items: center;
            padding: 10px 13px;
            border-radius: 9px;
            color: #5e7695;
            font-size: 12px;
            transition: background .2s, color .2s;
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
            padding-top: 12px;
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
            min-height: 100vh;
            margin-left: 242px;
            padding: 22px 24px 38px;
        }

        .page-container {
            width: 100%;
            max-width: none;
            margin: 0 auto;
        }

        .judul {
            color: #234e78;
            font-size: 28px;
            font-weight: 600;
        }

        .subjudul {
            color: #71849a;
            font-size: 12px;
        }

        .card {
            overflow: hidden;
            border: 1px solid #e0e9f4;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .045);
        }

        .card-body {
            padding: 21px;
        }

        .section-title {
            margin-bottom: 5px;
            color: #234e78;
            font-size: 20px;
            font-weight: 600;
        }

        .section-description {
            margin-bottom: 0;
            color: var(--muted);
            font-size: 12px;
        }

        /* TOMBOL */
        .btn {
            border-radius: 7px;
        }

        .btn-primary {
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-bg: #477ba6;
            --bs-btn-hover-border-color: #477ba6;
            --bs-btn-active-bg: #3f7099;
            --bs-btn-active-border-color: #3f7099;
        }

        .btn-secondary {
            --bs-btn-bg: #eaf2fa;
            --bs-btn-border-color: #dce7f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-border-color: #cbddeb;
            --bs-btn-hover-color: #234e78;
        }

        .back-button {
            margin-bottom: 14px;
        }

        /* TABEL */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 7px;
        }

        #tabelTindakan {
            width: 100% !important;
            min-width: 1250px;
            margin-bottom: 0 !important;
            color: #344b63;
            font-size: 12px;
            vertical-align: middle;
        }

        #tabelTindakan thead th {
            padding: 12px 10px;
            background: #edf4fb;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            white-space: nowrap;
        }

        #tabelTindakan tbody td {
            padding: 10px;
            border-color: #e2eaf3;
            vertical-align: middle;
        }

        #tabelTindakan tbody tr:hover > * {
            background-color: #f5f9fe;
        }

        #tabelTindakan th:first-child,
        #tabelTindakan td:first-child {
            min-width: 48px;
            text-align: center;
        }

        #tabelTindakan th:nth-child(6),
        #tabelTindakan td:nth-child(6) {
            min-width: 60px;
            text-align: center;
        }

        #tabelTindakan th:last-child,
        #tabelTindakan td:last-child {
            width: 100px;
            min-width: 100px;
            text-align: center;
            white-space: nowrap;
        }

        #tabelTindakan .btn {
            display: inline-block;
            min-width: 64px;
            padding: 5px 9px;
            font-size: 12px;
            visibility: visible;
            opacity: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
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
        }

        .dt-container .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
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
            font-size: 12px;
            white-space: nowrap;
        }

        .dt-container .dt-length select,
        .dt-container .dt-search input {
            height: 36px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            font-size: 12px;
        }

        .dt-container .dt-length select {
            width: 58px;
            padding: 5px;
        }

        .dt-container .dt-search input {
            width: 160px;
            margin-left: 0 !important;
            padding: 7px 10px;
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 5px !important;
            font-size: 12px;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            background: var(--blue) !important;
            border-color: var(--blue) !important;
            color: #fff !important;
        }

        .dt-container .dt-info {
            color: #526b86;
            font-size: 12px;
        }

        /* FORM MODAL */
        .modal-content {
            overflow: hidden;
            border: 1px solid #e1eaf4;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(35, 78, 120, .12);
        }

        .modal-header {
            background: #f5f9fe;
            border-bottom-color: #e1eaf4;
        }

        .modal-title {
            color: #234e78;
            font-weight: 600;
        }

        .modal-footer {
            border-top-color: #e1eaf4;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            font-size: 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        .alert {
            border-radius: 8px;
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
                margin-left: 0;
                padding: 18px 14px 28px;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 16px;
            }

            .judul {
                font-size: 23px;
            }

            .dt-container .dt-layout-row {
                flex-wrap: wrap;
            }

            .dt-container .dt-search input {
                width: 130px;
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
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
        <a href="tindakan.php" class="menu-link active">Tindakan</a>
        <a href="riwayat.php" class="menu-link">Riwayat</a>
        <a href="rekap_poin.php" class="menu-link">Rekap Poin</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<!-- KONTEN UTAMA -->
<main class="main">
    <div class="page-container">

        <div class="back-button">
            <a href="../dashboard.php" class="btn btn-secondary btn-sm px-3 py-2">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <!-- JUDUL HALAMAN -->
        <section class="card mb-4">
            <div class="card-body">
                <h1 class="judul mb-1">Tindakan Pelanggaran</h1>
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
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <!-- TABEL DATA PELANGGARAN -->
        <section class="card">
            <div class="card-body">
                <h2 class="section-title">Data Pelanggaran</h2>
                <p class="section-description mb-3">
                    Daftar pelanggaran yang telah dicatat oleh Anda.
                </p>

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
                                    $status_data = $data['status'] ?? 'Diproses';

                                    $class_status = 'status-diproses';

                                    if ($status_data === 'Ditindaklanjuti') {
                                        $class_status = 'status-ditindaklanjuti';
                                    } elseif ($status_data === 'Selesai') {
                                        $class_status = 'status-selesai';
                                    }
                                ?>

                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= e($data['tanggal']); ?></td>
                                    <td><?= e($data['nama_siswa']); ?></td>
                                    <td><?= e($data['nama_kelas'] ?: '-'); ?></td>
                                    <td><?= e($data['nama_pelanggaran']); ?></td>
                                    <td><?= (int)$data['poin']; ?></td>
                                    <td><?= e($data['keterangan'] ?: '-'); ?></td>
                                    <td><?= e(trim((string)($data['tindakan'] ?? '')) ?: '-'); ?></td>

                                    <td>
                                        <span class="status-badge <?= e($class_status); ?>">
                                            <?= e($status_data); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTindakan<?= $id_data; ?>">
                                            Tindakan
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($daftar_pelanggaran)): ?>
                    <div class="text-center text-muted py-4">
                        Belum ada data pelanggaran yang dicatat.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<!-- MODAL TINDAKAN UNTUK SETIAP DATA -->
<?php foreach ($daftar_pelanggaran as $data): ?>
    <?php
        $id_data = (int)$data['id'];
        $status_data = $data['status'] ?? 'Diproses';
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
                        <h5 class="modal-title" id="judulModal<?= $id_data; ?>">
                            Tindakan Pelanggaran
                        </h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $id_data; ?>">

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control"
                                   value="<?= e($data['tanggal']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control"
                                   value="<?= e($data['nama_siswa']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control"
                                   value="<?= e($data['nama_kelas'] ?: '-'); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pelanggaran</label>
                            <input type="text" class="form-control"
                                   value="<?= e($data['nama_pelanggaran']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="tindakan<?= $id_data; ?>">
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
                            <label class="form-label" for="status<?= $id_data; ?>">
                                Status
                            </label>

                            <select id="status<?= $id_data; ?>"
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

                                <option value="Selesai"
                                    <?= $status_data === 'Selesai' ? 'selected' : ''; ?>>
                                    Selesai
                                </option>
                            </select>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    $('#tabelTindakan').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [],
        autoWidth: false,
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
});
</script>

</body>
</html>
