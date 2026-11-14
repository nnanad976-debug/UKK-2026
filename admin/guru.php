
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_set_charset($koneksi, 'utf8mb4');

$pesan = '';
$jenis_pesan = 'success';
$bukaForm = false;

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* =========================
   NOTIFIKASI
========================= */

if (isset($_GET['berhasil'])) {
    $pesan = 'Data guru berhasil ditambahkan.';
}

/* =========================
   PROSES TAMBAH GURU
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $emailGuru = trim($_POST['email'] ?? '');
    $username = $emailGuru;

    $password = $_POST['password'] ?? '';
    $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

    $statusAktif = filter_var(
        $_POST['status_aktif'] ?? '1',
        FILTER_VALIDATE_INT
    );

    $bukaForm = true;

    if (
        $nip === '' ||
        $nama === '' ||
        $emailGuru === '' ||
        $password === '' ||
        $konfirmasiPassword === ''
    ) {
        $pesan = 'Semua kolom wajib diisi.';
        $jenis_pesan = 'danger';

    } elseif (!filter_var($emailGuru, FILTER_VALIDATE_EMAIL)) {
        $pesan = 'Format email guru tidak valid.';
        $jenis_pesan = 'danger';

    } elseif (strlen($password) < 8) {
        $pesan = 'Password minimal 8 karakter.';
        $jenis_pesan = 'danger';

    } elseif ($password !== $konfirmasiPassword) {
        $pesan = 'Konfirmasi password tidak cocok.';
        $jenis_pesan = 'danger';

    } elseif (!in_array($statusAktif, [0, 1], true)) {
        $pesan = 'Status guru tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        try {

            /* CEK NIP DAN EMAIL PADA TABEL GURU */

            $cekGuru = mysqli_prepare(
                $koneksi,
                "SELECT id
                 FROM t_guru
                 WHERE nip = ? OR email = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $cekGuru,
                'ss',
                $nip,
                $emailGuru
            );

            mysqli_stmt_execute($cekGuru);

            $hasilCekGuru = mysqli_stmt_get_result($cekGuru);
            $guruSudahAda = mysqli_num_rows($hasilCekGuru) > 0;

            mysqli_stmt_close($cekGuru);

            /* CEK EMAIL PADA AKUN LOGIN */

            $cekUser = mysqli_prepare(
                $koneksi,
                "SELECT id
                 FROM t_users
                 WHERE email = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $cekUser,
                's',
                $username
            );

            mysqli_stmt_execute($cekUser);

            $hasilCekUser = mysqli_stmt_get_result($cekUser);
            $userSudahAda = mysqli_num_rows($hasilCekUser) > 0;

            mysqli_stmt_close($cekUser);

            if ($guruSudahAda) {

                $pesan = 'NIP atau email guru sudah terdaftar.';
                $jenis_pesan = 'danger';

            } elseif ($userSudahAda) {

                $pesan = 'Email tersebut sudah digunakan untuk login.';
                $jenis_pesan = 'danger';

            } else {

                mysqli_begin_transaction($koneksi);

                try {

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $role = 'guru';

                    /* MEMBUAT AKUN LOGIN GURU */

                    $stmtUser = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_users
                        (name, email, password, role, created_at, updated_at)
                        VALUES (?, ?, ?, ?, NOW(), NOW())"
                    );

                    mysqli_stmt_bind_param(
                        $stmtUser,
                        'ssss',
                        $nama,
                        $username,
                        $passwordHash,
                        $role
                    );

                    mysqli_stmt_execute($stmtUser);

                    $userId = mysqli_insert_id($koneksi);

                    mysqli_stmt_close($stmtUser);

                    /* MENYIMPAN DATA GURU */

                    $stmtGuru = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_guru
                        (
                            nip,
                            nama,
                            email,
                            status_aktif,
                            user_id,
                            created_at,
                            updated_at
                        )
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
                    );

                    mysqli_stmt_bind_param(
                        $stmtGuru,
                        'sssii',
                        $nip,
                        $nama,
                        $emailGuru,
                        $statusAktif,
                        $userId
                    );

                    mysqli_stmt_execute($stmtGuru);

                    mysqli_stmt_close($stmtGuru);

                    mysqli_commit($koneksi);

                    header('Location: guru.php?berhasil=1');
                    exit;

                } catch (Throwable $error) {

                    mysqli_rollback($koneksi);

                    $pesan = 'Data guru gagal disimpan. Periksa struktur database dan coba lagi.';
                    $jenis_pesan = 'danger';
                }
            }

        } catch (Throwable $error) {

            $pesan = 'Terjadi kesalahan saat memeriksa data guru.';
            $jenis_pesan = 'danger';
        }
    }
}

