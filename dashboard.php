
<?php
include __DIR__ . "/middleware/auth.php";

$role = strtolower(trim($_SESSION['role'] ?? ''));
$nama = $_SESSION['nama'] ?? $_SESSION['name'] ?? 'User';

$isWaliKelas = (
    $role !== 'admin' &&
    !empty($_SESSION['is_wali_kelas'])
);

if ($role === 'admin') {
    $jenisDashboard = 'admin';
} elseif ($isWaliKelas) {
    $jenisDashboard = 'wali_kelas';
} else {
    $jenisDashboard = 'guru';
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Sistem Pelanggaran</title>

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --page: #f3f7fc;
    --text: #263f63;
    --muted: #7890ad;
    --line: #dce9f8;
    --white: #ffffff;
    --shadow: 0 3px 12px rgba(35, 78, 120, .045);

    --sidebar-width: 282px;
    --font: "Segoe UI", Arial, sans-serif;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    min-height: 100%;
    overflow-y: scroll;
    scroll-behavior: auto;
}

body {
    width: 100%;
    min-height: 100vh;
    overflow-x: hidden;
    background: var(--page);
    color: var(--text);
    font-family: var(--font);
    font-size: 15px;
    font-weight: 400;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

a {
    color: inherit;
    text-decoration: none;
}

button,
input,
select,
textarea {
    font: inherit;
}

/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;

    width: var(--sidebar-width);
    height: 100vh;
    padding: 25px 14px 16px;

    display: flex;
    flex-direction: column;

    background: var(--white);
    border-right: 1px solid var(--line);

    overflow-x: hidden;
    overflow-y: auto;
    scrollbar-gutter: stable;
}

.brand {
    flex-shrink: 0;
    padding: 0 14px 20px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--line);
}

.brand-title {
    color: var(--blue-dark);
    font-size: 19px;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 5px;
}

.brand-subtitle {
    color: var(--muted);
    font-size: 12px;
    font-weight: 400;
    line-height: 1.5;
}

