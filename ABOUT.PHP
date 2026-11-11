
<?php
require_once __DIR__ . '/middleware/auth.php';

$role = strtolower(trim((string)($_SESSION['role'] ?? 'guru')));
$namaPengguna = $_SESSION['nama'] ?? $_SESSION['name'] ?? 'Pengguna';

$isWaliKelas = (
    in_array($role, ['wali_kelas', 'wali kelas', 'walikelas'], true) ||
    ($role !== 'admin' && !empty($_SESSION['is_wali_kelas']))
);

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$appBase = rtrim($scriptDir, '/');

if ($appBase === '.' || $appBase === '/') {
    $appBase = '';
}

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function appUrl($path): string
{
    global $appBase;
    return $appBase . '/' . ltrim($path, '/');
}

function findPage(array $candidates): ?string
{
    foreach ($candidates as $candidate) {
        $candidate = ltrim($candidate, '/');

        if (is_file(__DIR__ . '/' . $candidate)) {
            return appUrl($candidate);
        }
    }

    return null;
}

function menuPageLink($url, $label, $active = false): string
{
    $classes = 'menu-link' . ($active ? ' active' : '');

    if ($url === null) {
        return '<a href="#" class="' . $classes . ' disabled" aria-disabled="true">' .
            e($label) . '</a>';
    }

    return '<a href="' . e($url) . '" class="' . $classes . '"' .
        ($active ? ' aria-current="page"' : '') . '>' .
        e($label) . '</a>';
}

/* FOTO PROFIL */
$fotoFile = __DIR__ . '/assets/foto-nadia.png.png';
$fotoUrl = appUrl('assets/foto-nadia.png.png');

if (is_file($fotoFile)) {
    $fotoUrl .= '?v=' . filemtime($fotoFile);
}

/* MENU GURU */
$guruDashboard = findPage([
    'guru/dashboard.php',
    'guru/index.php',
    'guru/beranda.php',
    'dashboard.php'
]);

$guruCatat = findPage(['guru/catat_pelanggaran.php']);
$guruTindakan = findPage(['guru/tindakan.php']);
$guruRiwayat = findPage(['guru/riwayat.php']);
$guruRekap = findPage([
    'guru/rekap_poin.php',
    'guru/rekap.php',
    'guru/rekap-point.php'
]);

/* MENU WALI KELAS */
$waliDashboard = findPage(['dashboard.php']);
$waliCatat = findPage(['walikelas/catat_pelanggaran.php']);
$waliRiwayat = findPage(['walikelas/riwayat.php']);
$waliDataSiswa = findPage(['walikelas/data_siswa.php']);
$waliRekap = findPage(['walikelas/rekap_pelanggaran.php']);
$waliCetak = findPage(['walikelas/cetak_rekap.php']);

/* MENU ADMIN */
$adminDashboard = findPage([
    'admin/dashboard.php',
    'dashboard.php',
    'admin/index.php'
]);

