```php
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil ID siswa
$id = $_GET['id'] ?? $_POST['id'] ?? '';

if (!ctype_digit((string)$id) || (int)$id < 1) {
    header("Location: siswa.php?status=id_tidak_valid");
    exit;
}

$id = (int)$id;
$pesan = '';

// Ambil data siswa yang akan diedit
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nis, nisn, nama, jenis_kelamin,
            tanggal_lahir, alamat, status_aktif
     FROM t_siswa WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$siswa) {
    header("Location: siswa.php?status=id_tidak_valid");
    exit;
}

// Proses simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['simpan_perubahan'])) {

    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    // Pertahankan input jika validasi gagal
    $siswa['nis'] = $nis;
    $siswa['nisn'] = $nisn;
    $siswa['nama'] = $nama;
    $siswa['jenis_kelamin'] = $jenis_kelamin;
    $siswa['tanggal_lahir'] = $tanggal_lahir;
    $siswa['alamat'] = $alamat;
    $siswa['status_aktif'] = $status_aktif;

    if (
        $nis === '' ||
        $nisn === '' ||
        $nama === '' ||
        !in_array($jenis_kelamin, ['L', 'P'], true) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir) ||
        !checkdate(
            (int)substr($tanggal_lahir, 5, 2),
            (int)substr($tanggal_lahir, 8, 2),
            (int)substr($tanggal_lahir, 0, 4)
        ) ||
        $alamat === '' ||
        !in_array((string)$status_aktif, ['0', '1'], true)
    ) {
        $pesan = 'Periksa kembali semua data. Pastikan sudah diisi dengan benar.';
    } else {
        try {
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_siswa
                 SET nis = ?, nisn = ?, nama = ?,
                     jenis_kelamin = ?, tanggal_lahir = ?,
                     alamat = ?, status_aktif = ?
                 WHERE id = ?"
            );

            $status_aktif = (int)$status_aktif;

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssii",
                $nis,
                $nisn,
                $nama,
                $jenis_kelamin,
                $tanggal_lahir,
                $alamat,
                $status_aktif,
                $id
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: siswa.php?status=edit_sukses");
            exit;

        } catch (mysqli_sql_exception $err) {
            if (isset($stmt) && $stmt instanceof mysqli_stmt) {
                mysqli_stmt_close($stmt);
            }

            $pesan = 'Data gagal diperbarui. Periksa apakah NIS atau NISN sudah digunakan siswa lain.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --muted: #7890ad;
            --line: #dce9f8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f3f7fc;
            color: #2f435a;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        a { text-decoration: none; }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
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

        .brand-subtitle { font-size: 12px; color: var(--muted); }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 15px 10px;
        }

        .menu { display: flex; flex-direction: column; gap: 3px; }

        .menu-link {
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
        }

        .menu-link:hover,
        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .menu-link.active {
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .logout {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .logout .menu-link { color: #d9534f; }

        .main {
            margin-left: 282px;
            min-height: 100vh;
            padding: 24px 28px 40px;
        }

        .judul, h4 { color: #234e78; }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35,78,120,.05);
        }

        .card-body { padding: 24px; }

        .form-label {
            color: #405873;
            font-weight: 600;
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

        .btn { border-radius: 7px; }

        .btn-primary {
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
        }

        .btn-secondary {
            --bs-btn-bg: #e8f0f8;
            --bs-btn-border-color: #d8e5f1;
            --bs-btn-color: #426584;
            --bs-btn-hover-bg: #dceaf6;
            --bs-btn-hover-color: #234e78;
        }

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 18px 14px 30px;
            }

            .logout { margin-top: 14px; }
        }

        @media (max-width: 576px) {
            .card-body { padding: 17px; }
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
        <a href="siswa.php" class="menu-link active">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>
</aside>

<main class="main">
    <div class="container-fluid p-0">
        <div class="mb-4">
            <a href="siswa.php" class="btn btn-secondary">
                &larr; Kembali ke Data Siswa
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="judul mb-1">Edit Data Siswa</h2>
                <p class="text-muted mb-0">
                    Ubah informasi siswa, lalu simpan perubahan.
                </p>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-danger">
                <?= e($pesan); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST"
                      action="edit_siswa.php?id=<?= $id; ?>">

                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="mb-3">
                        <label for="nis" class="form-label">NIS</label>
                        <input type="text" id="nis" name="nis"
                               class="form-control"
                               value="<?= e($siswa['nis']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nisn" class="form-label">NISN</label>
                        <input type="text" id="nisn" name="nisn"
                               class="form-control"
                               value="<?= e($siswa['nisn']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Siswa</label>
                        <input type="text" id="nama" name="nama"
                               class="form-control"
                               value="<?= e($siswa['nama']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">
                            Jenis Kelamin
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                                class="form-select" required>
                            <option value="L"
                                <?= $siswa['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>>
                                Laki-laki
                            </option>
                            <option value="P"
                                <?= $siswa['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="tanggal_lahir"
                               name="tanggal_lahir" class="form-control"
                               value="<?= e($siswa['tanggal_lahir']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat"
                                  class="form-control" rows="3"
                                  required><?= e($siswa['alamat']); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status_aktif" class="form-label">
                            Status
                        </label>
                        <select id="status_aktif" name="status_aktif"
                                class="form-select" required>
                            <option value="1"
                                <?= (string)$siswa['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                Aktif
                            </option>
                            <option value="0"
                                <?= (string)$siswa['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <button type="submit" name="simpan_perubahan"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="siswa.php" class="btn btn-secondary">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>
```