```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

$pesan = "";
$jenis_pesan = "";
$bukaForm = false;

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

if (isset($_GET['berhasil'])) {
    $pesan = "Jenis pelanggaran berhasil disimpan.";
    $jenis_pesan = "success";
}

if (isset($_POST['simpan'])) {
    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 1);
    $bukaForm = true;

    if (
        $kategori_id <= 0 ||
        $kode === '' ||
        $nama === '' ||
        $deskripsi === '' ||
        $poin < 0 ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Semua kolom wajib diisi dengan data yang benar.";
        $jenis_pesan = "danger";
    } else {
        $cekKategori = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori
             WHERE id = ? AND status_aktif = 1 LIMIT 1"
        );

        mysqli_stmt_bind_param($cekKategori, "i", $kategori_id);
        mysqli_stmt_execute($cekKategori);
        $hasilKategori = mysqli_stmt_get_result($cekKategori);
        $kategoriValid = mysqli_num_rows($hasilKategori) > 0;
        mysqli_stmt_close($cekKategori);

        if (!$kategoriValid) {
            $pesan = "Kategori tidak ditemukan atau sudah tidak aktif.";
            $jenis_pesan = "danger";
        } else {
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_pelanggaran
                (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "issisi",
                    $kategori_id,
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi,
                    $status_aktif
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: jenis_pelanggaran.php?berhasil=1");
                    exit;
                }

                $pesan = "Data gagal disimpan. Pastikan kode pelanggaran belum digunakan.";
                $jenis_pesan = "danger";
                mysqli_stmt_close($stmt);
            } else {
                $pesan = "Data gagal disimpan. Periksa struktur database.";
                $jenis_pesan = "danger";
            }
        }
    }
}

$query_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/* Menampilkan satu data untuk kombinasi kategori, nama, dan poin
   yang sama. Data duplikat asli tidak dihapus dari database. */
$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        k.nama AS nama_kategori
     FROM t_pelanggaran p
     INNER JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
     WHERE p.id = (
        SELECT MIN(p2.id)
        FROM t_pelanggaran p2
        WHERE p2.nama = p.nama
          AND p2.poin = p.poin
          AND p2.pelanggaran_kategori_id = p.pelanggaran_kategori_id
     )
     ORDER BY p.id DESC"
);

if (!$query) {
    die("Gagal mengambil data jenis pelanggaran. Periksa struktur tabel database.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Jenis Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --white: #fff;
            --text: #2f435a;
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
            font-family: "Segoe UI", Arial, sans-serif;
        }

        a {
            text-decoration: none;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
            min-width: 282px;
            height: 100vh;
            overflow-y: auto;
            background: var(--white);
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

        .menu-link {
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
            transition: .2s ease;
        }

        .menu-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
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

        .main {
            margin-left: 282px;
            min-height: 100vh;
            padding: 24px 28px 40px;
        }

        .container {
            width: 100%;
            max-width: none !important;
            margin: 0 auto;
            padding: 0 !important;
        }

        .judul, h4 {
            color: #234e78;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35,78,120,.05) !important;
        }

        .card-body {
            padding: 22px;
        }

        .form-label {
            color: #405873;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control, .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87,143,190,.14);
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

        .status-aktif, .status-nonaktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
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

        /* Tampilan kontrol DataTables */
        .dt-container .dt-layout-row {
            margin: 12px 0;
        }

        .dt-container .dt-layout-row:first-child {
            margin-top: 22px;
            margin-bottom: 8px;
        }

        .dt-container .dt-layout-cell {
            display: flex;
            align-items: center;
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
            font-size: 13px;
            white-space: nowrap;
        }

        .dt-container .dt-length select {
            width: 65px !important;
            min-width: 65px;
            height: 38px;
            padding: 6px 5px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: #344b63;
        }

        .dt-container .dt-search input {
            width: 190px;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            margin-left: 0 !important;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87,143,190,.14);
        }

        .dt-container .dt-layout-table {
            margin-top: 8px;
        }

        .dt-container .dt-info {
            font-size: 13px;
            color: #71849a;
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 6px !important;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            background: #eaf3ff !important;
            border-color: #dce9f8 !important;
            color: #2878cf !important;
        }

        .table-responsive {
            width: 100%;
        }

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 100%;
                height: auto;
                min-height: auto;
            }

            .main {
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

            .dt-container .dt-search input {
                width: 140px;
            }

            .dt-container .dt-layout-row {
                gap: 12px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru.php" class="menu-link">Kelola Guru</a>
        <a href="siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link active">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">
    <div class="container py-2">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <button
                type="button"
                class="btn btn-primary"
                id="tombolTambah"
                data-bs-toggle="collapse"
                data-bs-target="#formTambahJenis"
                aria-expanded="<?= $bukaForm ? 'true' : 'false'; ?>"
                aria-controls="formTambahJenis">
                + Tambah Jenis Pelanggaran
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Kelola Jenis Pelanggaran</h2>
                <p class="text-muted mb-0">
                    Tambah dan kelola data jenis pelanggaran siswa.
                </p>
            </div>
        </div>

        <?php if ($pesan !== '' && !$bukaForm): ?>
            <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show" role="alert">
                <?= e($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div class="collapse <?= $bukaForm ? 'show' : ''; ?>" id="formTambahJenis">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-4">Tambah Jenis Pelanggaran</h4>

                    <?php if ($pesan !== '' && $bukaForm): ?>
                        <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show" role="alert">
                            <?= e($pesan); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="jenis_pelanggaran.php" autocomplete="off">

                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori Pelanggaran</label>
                            <select name="pelanggaran_kategori_id" id="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php if ($query_kategori): ?>
                                    <?php while ($kategori = mysqli_fetch_assoc($query_kategori)): ?>
                                        <option
                                            value="<?= (int)$kategori['id']; ?>"
                                            <?= ((string)($_POST['pelanggaran_kategori_id'] ?? '') === (string)$kategori['id']) ? 'selected' : ''; ?>>
                                            <?= e($kategori['nama']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode" class="form-label">Kode Pelanggaran</label>
                                <input
                                    type="text"
                                    id="kode"
                                    name="kode"
                                    class="form-control"
                                    placeholder="Contoh: PLG-051"
                                    value="<?= e($_POST['kode'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Pelanggaran</label>
                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    class="form-control"
                                    placeholder="Contoh: Datang terlambat"
                                    value="<?= e($_POST['nama'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="poin" class="form-label">Poin</label>
                                <input
                                    type="number"
                                    id="poin"
                                    name="poin"
                                    class="form-control"
                                    min="0"
                                    value="<?= e($_POST['poin'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status_aktif" class="form-label">Status</label>
                                <select id="status_aktif" name="status_aktif" class="form-select" required>
                                    <option value="1" <?= (($_POST['status_aktif'] ?? '1') === '1') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="0" <?= (($_POST['status_aktif'] ?? '') === '0') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                class="form-control"
                                rows="3"
                                required><?= e($_POST['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary">
                            Tambah Jenis Pelanggaran
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#formTambahJenis">
                            Tutup Form
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">Data Jenis Pelanggaran</h4>
                <p class="keterangan mb-3">
                    Daftar jenis pelanggaran beserta kategori, poin, dan statusnya.
                </p>

                <div class="table-responsive">
                    <table id="tabelPelanggaran" class="table table-bordered table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th style="width:60px">No</th>
                                <th>Kode</th>
                                <th>Kategori</th>
                                <th>Nama Pelanggaran</th>
                                <th>Poin</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= e($data['kode']); ?></td>
                                        <td><?= e($data['nama_kategori']); ?></td>
                                        <td><?= e($data['nama']); ?></td>
                                        <td><?= (int)$data['poin']; ?></td>
                                        <td><?= e($data['deskripsi']); ?></td>

                                        <td>
                                            <?php if ((int)$data['status_aktif'] === 1): ?>
                                                <span class="status-aktif">Aktif</span>
                                            <?php else: ?>
                                                <span class="status-nonaktif">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a
                                                    href="edit_jenis_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_jenis_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data jenis pelanggaran.
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
$(document).ready(function () {
    const tabel = document.getElementById('tabelPelanggaran');
    const jumlahBaris = tabel.querySelectorAll('tbody tr').length;
    const dataKosong = jumlahBaris === 1 &&
        tabel.querySelector('tbody td[colspan="8"]') !== null;

    if (!dataKosong) {
        $('#tabelPelanggaran').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            ordering: true,
            searching: true,
            paging: true,
            autoWidth: false,
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari jenis pelanggaran...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ jenis pelanggaran',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Jenis pelanggaran tidak ditemukan',
                emptyTable: 'Belum ada data jenis pelanggaran',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }
            },
            columnDefs: [
                {
                    targets: [0, 7],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    const formTambah = document.getElementById('formTambahJenis');
    const tombolTambah = document.getElementById('tombolTambah');

    formTambah.addEventListener('shown.bs.collapse', function () {
        tombolTambah.textContent = '− Tutup Form Tambah';
        formTambah.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });

    formTambah.addEventListener('hidden.bs.collapse', function () {
        tombolTambah.textContent = '+ Tambah Jenis Pelanggaran';
    });

    if (formTambah.classList.contains('show')) {
        tombolTambah.textContent = '− Tutup Form Tambah';
    }
});
</script>

</body>
</html>
```