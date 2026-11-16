
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =====================================
   PEMERIKSAAN AKSES WALI KELAS
===================================== */

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (
    $role !== 'guru' ||
    empty($_SESSION['is_wali_kelas']) ||
    empty($_SESSION['guru_id'])
) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

$guru_id = (int) $_SESSION['guru_id'];
$nama = $_SESSION['nama'] ?? 'Wali Kelas';

/* =====================================
   FUNGSI
===================================== */

function e($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function menuAktif(string $file, string $halaman): string
{
    return $file === $halaman ? 'active' : '';
}

/* =====================================
   AMBIL PENUGASAN KELAS WALI KELAS
===================================== */

$sql_kelas = "
    SELECT DISTINCT
        wk.kelas_id,
        wk.tahun_ajaran_id,
        k.nama AS nama_kelas
    FROM t_wali_kelas wk
    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND k.status_aktif = 1
      AND wk.tanggal_mulai <= CURDATE()
      AND (
          wk.tanggal_selesai IS NULL
          OR wk.tanggal_selesai >= CURDATE()
      )
    ORDER BY k.nama ASC
";

$stmt = $koneksi->prepare($sql_kelas);

if (!$stmt) {
    exit("Query kelas gagal: " . e($koneksi->error));
}

$stmt->bind_param("i", $guru_id);
$stmt->execute();

$result_kelas = $stmt->get_result();
$daftar_kelas = [];

while ($row = $result_kelas->fetch_assoc()) {
    $daftar_kelas[] = $row;
}

$stmt->close();

/* =====================================
   PILIH PENUGASAN KELAS
===================================== */

$kelas_id = 0;
$tahun_ajaran_id = 0;
$nama_kelas_aktif = '';

$pilihan_kelas = trim($_GET['penugasan'] ?? '');
$kelas_valid = false;

if ($pilihan_kelas !== '') {
    $bagian = explode('|', $pilihan_kelas);

    if (count($bagian) === 2) {
        $pilih_kelas_id = (int) $bagian[0];
        $pilih_tahun_id = (int) $bagian[1];

        foreach ($daftar_kelas as $kelas) {
            if (
                (int) $kelas['kelas_id'] === $pilih_kelas_id &&
                (int) $kelas['tahun_ajaran_id'] === $pilih_tahun_id
            ) {
                $kelas_id = $pilih_kelas_id;
                $tahun_ajaran_id = $pilih_tahun_id;
                $nama_kelas_aktif = $kelas['nama_kelas'];
                $kelas_valid = true;
                break;
            }
        }
    }
}

/* =====================================
   DEFAULT KELAS PERTAMA
===================================== */

if (!$kelas_valid && !empty($daftar_kelas)) {
    $kelas_id = (int) $daftar_kelas[0]['kelas_id'];
    $tahun_ajaran_id = (int) $daftar_kelas[0]['tahun_ajaran_id'];
    $nama_kelas_aktif = $daftar_kelas[0]['nama_kelas'];
}

/* =====================================
   AMBIL SISWA
   SATU SISWA SATU PENEMPATAN AKTIF
===================================== */

$siswa = [];

if ($kelas_id > 0 && $tahun_ajaran_id > 0) {

    $sql_siswa = "
        SELECT
            s.id,
            s.nis,
            s.nisn,
            s.nama,
            s.jenis_kelamin,
            s.tanggal_lahir,
            s.alamat
        FROM t_kelas_siswa ks

        INNER JOIN (
            SELECT MAX(id) AS id
            FROM t_kelas_siswa
            WHERE kelas_id = ?
              AND tahun_ajaran_id = ?
              AND status_aktif = 1
              AND tanggal_mulai <= CURDATE()
              AND (
                  tanggal_selesai IS NULL
                  OR tanggal_selesai >= CURDATE()
              )
            GROUP BY siswa_id
        ) penempatan_aktif
            ON penempatan_aktif.id = ks.id

        INNER JOIN t_siswa s
            ON s.id = ks.siswa_id

        WHERE ks.kelas_id = ?
          AND ks.tahun_ajaran_id = ?
          AND ks.status_aktif = 1
          AND s.status_aktif = 1

        ORDER BY s.nama ASC, s.id ASC
    ";

    $stmt_siswa = $koneksi->prepare($sql_siswa);

    if (!$stmt_siswa) {
        exit("Query siswa gagal: " . e($koneksi->error));
    }

    $stmt_siswa->bind_param(
        "iiii",
        $kelas_id,
        $tahun_ajaran_id,
        $kelas_id,
        $tahun_ajaran_id
    );

    $stmt_siswa->execute();

    $result_siswa = $stmt_siswa->get_result();

    while ($row = $result_siswa->fetch_assoc()) {
        $siswa[] = $row;
    }

    $stmt_siswa->close();
}

/* =====================================
   SIDEBAR AKTIF
===================================== */

$halamanAktif = basename($_SERVER['PHP_SELF'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Siswa - Sistem Pelanggaran</title>

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32, 62, 104, .07);
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea,
        table {
            font-family: inherit;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
            border-right: 1px solid var(--line);
        }

        .brand {
            padding: 0 14px 20px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--line);
        }

        .brand-title {
            margin-bottom: 5px;
            color: var(--blue-dark);
            font-size: 19px;
            font-weight: 700;
            line-height: 1.4;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
            line-height: 1.4;
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
            font-weight: 400;
            line-height: 1.4;
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

        /* KONTEN UTAMA */

        .main {
            display: block;
            width: auto;
            min-width: 0;
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-container {
            display: block;
            width: 100%;
            max-width: none;
            min-width: 0;
            margin: 0;
        }

        /* HEADER */

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            min-height: 112px;
            padding: 24px 25px;
            margin-bottom: 22px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(32, 62, 104, .04);
        }

        .welcome h2 {
            margin: 0 0 7px;
            color: var(--blue-dark);
            font-size: 25px;
            font-weight: 600;
            line-height: 1.4;
        }

        .welcome p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
            line-height: 1.5;
        }

        .user-box {
            flex-shrink: 0;
            padding: 9px 15px;
            background: var(--blue-soft);
            border: 1px solid var(--line);
            border-radius: 30px;
            color: var(--blue-dark);
            font-size: 13px;
            font-weight: 600;
        }

        /* JUDUL HALAMAN */

        .page-head {
            margin: 0 0 20px;
        }

        .page-head h3 {
            margin: 0 0 7px;
            color: var(--blue-dark);
            font-size: 28px;
            font-weight: 600;
            line-height: 1.4;
        }

        .page-head p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
        }

        /* PANEL */

        .panel {
            min-width: 0;
            padding: 25px;
            margin-bottom: 22px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
            min-width: 0;
            margin-bottom: 22px;
        }

        .panel-header h4 {
            margin: 0 0 6px;
            color: var(--blue-dark);
            font-size: 17px;
            font-weight: 700;
            line-height: 1.4;
        }

        .panel-header p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
            line-height: 1.5;
        }

        /* FILTER DAN PENCARIAN */

        .filter-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
            min-width: 0;
        }

        .filter-group form {
            margin: 0;
        }

        .form-select,
        .search-input {
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #d7e3ef;
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            font-weight: 400;
            outline: none;
        }

        .form-select {
            width: 220px;
            max-width: 100%;
            cursor: pointer;
        }

        .search-input {
            width: 220px;
        }

        .form-select:focus,
        .search-input:focus {
            border-color: #8eb5d8;
            box-shadow: 0 0 0 .2rem rgba(87, 143, 190, .14);
        }

        .search-input::placeholder {
            color: #9aabc0;
        }

        /* TABEL */

        .table-area {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .table-responsive {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 9px;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            display: table;
            width: 100%;
            min-width: 0;
            margin: 0;
            border-collapse: collapse;
            table-layout: fixed;
            color: #344b63;
            font-family: inherit;
            font-size: 13px;
            vertical-align: middle;
        }

        .table thead th {
            height: 48px;
            padding: 13px 12px;
            background: #f5f8fc;
            color: #7890ad;
            border-bottom: 1px solid var(--line);
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .table tbody td {
            height: 53px;
            padding: 12px;
            border-bottom: 1px solid #e2eaf3;
            color: #526b89;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.5;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        .table tbody tr:hover {
            background: #fafcff;
        }

        /* Pembagian kolom dibuat proporsional dan total tepat 100% */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 5%;
            text-align: center;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) { width: 11%; }

        .table th:nth-child(3),
        .table td:nth-child(3) { width: 12%; }

        .table th:nth-child(4),
        .table td:nth-child(4) { width: 20%; }

        .table th:nth-child(5),
        .table td:nth-child(5) { width: 14%; }

        .table th:nth-child(6),
        .table td:nth-child(6) { width: 15%; }

        .table th:nth-child(7),
        .table td:nth-child(7) { width: 23%; }

        /* Garis pemisah tipis membantu setiap kolom terlihat jelas */
        .table thead th:not(:last-child),
        .table tbody td:not(:last-child) {
            border-right: 1px solid #edf2f8;
        }

        .table tbody td:nth-child(7) {
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .student-name {
            color: var(--blue-dark);
            font-weight: 600;
        }

        .badge-gender {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* TOTAL DAN KETERANGAN */

        .total-info {
            min-height: 24px;
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
        }

        .total-info strong {
            color: var(--blue-dark);
            font-weight: 700;
        }

        .search-info {
            min-height: 18px;
            margin-top: 7px;
            color: var(--muted);
            font-size: 12px;
        }

        /* DATA KOSONG */

        .empty {
            min-height: 180px;
            padding: 35px 15px !important;
            color: var(--muted) !important;
            font-size: 13px !important;
            text-align: center !important;
            vertical-align: middle;
        }

        /* FOOTER */

        .footer {
            margin-top: 25px;
            color: #91a5bd;
            font-size: 11px;
            font-weight: 400;
            text-align: center;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            .table {
                min-width: 850px;
            }

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

            .topbar {
                min-height: auto;
                padding: 19px;
                align-items: flex-start;
                flex-direction: column;
            }

            .welcome h2 {
                font-size: 23px;
            }

            .page-head h3 {
                font-size: 23px;
            }

            .panel {
                padding: 17px;
            }

            .panel-header {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
                justify-content: flex-start;
                flex-direction: column;
                align-items: stretch;
            }

            .form-select,
            .search-input {
                width: 100%;
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

        <a href="riwayat.php" class="menu-link">
            Riwayat
        </a>

        <a
            href="data_siswa.php"
            class="menu-link <?= menuAktif('data_siswa.php', $halamanAktif); ?>"
            aria-current="page"
        >
            Data Siswa
        </a>

        <a href="rekap_poin.php" class="menu-link">
            Rekap Poin Siswa
        </a>

        <a href="rekap_pelanggaran.php" class="menu-link">
            Rekap Pelanggaran
        </a>

        <a href="cetak_rekap.php" class="menu-link">
            Laporan
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

        <section class="topbar">

            <div class="welcome">
                <h2>Data Siswa</h2>

                <p>
                    Lihat data siswa sesuai kelas yang menjadi tanggung jawab Anda.
                </p>
            </div>

            <div class="user-box">
                <?= e($nama); ?>
            </div>

        </section>

        <section class="page-head">

            <h3>Data Siswa</h3>

            <p>
                Informasi siswa pada kelas yang Anda ampu.
            </p>

        </section>

        <section class="panel">

            <div class="panel-header">

                <div>
                    <h4>Daftar Siswa</h4>

                    <p>
                        Data siswa berdasarkan kelas yang menjadi tanggung jawab Anda.
                    </p>
                </div>

                <?php if (!empty($daftar_kelas)): ?>

                    <div class="filter-group">

                        <form method="GET">

                            <select
                                name="penugasan"
                                class="form-select"
                                onchange="this.form.submit()"
                                aria-label="Pilih kelas"
                            >

                                <?php foreach ($daftar_kelas as $kelas): ?>

                                    <?php
                                    $value_kelas =
                                        (int) $kelas['kelas_id'] . '|' .
                                        (int) $kelas['tahun_ajaran_id'];

                                    $value_aktif =
                                        $kelas_id . '|' .
                                        $tahun_ajaran_id;
                                    ?>

                                    <option
                                        value="<?= e($value_kelas); ?>"
                                        <?= $value_kelas === $value_aktif ? 'selected' : ''; ?>
                                    >
                                        <?= e($kelas['nama_kelas']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </form>

                        <input
                            type="search"
                            id="cariSiswa"
                            class="search-input"
                            placeholder="Cari nama siswa..."
                            autocomplete="off"
                            aria-label="Cari nama siswa"
                        >

                    </div>

                <?php endif; ?>

            </div>

            <?php if (empty($daftar_kelas)): ?>

                <div class="empty">
                    Belum ada penugasan kelas Wali Kelas yang aktif.
                </div>

            <?php else: ?>

                <div class="table-area">

                    <div class="table-responsive">

                        <table class="table" id="tabelSiswa">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Alamat</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($siswa)): ?>

                                    <tr class="baris-kosong">
                                        <td colspan="7" class="empty">
                                            Belum ada siswa aktif di kelas ini.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($siswa as $index => $row): ?>

                                        <tr class="baris-siswa">

                                            <td class="nomor-siswa">
                                                <?= $index + 1; ?>
                                            </td>

                                            <td>
                                                <?= e($row['nis'] ?: '-'); ?>
                                            </td>

                                            <td>
                                                <?= e($row['nisn'] ?: '-'); ?>
                                            </td>

                                            <td>
                                                <span class="student-name">
                                                    <?= e($row['nama']); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php
                                                $gender = $row['jenis_kelamin'] ?? '';

                                                if ($gender === 'L') {
                                                    $genderLabel = 'Laki-laki';
                                                } elseif ($gender === 'P') {
                                                    $genderLabel = 'Perempuan';
                                                } else {
                                                    $genderLabel = '-';
                                                }
                                                ?>

                                                <span class="badge-gender">
                                                    <?= e($genderLabel); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php
                                                $tanggalLahir = '-';

                                                if (!empty($row['tanggal_lahir'])) {
                                                    $timestamp = strtotime($row['tanggal_lahir']);

                                                    if ($timestamp !== false) {
                                                        $tanggalLahir = date('d-m-Y', $timestamp);
                                                    }
                                                }
                                                ?>

                                                <?= e($tanggalLahir); ?>
                                            </td>

                                            <td>
                                                <?= e($row['alamat'] ?: '-'); ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="total-info" id="totalInfo">

                    Total siswa:
                    <strong id="jumlahTampil"><?= count($siswa); ?></strong>

                    &nbsp; | &nbsp;

                    Kelas:
                    <strong><?= e($nama_kelas_aktif); ?></strong>

                </div>

                <div class="search-info" id="searchInfo" aria-live="polite"></div>

            <?php endif; ?>

        </section>

        <footer class="footer">
            © 2026 Sistem Pelanggaran - SMK Muhammadiyah
        </footer>

    </div>

</main>

<!-- JAVASCRIPT PENCARIAN -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const inputCari = document.getElementById('cariSiswa');
    const tabel = document.getElementById('tabelSiswa');
    const jumlahTampil = document.getElementById('jumlahTampil');
    const searchInfo = document.getElementById('searchInfo');

    if (!inputCari || !tabel) {
        return;
    }

    const barisSiswa = Array.from(
        tabel.querySelectorAll('tbody tr.baris-siswa')
    );

    const jumlahAwal = barisSiswa.length;
    const tbody = tabel.querySelector('tbody');

    const barisKosong = document.createElement('tr');

    barisKosong.innerHTML = `
        <td colspan="7" class="empty">
            Siswa yang dicari tidak ditemukan.
        </td>
    `;

    inputCari.addEventListener('input', function () {

        const kataKunci = this.value
            .trim()
            .toLocaleLowerCase('id-ID');

        let nomor = 0;

        barisSiswa.forEach(function (baris) {

            const nama = baris
                .querySelector('.student-name')
                .textContent
                .trim()
                .toLocaleLowerCase('id-ID');

            const cocok = nama.includes(kataKunci);

            baris.hidden = !cocok;

            if (cocok) {
                nomor++;

                const kolomNomor = baris.querySelector('.nomor-siswa');

                if (kolomNomor) {
                    kolomNomor.textContent = nomor;
                }
            }

        });

        if (nomor === 0 && jumlahAwal > 0) {
            if (!barisKosong.isConnected) {
                tbody.appendChild(barisKosong);
            }
        } else {
            barisKosong.remove();
        }

        if (jumlahTampil) {
            jumlahTampil.textContent = nomor;
        }

        if (searchInfo) {
            searchInfo.textContent = kataKunci
                ? `Menampilkan ${nomor} dari ${jumlahAwal} siswa.`
                : '';
        }

    });

});
</script>

</body>
</html>
