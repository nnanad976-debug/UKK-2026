
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$pesan = '';
$jenis_pesan = '';
$bukaForm = false;

$urutanJurusan = [
    'TKR' => 1,
    'TSM' => 2,
    'TKJ' => 3,
    'RPL' => 4,
    'BD'  => 5
];

$urutanTingkat = [
    'X'   => 1,
    'XI'  => 2,
    'XII' => 3
];

$batasKelas = [
    'TKR' => ['X' => 5, 'XI' => 4, 'XII' => 5],
    'TSM' => ['X' => 3, 'XI' => 3, 'XII' => 3],
    'TKJ' => ['X' => 4, 'XI' => 4, 'XII' => 4],
    'RPL' => ['X' => 2, 'XI' => 1, 'XII' => 2],
    'BD'  => ['X' => 1, 'XI' => 1, 'XII' => 1]
];

$tingkatInput = (string)($_POST['tingkat'] ?? '');
$jurusanInput = (string)($_POST['jurusan'] ?? '');
$nomorInput = (string)($_POST['nomor'] ?? '');
$statusInput = (string)($_POST['status_aktif'] ?? '1');

if (isset($_GET['berhasil'])) {
    $pesan = 'Data kelas berhasil disimpan!';
    $jenis_pesan = 'success';
} elseif (isset($_GET['berhasil_edit'])) {
    $pesan = 'Data kelas berhasil diperbarui!';
    $jenis_pesan = 'success';
} elseif (isset($_GET['berhasil_hapus'])) {
    $pesan = 'Data kelas berhasil dihapus!';
    $jenis_pesan = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $bukaForm = true;

    $tingkat = trim($tingkatInput);
    $jurusan = trim($jurusanInput);
    $nomor = filter_var($nomorInput, FILTER_VALIDATE_INT);
    $statusAktif = filter_var($statusInput, FILTER_VALIDATE_INT);

    if (
        !isset($urutanTingkat[$tingkat]) ||
        !isset($urutanJurusan[$jurusan]) ||
        $nomor === false ||
        $nomor === null ||
        $nomor < 1 ||
        !isset($batasKelas[$jurusan][$tingkat]) ||
        $nomor > $batasKelas[$jurusan][$tingkat] ||
        !in_array($statusAktif, [0, 1], true)
    ) {
        $pesan = 'Data kelas tidak valid. Periksa tingkat, jurusan, dan nomor kelas.';
        $jenis_pesan = 'danger';
    } else {
        $nama = $tingkat . ' ' . $jurusan . ' ' . $nomor;

        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_kelas WHERE nama = ? LIMIT 1"
        );

        if (!$cek) {
            $pesan = 'Gagal memeriksa data kelas.';
            $jenis_pesan = 'danger';
        } else {
            mysqli_stmt_bind_param($cek, 's', $nama);
            mysqli_stmt_execute($cek);

            $hasilCek = mysqli_stmt_get_result($cek);
            $kelasSudahAda = $hasilCek && mysqli_num_rows($hasilCek) > 0;

            mysqli_stmt_close($cek);

            if ($kelasSudahAda) {
                $pesan = 'Kelas ' . $nama . ' sudah terdaftar.';
                $jenis_pesan = 'danger';
            } else {
                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif)
                     VALUES (?, ?, ?, ?)"
                );

                if (!$stmt) {
                    $pesan = 'Gagal menyiapkan penyimpanan data kelas.';
                    $jenis_pesan = 'danger';
                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        'sssi',
                        $nama,
                        $tingkat,
                        $jurusan,
                        $statusAktif
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        header('Location: kelas.php?berhasil=1');
                        exit;
                    }

                    $pesan = 'Data kelas gagal disimpan. Periksa data dan coba lagi.';
                    $jenis_pesan = 'danger';

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     ORDER BY
        CASE jurusan
            WHEN 'TKR' THEN 1
            WHEN 'TSM' THEN 2
            WHEN 'TKJ' THEN 3
            WHEN 'RPL' THEN 4
            WHEN 'BD' THEN 5
            ELSE 6
        END,
        CASE tingkat
            WHEN 'X' THEN 1
            WHEN 'XI' THEN 2
            WHEN 'XII' THEN 3
            ELSE 4
        END,
        CAST(SUBSTRING_INDEX(nama, ' ', -1) AS UNSIGNED),
        id ASC"
);