$logoutUrl = findPage(['logout.php']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me | Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f6f9fd;
            --white: #ffffff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32, 62, 104, .07);
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
            transition: transform .3s ease;
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
            min-height: 42px;
            display: flex;
            align-items: center;
            padding: 11px 14px;
            border-radius: 9px;
            color: #5e7695;
            font-size: 13px;
            transition: background .2s ease, color .2s ease,
                        padding-left .2s ease;
        }

        .menu-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
            padding-left: 18px;
        }

        .menu-link.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
            box-shadow: inset 4px 0 0 var(--blue);
        }

        .menu-link.disabled {
            opacity: .45;
            cursor: not-allowed;
            pointer-events: none;
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

        .mobile-header {
            display: none;
        }

        /* HEADER SAMA DENGAN CATAT PELANGGARAN */
        .page-heading {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 24px 25px;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
        }

        .page-heading h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--blue-dark);
            margin: 0 0 7px;
        }

        .page-heading p {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
        }

        /* KARTU PROFIL */
        .profile-card,
        .info-card,
        .technology-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .profile-card {
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            margin-bottom: 22px;
        }

        .profile-label {
            color: var(--blue);
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 13px;
        }

        .profile-card h2 {
            font-size: 32px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 13px;
        }

        .profile-description {
            color: #71819a;
            font-size: 13px;
            line-height: 1.9;
            max-width: 620px;
            margin-bottom: 14px;
        }

        .school-label {
            display: inline-block;
            background: var(--blue-soft);
            color: #285b91;
            font-size: 11px;
            padding: 9px 12px;
            border-radius: 6px;
        }

        .photo-wrapper {
            flex-shrink: 0;
        }

        .profile-photo {
            width: 175px;
            height: 198px;
            object-fit: cover;
            object-position: center top;
            border-radius: 8px;
            display: block;
        }

        .photo-status {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
            color: var(--muted);
        }

        /* INFORMASI PROYEK */
        .info-grid {
            display: grid;
            grid-template-columns: 1.1fr .8fr;
            gap: 22px;
            margin-bottom: 22px;
        }

        .info-card {
            padding: 25px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 7px;
        }

        .section-subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 23px;
        }

        .project-name {
            font-size: 13px;
            font-weight: 700;
            color: #234e78;
            margin-bottom: 8px;
        }

        .body-text {
            color: #71819a;
            font-size: 12px;
            line-height: 1.9;
        }

        .divider {
            border: 0;
            border-top: 1px solid var(--line);
            margin: 18px 0;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px 18px;
        }

        .detail-label {
            color: #8592a7;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .detail-value {
            color: #24466f;
            font-size: 12px;
            font-weight: 600;
        }

        .goal-text {
            border-left: 3px solid #80b5f6;
            padding: 2px 0 2px 13px;
            margin-bottom: 16px;
        }

        /* TEKNOLOGI */
        .technology-card {
            padding: 25px;
        }

        .technology-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 17px;
        }

        .technology-heading h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--blue-dark);
            margin: 0;
        }

        .technology-heading span {
            font-size: 11px;
            color: var(--muted);
        }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tech-item {
            padding: 9px 14px;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: #34577f;
            font-size: 11px;
            background: #fbfcfe;
            transition: .2s ease;
            cursor: default;
        }

        .tech-item:hover {
            background: var(--blue);
            border-color: var(--blue);
            color: white;
        }

        .tech-detail {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-left: 3px solid var(--blue);
            background: #f5f9ff;
            border-radius: 6px;
            color: #58718e;
            font-size: 12px;
            line-height: 1.9;
        }

        .tech-detail.show {
            display: block;
        }

        .detail-button {
            margin-top: 16px;
            background: var(--blue-soft);
            color: #285b91;
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 9px 14px;
            font-size: 12px;
            cursor: pointer;
            transition: .2s ease;
        }

        .detail-button:hover {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }

        .footer-note {
            text-align: right;
            color: #9aa8ba;
            font-size: 10px;
            margin-top: 18px;
        }

        /* RESPONSIVE */
        @media (max-width: 1050px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 25px rgba(25, 50, 80, .12);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(20, 40, 65, .35);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main {
                margin-left: 0;
                padding: 18px 18px 30px;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 20px;
            }

            .menu-toggle {
                width: 40px;
                height: 40px;
                border: 1px solid var(--line);
                background: white;
                color: var(--blue-dark);
                border-radius: 8px;
                font-size: 20px;
                cursor: pointer;
            }

            .mobile-brand {
                font-size: 14px;
                font-weight: 700;
                color: var(--blue-dark);
            }
        }

        @media (max-width: 600px) {
            .page-heading {
                padding: 20px;
            }

            .page-heading h1 {
                font-size: 24px;
            }

            .profile-card {
                flex-direction: column-reverse;
                align-items: flex-start;
                padding: 22px;
            }

            .profile-card h2 {
                font-size: 26px;
            }

            .profile-photo {
                width: 135px;
                height: 155px;
            }

            .info-card,
            .technology-card {
                padding: 20px;
            }

            .technology-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .details-grid {
                gap: 18px 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<?php if ($role === 'admin'): ?>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">SMK Muhammadiyah</div>
        </div>

        <div class="menu-title">Menu Utama</div>

        <nav class="menu">
            <?= menuPageLink($adminDashboard, 'Dashboard') ?>
            <?= menuPageLink(findPage(['admin/guru.php']), 'Kelola Guru') ?>
            <?= menuPageLink(findPage(['admin/siswa.php']), 'Kelola Siswa') ?>
            <?= menuPageLink(findPage(['admin/kelas.php']), 'Kelola Kelas') ?>
            <?= menuPageLink(findPage(['admin/tahun_ajaran.php']), 'Tahun Ajaran') ?>
            <?= menuPageLink(findPage(['admin/penempatan_siswa.php']), 'Penempatan Siswa') ?>
            <?= menuPageLink(findPage(['admin/wali_kelas.php']), 'Wali Kelas') ?>
            <?= menuPageLink(findPage(['admin/kategori_pelanggaran.php']), 'Kategori Pelanggaran') ?>
            <?= menuPageLink(findPage(['admin/jenis_pelanggaran.php']), 'Jenis Pelanggaran') ?>
            <?= menuPageLink(findPage(['admin/cetak_export.php']), 'Laporan') ?>
            <?= menuPageLink(appUrl('about.php'), 'About Me', true) ?>
        </nav>

        <div class="logout">
            <?= menuPageLink($logoutUrl, 'Logout') ?>
        </div>
    </aside>

<?php elseif ($isWaliKelas): ?>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">SMK Muhammadiyah</div>
        </div>

        <div class="menu-title">Menu Utama</div>

        <nav class="menu">
            <?= menuPageLink($waliDashboard, 'Dashboard') ?>
            <?= menuPageLink($waliCatat, 'Catat Pelanggaran') ?>
            <?= menuPageLink($waliRiwayat, 'Riwayat') ?>
            <?= menuPageLink($waliDataSiswa, 'Data Siswa') ?>
            <?= menuPageLink($waliRekap, 'Rekap Pelanggaran') ?>
            <?= menuPageLink($waliCetak, 'Laporan') ?>
            <?= menuPageLink(appUrl('about.php'), 'About Me', true) ?>
        </nav>

        <div class="logout">
            <?= menuPageLink($logoutUrl, 'Logout') ?>
        </div>
    </aside>

<?php else: ?>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">SMK Muhammadiyah</div>
        </div>

        <div class="menu-title">Menu Utama</div>

        <nav class="menu">
            <?= menuPageLink($guruDashboard, 'Dashboard') ?>
            <?= menuPageLink($guruCatat, 'Catat Pelanggaran') ?>
            <?= menuPageLink($guruTindakan, 'Tindakan') ?>
            <?= menuPageLink($guruRiwayat, 'Riwayat') ?>
            <?= menuPageLink($guruRekap, 'Rekap Poin') ?>
            <?= menuPageLink(appUrl('about.php'), 'About Me', true) ?>
        </nav>

        <div class="logout">
            <?= menuPageLink($logoutUrl, 'Logout') ?>
        </div>
    </aside>

<?php endif; ?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<main class="main">

    <div class="mobile-header">
        <button
            type="button"
            class="menu-toggle"
            id="menuToggle"
            aria-label="Buka menu"
            aria-expanded="false"
        >☰</button>

        <div class="mobile-brand">Sistem Pelanggaran</div>
    </div>

    <header class="page-heading">
        <h1>About Me</h1>
        <p>Profil pengembang dan informasi proyek UKK.</p>
    </header>

    <section class="profile-card">
        <div class="profile-information">
            <div class="profile-label">Perkenalkan, saya</div>

            <h2>Nadia Zulfa Soraya</h2>

            <p class="profile-description">
                Siswi Rekayasa Perangkat Lunak yang mempelajari pemrograman,
                pengembangan website, dan perancangan aplikasi yang mudah digunakan.
            </p>

            <span class="school-label">
                SMK Muhammadiyah Kota Tasikmalaya
            </span>
        </div>

        <div class="photo-wrapper">
            <img
                src="<?= e($fotoUrl) ?>"
                alt="Foto pengembang"
                class="profile-photo"
                id="profilePhoto"
            >

            <div class="photo-status" id="photoStatus">
                Profil Pengembang
            </div>
        </div>
    </section>

    <div class="info-grid">

        <section class="info-card">
            <h3 class="section-title">Informasi Proyek</h3>

            <p class="section-subtitle">
                Gambaran aplikasi yang dikembangkan.
            </p>

            <div class="project-name">
                Sistem Informasi Pelanggaran Siswa
            </div>

            <p class="body-text">
                Aplikasi berbasis web untuk membantu pencatatan kejadian
                pelanggaran dan pemantauan kedisiplinan siswa di sekolah.
            </p>

            <hr class="divider">

            <div class="details-grid">
                <div>
                    <div class="detail-label">Platform</div>
                    <div class="detail-value">Website</div>
                </div>

                <div>
                    <div class="detail-label">Basis Data</div>
                    <div class="detail-value">MySQL</div>
                </div>

                <div>
                    <div class="detail-label">Pengguna</div>
                    <div class="detail-value">Admin, Guru, dan Wali Kelas</div>
                </div>

                <div>
                    <div class="detail-label">Bidang</div>
                    <div class="detail-value">Administrasi Sekolah</div>
                </div>
            </div>
        </section>

        <section class="info-card">
            <h3 class="section-title">Tujuan Proyek</h3>

            <p class="section-subtitle">
                Fungsi utama yang ingin dicapai.
            </p>

            <div class="goal-text body-text">
                Membantu guru dan admin mencatat serta memantau riwayat
                pelanggaran siswa dengan lebih teratur dan efisien.
            </div>

            <hr class="divider">

            <p class="body-text">
                Proyek ini menerapkan keterampilan pemrograman PHP Native,
                pengelolaan basis data, dan perancangan antarmuka website
                yang dipelajari di jurusan RPL.
            </p>
        </section>

    </div>

    <section class="technology-card">
        <div class="technology-heading">
            <h3>Teknologi yang Digunakan</h3>
            <span>Perangkat pengembangan aplikasi</span>
        </div>

        <div class="tech-list">
            <span class="tech-item" title="Struktur halaman website">HTML</span>
            <span class="tech-item" title="Tampilan dan tata letak">CSS</span>
            <span class="tech-item" title="Pemrosesan aplikasi">PHP Native</span>
            <span class="tech-item" title="Penyimpanan data">MySQL</span>
            <span class="tech-item" title="Komponen antarmuka">Bootstrap 5.3</span>
            <span class="tech-item" title="Interaksi halaman">JavaScript</span>
        </div>

        <button
            type="button"
            class="detail-button"
            id="detailButton"
            aria-expanded="false"
            aria-controls="techDetail"
        >
            Lihat penjelasan teknologi
        </button>

        <div class="tech-detail" id="techDetail">
            <strong>HTML</strong> membentuk struktur halaman.<br>
            <strong>CSS</strong> mengatur warna, ukuran, dan tata letak.<br>
            <strong>PHP Native</strong> menangani proses aplikasi di server.<br>
            <strong>MySQL</strong> menyimpan data siswa dan pelanggaran.<br>
            <strong>Bootstrap</strong> membantu menyusun komponen antarmuka.<br>
            <strong>JavaScript</strong> menambahkan interaksi pada halaman.
        </div>
    </section>

    <div class="footer-note">
        Sistem Informasi Pelanggaran Siswa
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');

    const detailButton = document.getElementById('detailButton');
    const techDetail = document.getElementById('techDetail');

    const profilePhoto = document.getElementById('profilePhoto');
    const photoStatus = document.getElementById('photoStatus');

    function closeSidebar() {
        if (!sidebar || !overlay || !menuToggle) return;

        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        menuToggle.setAttribute('aria-expanded', 'false');
    }

    if (sidebar && overlay && menuToggle) {
        menuToggle.addEventListener('click', function () {
            const isOpen = sidebar.classList.toggle('open');

            overlay.classList.toggle('show', isOpen);
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        });

        overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        sidebar.querySelectorAll('.menu-link').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });
    }

    if (detailButton && techDetail) {
        detailButton.addEventListener('click', function () {
            const isVisible = techDetail.classList.toggle('show');

            detailButton.setAttribute('aria-expanded', String(isVisible));

            detailButton.textContent = isVisible
                ? 'Sembunyikan penjelasan teknologi'
                : 'Lihat penjelasan teknologi';
        });
    }

    if (profilePhoto && photoStatus) {
        profilePhoto.addEventListener('error', function () {
            photoStatus.textContent = 'Foto profil tidak ditemukan';
            profilePhoto.style.display = 'none';
        });
    }
});
</script>

</body>
</html>