/* =========================
   AMBIL DATA GURU
========================= */

try {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            g.id,
            g.nip,
            g.nama,
            g.email,
            g.status_aktif
         FROM t_guru g
         ORDER BY g.id DESC"
    );

} catch (mysqli_sql_exception $error) {

    $query = false;

    $pesan = 'Data guru gagal dimuat. Periksa tabel dan kolom pada t_guru.';
    $jenis_pesan = 'danger';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kelola Guru</title>

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
            font-size: 16px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */

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

        /* KONTEN UTAMA */

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
            font-size: 26px;
            font-weight: 700;
        }

        .card {
            border: 1px solid #e1eaf4 !important;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .05) !important;
        }

        .card-body {
            padding: 24px;
        }

        /* FORM */

        .form-label {
            color: #405873;
            font-weight: 600;
            font-size: 16px;
        }

        .form-control,
        .form-select {
            border-color: #d7e3ef;
            border-radius: 7px;
            padding: 10px 12px;
            font-size: 16px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        /* TOMBOL */

        .btn {
            border-radius: 7px;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            padding: 8px 15px;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f91ba;
            --bs-btn-border-color: #5f91ba;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #4b7fa9;
            --bs-btn-hover-border-color: #4b7fa9;
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

        /* TOMBOL AKSI TABEL */

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            line-height: 1.4;
            border-radius: 6px;
        }

        /* TABEL */

        .table {
            color: #344b63;
            vertical-align: middle;
            margin-bottom: 0;
            font-size: 16px;
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

        /* STATUS */

        .status-aktif,
        .status-nonaktif {
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

        /* KETERANGAN */

        .keterangan {
            color: #71849a;
            font-size: 16px;
        }

        /* DATATABLES */

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
            width: 55px !important;
            min-width: 55px;
            height: 38px;
            padding: 6px 5px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: #344b63;
            font-size: 14px;
        }

        .dt-container .dt-search input {
            width: 175px;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            margin-left: 0 !important;
            font-size: 14px;
        }

        .dt-container .dt-length select:focus,
        .dt-container .dt-search input:focus {
            outline: none;
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .15rem rgba(87, 143, 190, .14);
        }

        .dt-container .dt-layout-table {
            margin-top: 8px;
        }

        .table-responsive {
            width: 100%;
        }

        /* PASSWORD */

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 100px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #52789c;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .password-feedback {
            display: none;
            font-size: 13px;
            margin-top: 6px;
        }

        /* RESPONSIVE */

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
                font-size: 23px;
            }

            .dt-container .dt-search input {
                width: 140px;
            }

            .dt-container .dt-layout-row {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <?php include "layout/sidebar.php"; ?>
</aside>

<main class="main">

    <div class="container py-2">

        <!-- TOMBOL BAGIAN ATAS -->

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <a href="../dashboard.php" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>

            <button
                type="button"
                class="btn btn-primary"
                id="tombolTambah"
                style="<?= $bukaForm ? 'display:none;' : ''; ?>"
                aria-expanded="<?= $bukaForm ? 'true' : 'false'; ?>"
                aria-controls="formTambahGuru"
            >
                + Tambah Data Guru
            </button>

        </div>

        <!-- JUDUL HALAMAN -->

        <div class="card mb-4">

            <div class="card-body">

                <h2 class="judul mb-1">
                    Kelola Guru
                </h2>

                <p class="text-muted mb-0">
                    Tambah dan kelola data guru beserta akun login.
                </p>

            </div>

        </div>

        <!-- NOTIFIKASI -->

        <?php if ($pesan !== '' && $jenis_pesan === 'danger'): ?>

            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <?= e($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>

            </div>

        <?php elseif ($pesan !== '' && isset($_GET['berhasil'])): ?>

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <?= e($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>

            </div>

        <?php endif; ?>

        <!-- FORM TAMBAH GURU -->

        <div
            id="formTambahGuru"
            class="card mb-4"
            style="<?= $bukaForm ? '' : 'display:none;'; ?>"
        >

            <div class="card-body">

                <h4 class="mb-4">
                    Tambah Data Guru
                </h4>

                <form
                    method="POST"
                    action="guru.php"
                    autocomplete="off"
                    id="formGuru"
                >

                    <div class="mb-3">

                        <label for="nip" class="form-label">
                            NIP
                        </label>

                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            class="form-control"
                            placeholder="Masukkan NIP guru"
                            value="<?= e($_POST['nip'] ?? ''); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="nama" class="form-label">
                            Nama Guru
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama guru"
                            value="<?= e($_POST['nama'] ?? ''); ?>"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="email" class="form-label">
                            Email Guru
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email guru"
                            value="<?= e($_POST['email'] ?? ''); ?>"
                            required
                        >

                        <small class="text-muted">
                            Email ini juga digunakan untuk login guru.
                        </small>

                    </div>

                    <div class="mb-3">

                        <label for="password" class="form-label">
                            Password
                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimal 8 karakter"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password"
                            >
                                Lihat
                            </button>

                        </div>

                        <small class="text-muted">
                            Password minimal 8 karakter.
                        </small>

                    </div>

                    <div class="mb-3">

                        <label for="konfirmasi_password" class="form-label">
                            Konfirmasi Password
                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                class="form-control"
                                placeholder="Masukkan ulang password"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                aria-describedby="pesanKonfirmasi"
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="konfirmasi_password"
                            >
                                Lihat
                            </button>

                        </div>

                        <div
                            id="pesanKonfirmasi"
                            class="password-feedback"
                        ></div>

                    </div>

                    <div class="mb-4">

                        <label for="status_aktif" class="form-label">
                            Status
                        </label>

                        <select
                            id="status_aktif"
                            name="status_aktif"
                            class="form-select"
                            required
                        >

                            <option
                                value="1"
                                <?= (($_POST['status_aktif'] ?? '1') === '1') ? 'selected' : ''; ?>
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                <?= (($_POST['status_aktif'] ?? '') === '0') ? 'selected' : ''; ?>
                            >
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                    <!-- TOMBOL FORM -->

                    <div class="d-flex flex-wrap gap-2">

                        <button
                            type="submit"
                            name="simpan"
                            value="1"
                            class="btn btn-primary"
                        >
                            Tambah Guru
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="batalTambah"
                        >
                            Batal
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- TABEL DATA GURU -->

        <div class="card">

            <div class="card-body">

                <h4 class="mb-1">
                    Data Guru
                </h4>

                <p class="keterangan mb-3">
                    Daftar seluruh guru yang terdaftar.
                </p>

                <div class="table-responsive">

                    <table
                        id="tabelGuru"
                        class="table table-bordered table-hover align-middle w-100"
                    >

                        <thead>

                            <tr>
                                <th style="width:60px">No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Email Guru</th>
                                <th>Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if ($query && mysqli_num_rows($query) > 0): ?>

                                <?php while ($guru = mysqli_fetch_assoc($query)): ?>

                                    <tr>

                                        <td></td>

                                        <td>
                                            <?= e($guru['nip'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= e($guru['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= e($guru['email'] ?? ''); ?>
                                        </td>

                                        <td>

                                            <?php if ((int)($guru['status_aktif'] ?? 0) === 1): ?>

                                                <span class="status-aktif">
                                                    Aktif
                                                </span>

                                            <?php else: ?>

                                                <span class="status-nonaktif">
                                                    Tidak Aktif
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <div class="d-flex flex-wrap gap-2">

                                                <a
                                                    href="edit_guru.php?id=<?= (int)$guru['id']; ?>"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_guru.php?id=<?= (int)$guru['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus guru ini?')"
                                                >
                                                    Hapus
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php elseif ($query): ?>

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada data guru.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center text-danger py-4">
                                        Data guru gagal dimuat.
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

<!-- JAVASCRIPT -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       BUKA DAN BATAL FORM
    ========================= */

    const formTambah = document.getElementById('formTambahGuru');
    const tombolTambah = document.getElementById('tombolTambah');
    const tombolBatal = document.getElementById('batalTambah');
    const formGuru = document.getElementById('formGuru');

    const password = document.getElementById('password');
    const konfirmasi = document.getElementById('konfirmasi_password');
    const pesanKonfirmasi = document.getElementById('pesanKonfirmasi');

    if (tombolTambah && formTambah) {

        tombolTambah.addEventListener('click', function () {

            formTambah.style.display = 'block';
            tombolTambah.style.display = 'none';

            tombolTambah.setAttribute('aria-expanded', 'true');

            formTambah.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }

    if (tombolBatal && formTambah) {

        tombolBatal.addEventListener('click', function () {

            formGuru.reset();

            password.type = 'password';
            konfirmasi.type = 'password';

            password.setCustomValidity('');
            konfirmasi.setCustomValidity('');

            password.classList.remove('is-valid', 'is-invalid');
            konfirmasi.classList.remove('is-valid', 'is-invalid');

            pesanKonfirmasi.textContent = '';
            pesanKonfirmasi.style.display = 'none';

            document.querySelectorAll('.toggle-password').forEach(function (tombol) {
                tombol.textContent = 'Lihat';
            });

            formTambah.style.display = 'none';

            if (tombolTambah) {
                tombolTambah.style.display = 'inline-block';
                tombolTambah.setAttribute('aria-expanded', 'false');
            }

        });

    }

    /* =========================
       LIHAT / SEMBUNYIKAN PASSWORD
    ========================= */

    document.querySelectorAll('.toggle-password').forEach(function (tombol) {

        tombol.addEventListener('click', function () {

            const input = document.getElementById(this.dataset.target);

            if (input.type === 'password') {

                input.type = 'text';
                this.textContent = 'Sembunyikan';

            } else {

                input.type = 'password';
                this.textContent = 'Lihat';

            }

        });

    });

    /* =========================
       VALIDASI KONFIRMASI PASSWORD
    ========================= */

    function periksaPassword() {

        if (konfirmasi.value === '') {

            konfirmasi.setCustomValidity('');

            konfirmasi.classList.remove('is-valid', 'is-invalid');

            pesanKonfirmasi.textContent = '';
            pesanKonfirmasi.style.display = 'none';

            return;
        }

        if (password.value !== konfirmasi.value) {

            konfirmasi.setCustomValidity('Konfirmasi password tidak cocok.');

            konfirmasi.classList.add('is-invalid');
            konfirmasi.classList.remove('is-valid');

            pesanKonfirmasi.textContent = 'Konfirmasi password tidak cocok.';
            pesanKonfirmasi.style.color = '#dc3545';
            pesanKonfirmasi.style.display = 'block';

        } else {

            konfirmasi.setCustomValidity('');

            konfirmasi.classList.add('is-valid');
            konfirmasi.classList.remove('is-invalid');

            pesanKonfirmasi.textContent = 'Password sudah cocok.';
            pesanKonfirmasi.style.color = '#198754';
            pesanKonfirmasi.style.display = 'block';

        }

    }

    password.addEventListener('input', periksaPassword);
    konfirmasi.addEventListener('input', periksaPassword);

    /* =========================
       VALIDASI SEBELUM SIMPAN
    ========================= */

    formGuru.addEventListener('submit', function (event) {

        periksaPassword();

        if (!formGuru.checkValidity()) {

            event.preventDefault();
            formGuru.reportValidity();

        }

    });

    /* =========================
       DATATABLES
    ========================= */

    if (window.jQuery && $.fn.DataTable) {

        $('#tabelGuru').DataTable({

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            ordering: true,

            searching: true,

            paging: true,

            autoWidth: false,

            order: [[1, 'desc']],

            columnDefs: [
                {
                    targets: [0, 5],
                    orderable: false,
                    searchable: false
                }
            ],

            language: {

                search: 'Cari:',

                searchPlaceholder: 'Cari nama atau NIP...',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ guru',

                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 guru',

                infoFiltered: '(difilter dari _MAX_ total guru)',

                zeroRecords: 'Data guru tidak ditemukan',

                emptyTable: 'Belum ada data guru',

                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }

            },

            drawCallback: function () {

                const api = this.api();
                const info = api.page.info();

                api.column(0, {
                    page: 'current',
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function (cell, i) {

                    cell.textContent = info.start + i + 1;

                });

            }

        });

    }

});
</script>

</body>
</html>