if (!$query) {
    die('Gagal mengambil data kelas: ' . e(mysqli_error($koneksi)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Kelas</title>

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

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f7fc;
            color: #2f435a;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR: CSS tetap berada di file kelas.php */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 282px;
            min-width: 282px;
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .brand {
            padding: 0 15px 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 22px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }

        .menu {
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
            font-size: 13px;
            transition: .2s ease;
        }

        .sidebar .menu-link:hover,
        .sidebar .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .sidebar .menu-link.active {
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .sidebar .logout .menu-link {
            color: #d9534f;
        }

        .sidebar .logout .menu-link:hover {
            background: #fff0ef;
            color: #c03935;
        }

        /* KONTEN: mulai setelah sidebar */
        .main {
            width: calc(100% - 282px);
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .main .container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .judul,
        .main h4 {
            color: #234e78;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
        }

        .card-body {
            padding: 24px;
        }

        .form-label {
            color: #405873;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        .btn {
            border-radius: 7px;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
            padding: 8px 15px;
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
        }

        .table {
            color: #344b63;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table thead th {
            background: #eaf2fa;
            color: #315b82;
            border-color: #dce7f1;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
        }

        .table tbody td {
            border-color: #e5edf5;
            padding: 11px 12px;
        }

        .table-hover tbody tr:hover > * {
            --bs-table-accent-bg: #f4f8fc;
        }

        .status-aktif,
        .status-nonaktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
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

        .keterangan {
            color: #71849a;
            font-size: 14px;
        }

        /* DataTables */
        #tabelKelas_wrapper .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 10px 0;
        }

        #tabelKelas_wrapper .dt-layout-row:first-child {
            margin-top: 22px;
            margin-bottom: 8px;
        }

        #tabelKelas_wrapper .dt-layout-cell {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        #tabelKelas_wrapper .dt-length,
        #tabelKelas_wrapper .dt-search {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        #tabelKelas_wrapper .dt-length label,
        #tabelKelas_wrapper .dt-search label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 13px;
            white-space: nowrap;
        }

        #tabelKelas_wrapper .dt-length select {
            width: 65px !important;
            min-width: 65px;
            height: 38px;
            margin: 0 !important;
            padding: 6px 4px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: #344b63;
            font-size: 13px;
        }

        #tabelKelas_wrapper .dt-search input {
            width: 175px;
            height: 38px;
            margin: 0 !important;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            color: #344b63;
            font-size: 13px;
        }

        #tabelKelas_wrapper .dt-length select:focus,
        #tabelKelas_wrapper .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        #tabelKelas_wrapper .dt-layout-table {
            margin-top: 8px;
        }

        #tabelKelas_wrapper .dt-info,
        #tabelKelas_wrapper .dt-paging {
            font-size: 13px;
            color: #5e7695;
        }

        #tabelKelas td:last-child {
            white-space: nowrap;
        }

        /* Tampilan layar kecil */
        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
                height: auto;
                min-height: auto;
                max-height: none;
            }

            .main {
                width: 100%;
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .logout {
                margin-top: 14px;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 17px;
            }

            #tabelKelas_wrapper .dt-layout-row {
                flex-wrap: wrap;
            }

            #tabelKelas_wrapper .dt-search input {
                width: 145px;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar hanya dipanggil satu kali. -->
<aside class="sidebar">
    <?php include "layout/sidebar.php"; ?>
</aside>

