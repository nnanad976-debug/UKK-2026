
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

$pesan = '';
$jenis_pesan = 'success';
$bukaForm = false;

// Pesan setelah berhasil menambahkan kategori.
if (isset($_GET['status']) && $_GET['status'] === 'sukses') {
    $pesan = 'Kategori pelanggaran berhasil ditambahkan.';
}

// Proses tambah kategori.
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $statusAktif = (int)($_POST['status_aktif'] ?? 1);

    $bukaForm = true;

    if ($nama === '' || $deskripsi === '') {
        $pesan = 'Nama kategori dan deskripsi wajib diisi.';
        $jenis_pesan = 'danger';

    } elseif (!in_array($statusAktif, [0, 1], true)) {
        $pesan = 'Status kategori tidak valid.';
        $jenis_pesan = 'danger';

    } else {
        // Mencegah nama kategori yang sama.
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_pelanggaran_kategori
             WHERE LOWER(TRIM(nama)) = LOWER(TRIM(?))
             LIMIT 1"
        );

        if (!$cek) {
            $pesan = 'Gagal memeriksa nama kategori.';
            $jenis_pesan = 'danger';

        } else {
            mysqli_stmt_bind_param($cek, 's', $nama);
            mysqli_stmt_execute($cek);

            $hasilCek = mysqli_stmt_get_result($cek);
            $sudahAda = mysqli_num_rows($hasilCek) > 0;

            mysqli_stmt_close($cek);

            if ($sudahAda) {
                $pesan = 'Nama kategori tersebut sudah terdaftar.';
                $jenis_pesan = 'danger';

            } else {
                // Tidak ada batas maksimal kategori.
                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO t_pelanggaran_kategori
                        (nama, deskripsi, status_aktif)
                     VALUES (?, ?, ?)"
                );

                if (!$stmt) {
                    $pesan = 'Gagal menyiapkan penyimpanan kategori.';
                    $jenis_pesan = 'danger';

                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        'ssi',
                        $nama,
                        $deskripsi,
                        $statusAktif
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);

                        header(
                            "Location: kategori_pelanggaran.php?status=sukses"
                        );
                        exit;

                    } else {
                        $pesan = 'Kategori gagal disimpan. Silakan coba lagi.';
                        $jenis_pesan = 'danger';

                        mysqli_stmt_close($stmt);
                    }
                }
            }
        }
    }
}

// Tampilkan kategori AKTIF saja.
// Tidak ada pembatasan jumlah kategori aktif.
$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY id ASC"
);

if (!$query) {
    die('Gagal mengambil data kategori pelanggaran.');
}

function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kelola Kategori Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

        /* SIDEBAR: ukuran dan tampilan seperti halaman Kelola Guru */
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

        /* KONTEN */
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

        .judul,
        h4 {
            color: #234e78;
        }

        .judul {
            font-size: 28px;
            font-weight: 600;
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
            font-size: 14px;
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
            padding: 8px 15px;
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

        .status-aktif {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            background: #e4f3e9;
            color: #34734b;
        }

        .keterangan {
            color: #71849a;
            font-size: 14px;
        }

        .alert {
            border-radius: 10px;
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

            .judul {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar menggunakan file layout yang sama dengan halaman admin -->
<aside class="sidebar">
    <?php include "layout/sidebar.php"; ?>
</aside>

<main class="main">
    <div class="container py-2">

        <!-- Tombol navigasi -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <button
                type="button"
                class="btn btn-primary"
                id="tombolTambah"
                data-bs-toggle="collapse"
                data-bs-target="#formTambahKategori"
                aria-expanded="<?= $bukaForm ? 'true' : 'false'; ?>"
                aria-controls="formTambahKategori">
                + Tambah Kategori Pelanggaran
            </button>

        </div>

        <!-- Judul halaman -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Kelola Kategori Pelanggaran</h2>
                <p class="text-muted mb-0">
                    Tambah dan kelola kategori pelanggaran siswa.
                </p>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if ($pesan !== ''): ?>
            <div
                class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show"
                role="alert">

                <?= e($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup">
                </button>
            </div>
        <?php endif; ?>

        <!-- Form tambah kategori -->
        <div
            class="collapse <?= $bukaForm ? 'show' : ''; ?>"
            id="formTambahKategori">

            <div class="card mb-4">
                <div class="card-body">

                    <h4 class="mb-4">Tambah Kategori Pelanggaran</h4>

                    <form
                        method="POST"
                        action="kategori_pelanggaran.php"
                        id="formKategori">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kategori
                            </label>

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                class="form-control"
                                placeholder="Contoh: Perundungan"
                                maxlength="100"
                                value="<?= e($_POST['nama'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan deskripsi kategori pelanggaran"
                                required><?= e($_POST['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select
                                id="status_aktif"
                                name="status_aktif"
                                class="form-select"
                                required>

                                <option
                                    value="1"
                                    <?= (($_POST['status_aktif'] ?? '1') === '1') ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option
                                    value="0"
                                    <?= (($_POST['status_aktif'] ?? '') === '0') ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan Kategori
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#formTambahKategori">
                            Tutup Form
                        </button>

                    </form>

                </div>
            </div>
        </div>

        <!-- Tabel kategori aktif -->
        <div class="card">
            <div class="card-body">

                <h4 class="mb-1">Data Kategori Pelanggaran</h4>

                <p class="keterangan mb-3">
                    Menampilkan seluruh kategori yang berstatus aktif.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($query) > 0): ?>

                                <?php $no = 1; ?>

                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= e($data['nama'] ?? ''); ?></td>

                                        <td><?= e($data['deskripsi'] ?? ''); ?></td>

                                        <td>
                                            <span class="status-aktif">Aktif</span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-2">

                                                <a
                                                    href="edit_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                    Hapus
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada kategori aktif.
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formTambah = document.getElementById('formTambahKategori');
    const tombolTambah = document.getElementById('tombolTambah');

    formTambah.addEventListener('shown.bs.collapse', function () {
        tombolTambah.textContent = '− Tutup Form Kategori';

        formTambah.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });

    formTambah.addEventListener('hidden.bs.collapse', function () {
        tombolTambah.textContent = '+ Tambah Kategori Pelanggaran';
    });

    if (formTambah.classList.contains('show')) {
        tombolTambah.textContent = '− Tutup Form Kategori';
    }
});
</script>

</body>
</html>