.menu-title {
    flex-shrink: 0;
    margin: 0 14px 11px;
    color: #91a5bd;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    line-height: 1.4;
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

    flex-shrink: 0;
    min-height: 42px;
    padding: 11px 14px;

    border-radius: 9px;

    color: #5e7695;
    font-size: 13px;
    font-weight: 400;
    line-height: 1.4;

    transition: none;
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
    flex-shrink: 0;
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

/* =========================
   KONTEN UTAMA
========================= */

.main {
    width: calc(100% - var(--sidebar-width));
    min-width: 0;
    min-height: 100vh;
    margin-left: var(--sidebar-width);
    padding: 24px 28px 40px;
}

.page-container {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 0 auto;
}

/* =========================
   CARD
========================= */

.card {
    width: 100%;
    min-width: 0;
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.card-body {
    min-width: 0;
    padding: 20px;
}


/* =========================
   SPASI DAN HIERARKI DASHBOARD
========================= */

.welcome-card {
    margin-bottom: 22px;
}

.welcome-card .card-body {
    padding: 24px 26px;
}

.welcome-title {
    color: #234e78;
    font-size: 29px;
    font-weight: 600;
    line-height: 1.35;
    margin: 0 0 7px;
}

.welcome-description {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.card > .card-body {
    padding: 24px 26px;
}

.section-title {
    color: #234e78;
    font-size: 21px;
    font-weight: 600;
    line-height: 1.4;
    margin: 0 0 6px;
}

.section-description {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
    margin: 0 0 20px;
}

.dashboard-grid {
    margin-top: 0;
}

.menu-card {
    min-height: 170px;
    padding: 22px;
}

.menu-card h3 {
    margin-bottom: 9px;
}

.menu-card p {
    line-height: 1.65;
    max-width: 100%;
}

.card-button {
    margin-top: 20px;
}

/* =========================
   GRID DASHBOARD
========================= */

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    align-items: stretch;
}

/* =========================
   MENU CARD
========================= */

.menu-card {
    min-width: 0;
    min-height: 165px;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;

    padding: 20px;

    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.menu-card h3 {
    color: #234e78;
    font-size: 17px;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 8px;
}

.menu-card p {
    color: var(--muted);
    font-size: 13px;
    font-weight: 400;
    line-height: 1.55;
    margin: 0;
}

/* =========================
   TOMBOL
========================= */

.card-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 35px;
    margin-top: 18px;
    padding: 8px 15px;

    border: 1px solid var(--blue);
    border-radius: 7px;

    background: var(--blue);
    color: var(--white);

    font-size: 13px;
    font-weight: 600;
    line-height: 1.4;

    transition: none;
}

.card-button:hover {
    background: #2168b4;
    border-color: #2168b4;
    color: var(--white);
}

/* =========================
   FOCUS
========================= */

a:focus-visible {
    outline: 2px solid var(--blue);
    outline-offset: 3px;
}

/* =========================
   FOOTER
========================= */

.footer {
    margin-top: 28px;
    color: #9aabc0;
    font-size: 11px;
    font-weight: 400;
    line-height: 1.5;
    text-align: center;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1100px) {
    .dashboard-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        min-height: auto;
        padding: 20px 14px;
        overflow: visible;
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
        padding: 19px;
    }

    .welcome-card .card-body {
        padding: 20px;
    }

    .welcome-card {
        margin-bottom: 18px;
    }

    .section-description {
        margin-bottom: 17px;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .welcome-title,
    .judul {
        font-size: 25px;
    }

    .section-title {
        font-size: 19px;
    }

    .menu-card {
        min-height: 150px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        transition: none !important;
        animation: none !important;
    }
}
</style>
</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">

        <a href="dashboard.php" class="menu-link active">Dashboard</a>

        <?php if ($jenisDashboard === 'admin'): ?>

            <a href="admin/guru.php" class="menu-link">Kelola Guru</a>
            <a href="admin/siswa.php" class="menu-link">Kelola Siswa</a>
            <a href="admin/kelas.php" class="menu-link">Kelola Kelas</a>
            <a href="admin/tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
            <a href="admin/penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
            <a href="admin/wali_kelas.php" class="menu-link">Wali Kelas</a>
            <a href="admin/kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
            <a href="admin/jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
            <a href="admin/cetak_export.php" class="menu-link">Laporan</a>

        <?php elseif ($jenisDashboard === 'wali_kelas'): ?>

            <a href="walikelas/catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
            <a href="walikelas/riwayat.php" class="menu-link">Riwayat</a>
            <a href="walikelas/data_siswa.php" class="menu-link">Data Siswa</a>
            <a href="walikelas/rekap_poin.php" class="menu-link">Rekap Poin Siswa</a>
            <a href="walikelas/rekap_pelanggaran.php" class="menu-link">Rekap Pelanggaran</a>
            <a href="walikelas/cetak_rekap.php" class="menu-link">Laporan</a>

        <?php else: ?>

            <a href="guru/catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
            <a href="guru/tindakan.php" class="menu-link">Tindakan</a>
            <a href="guru/riwayat.php" class="menu-link">Riwayat</a>
            <a href="guru/rekap_poin.php" class="menu-link">Rekap Poin</a>

        <?php endif; ?>

        <a href="about.php" class="menu-link">About Me</a>

    </nav>

    <div class="logout">
        <a href="logout.php" class="menu-link">Logout</a>
    </div>

</aside>

<!-- =========================
     KONTEN UTAMA
========================= -->

<main class="main">

    <div class="page-container">

        <!-- SAMBUTAN -->

        <section class="card welcome-card">
            <div class="card-body">

                <h1 class="welcome-title">
                    Selamat datang, <?= e($nama); ?>
                </h1>

                <p class="welcome-description">
                    <?php if ($jenisDashboard === 'admin'): ?>
                        Kelola data sekolah dengan mudah dan terstruktur.
                    <?php elseif ($jenisDashboard === 'wali_kelas'): ?>
                        Pantau data siswa dan perkembangan pelanggaran kelas Anda.
                    <?php else: ?>
                        Kelola pencatatan dan tindakan pelanggaran siswa.
                    <?php endif; ?>
                </p>

            </div>
        </section>

        <!-- MENU DASHBOARD -->

        <section class="card">
            <div class="card-body">

                <h2 class="section-title">Menu Utama</h2>

                <p class="section-description">
                    Pilih menu untuk membuka fitur yang tersedia.
                </p>

                <div class="dashboard-grid">

                    <?php if ($jenisDashboard === 'admin'): ?>

                        <article class="menu-card">
                            <div>
                                <h3>Kelola Data</h3>
                                <p>
                                    Kelola data guru, siswa, kelas, tahun ajaran,
                                    penempatan siswa, dan wali kelas.
                                </p>
                            </div>
                            <a href="admin/guru.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Data Pelanggaran</h3>
                                <p>
                                    Kelola kategori dan jenis pelanggaran siswa.
                                </p>
                            </div>
                            <a href="admin/kategori_pelanggaran.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Laporan</h3>
                                <p>
                                    Cetak dan export data pelanggaran sekolah.
                                </p>
                            </div>
                            <a href="admin/cetak_export.php" class="card-button">Buka</a>
                        </article>

                    <?php elseif ($jenisDashboard === 'wali_kelas'): ?>

                        <article class="menu-card">
                            <div>
                                <h3>Catat Pelanggaran</h3>
                                <p>
                                    Catat pelanggaran siswa di kelas yang menjadi
                                    tanggung jawab Anda.
                                </p>
                            </div>
                            <a href="walikelas/catat_pelanggaran.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Riwayat Pelanggaran</h3>
                                <p>
                                    Lihat riwayat pelanggaran yang telah dicatat
                                    oleh Wali Kelas.
                                </p>
                            </div>
                            <a href="walikelas/riwayat.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Data Siswa</h3>
                                <p>
                                    Lihat daftar siswa yang berada di kelas
                                    tanggung jawab Anda.
                                </p>
                            </div>
                            <a href="walikelas/data_siswa.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Rekap Poin Siswa</h3>
                                <p>
                                    Lihat jumlah pelanggaran dan akumulasi poin
                                    siswa di kelas.
                                </p>
                            </div>
                            <a href="walikelas/rekap_poin.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Rekap Pelanggaran</h3>
                                <p>
                                    Pantau data pelanggaran dan perkembangan
                                    ketertiban kelas.
                                </p>
                            </div>
                            <a href="walikelas/rekap_pelanggaran.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Laporan</h3>
                                <p>
                                    Cetak laporan pelanggaran siswa berdasarkan kelas.
                                </p>
                            </div>
                            <a href="walikelas/cetak_rekap.php" class="card-button">Buka</a>
                        </article>

                    <?php else: ?>

                        <article class="menu-card">
                            <div>
                                <h3>Catat Pelanggaran</h3>
                                <p>
                                    Catat pelanggaran siswa dengan mudah
                                    dan terstruktur.
                                </p>
                            </div>
                            <a href="guru/catat_pelanggaran.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Tindakan</h3>
                                <p>
                                    Kelola tindakan yang diberikan terhadap
                                    pelanggaran siswa.
                                </p>
                            </div>
                            <a href="guru/tindakan.php" class="card-button">Buka</a>
                        </article>

                        <article class="menu-card">
                            <div>
                                <h3>Riwayat</h3>
                                <p>
                                    Lihat riwayat pencatatan pelanggaran siswa
                                    yang telah dilakukan.
                                </p>
                            </div>
                            <a href="guru/riwayat.php" class="card-button">Buka</a>
                        </article>

                    <?php endif; ?>

                </div>

            </div>
        </section>

        <!-- FOOTER -->

        <footer class="footer">
            Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
        </footer>

    </div>

</main>

</body>
</html>