<main class="main">
    <div class="container py-2">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <button type="button" class="btn btn-primary" id="btnToggleKelas">
                <?= $bukaForm ? 'Tutup Form' : '+ Tambah Data Kelas'; ?>
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Kelola Kelas</h2>
                <p class="text-muted mb-0">Tambah dan kelola data kelas.</p>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show" role="alert">
                <?= e($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div
            class="card mb-4 <?= $bukaForm ? '' : 'd-none'; ?>"
            id="formTambahKelas"
        >
            <div class="card-body">
                <h4 class="mb-4">Tambah Data Kelas</h4>

                <form method="POST" action="kelas.php" id="formKelas">
                    <div class="mb-3">
                        <label for="tingkat" class="form-label">Tingkat</label>
                        <select id="tingkat" name="tingkat" class="form-select" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <?php foreach (['X', 'XI', 'XII'] as $pilihanTingkat): ?>
                                <option
                                    value="<?= e($pilihanTingkat); ?>"
                                    <?= $tingkatInput === $pilihanTingkat ? 'selected' : ''; ?>
                                >
                                    <?= e($pilihanTingkat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select id="jurusan" name="jurusan" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach (['TKR', 'TSM', 'TKJ', 'RPL', 'BD'] as $pilihanJurusan): ?>
                                <option
                                    value="<?= e($pilihanJurusan); ?>"
                                    <?= $jurusanInput === $pilihanJurusan ? 'selected' : ''; ?>
                                >
                                    <?= e($pilihanJurusan); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nomor" class="form-label">Nomor Kelas</label>
                        <input
                            type="number"
                            id="nomor"
                            name="nomor"
                            class="form-control"
                            min="1"
                            max="5"
                            value="<?= e($nomorInput); ?>"
                            placeholder="Contoh: 1"
                            required
                        >
                        <small class="text-muted" id="keteranganNomor">
                            Nama kelas dibuat otomatis, misalnya X TKR 1.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="status_aktif" class="form-label">Status</label>
                        <select id="status_aktif" name="status_aktif" class="form-select" required>
                            <option value="1" <?= $statusInput === '1' ? 'selected' : ''; ?>>
                                Aktif
                            </option>
                            <option value="0" <?= $statusInput === '0' ? 'selected' : ''; ?>>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <button type="submit" name="simpan" value="1" class="btn btn-primary">
                        Tambah Kelas
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">Data Kelas</h4>
                <p class="keterangan mb-3">
                    Daftar kelas diurutkan berdasarkan jurusan, tingkat, dan nomor kelas.
                </p>

                <div class="table-responsive">
                    <table id="tabelKelas" class="table table-bordered table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($kelas = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= e($kelas['nama'] ?? ''); ?></td>
                                        <td><?= e($kelas['tingkat'] ?? ''); ?></td>
                                        <td><?= e($kelas['jurusan'] ?? ''); ?></td>
                                        <td>
                                            <?php if ((int)($kelas['status_aktif'] ?? 0) === 1): ?>
                                                <span class="status-aktif">Aktif</span>
                                            <?php else: ?>
                                                <span class="status-nonaktif">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a
                                                    href="edit_kelas.php?id=<?= (int)$kelas['id']; ?>"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_kelas.php?id=<?= (int)$kelas['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus kelas ini?')"
                                                >
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada data kelas.
                                    </td>
                                </tr>
                            <?php endif; ?>
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
    const tombol = document.getElementById('btnToggleKelas');
    const form = document.getElementById('formTambahKelas');
    const tingkat = document.getElementById('tingkat');
    const jurusan = document.getElementById('jurusan');
    const nomor = document.getElementById('nomor');
    const keteranganNomor = document.getElementById('keteranganNomor');

    tombol.addEventListener('click', function () {
        const formTertutup = form.classList.contains('d-none');

        if (formTertutup) {
            form.classList.remove('d-none');
            tombol.textContent = 'Tutup Form';
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            form.classList.add('d-none');
            tombol.textContent = '+ Tambah Data Kelas';
        }
    });

    const batas = {
        TKR: {X: 5, XI: 4, XII: 5},
        TSM: {X: 3, XI: 3, XII: 3},
        TKJ: {X: 4, XI: 4, XII: 4},
        RPL: {X: 2, XI: 1, XII: 2},
        BD:  {X: 1, XI: 1, XII: 1}
    };

    function perbaruiBatasNomor() {
        const max = batas[jurusan.value]?.[tingkat.value];

        if (max) {
            nomor.max = max;

            keteranganNomor.textContent =
                'Nomor kelas untuk ' + tingkat.value + ' ' +
                jurusan.value + ' adalah 1 sampai ' + max + '.';

            if (nomor.value !== '' && Number(nomor.value) > max) {
                nomor.value = '';
            }
        } else {
            nomor.max = 5;
            keteranganNomor.textContent =
                'Pilih tingkat dan jurusan terlebih dahulu. Nama kelas dibuat otomatis.';
        }
    }

    tingkat.addEventListener('change', perbaruiBatasNomor);
    jurusan.addEventListener('change', perbaruiBatasNomor);
    perbaruiBatasNomor();

    if (window.jQuery && $.fn.DataTable) {
        $('#tabelKelas').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            ordering: true,
            searching: true,
            paging: true,
            autoWidth: false,
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari nama kelas...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kelas',
                infoEmpty: 'Belum ada data kelas',
                infoFiltered: '(difilter dari _MAX_ total kelas)',
                zeroRecords: 'Data kelas tidak ditemukan',
                emptyTable: 'Belum ada data kelas',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }
            },
            columnDefs: [
                {
                    targets: [0, 5],
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
