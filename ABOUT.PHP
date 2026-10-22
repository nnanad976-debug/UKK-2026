```php
<?php
include "middleware/auth.php";

$role = strtolower($_SESSION['role'] ?? 'admin');
$namaPengguna = $_SESSION['nama'] ?? $_SESSION['name'] ?? 'Pengguna';

$fotoFile = __DIR__ . '/assets/foto-nadia.png.png';
$fotoUrl = 'assets/foto-nadia.png.png';

if (file_exists($fotoFile)) {
    $fotoUrl .= '?v=' . filemtime($fotoFile);
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me | Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --white: #ffffff;
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

        /* SIDEBAR - MENGIKUTI TAMPILAN HALAMAN GURU */
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
            min-height: 43px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: #5e7695;
            font-size: 13px;
            transition: background .2s ease, color .2s ease,
                        padding-left .2s ease;
        }

        .menu-link:hover {
            background: var(--blue-soft);
            color: var(--blue);
            padding-left: 19px;
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
            padding: 30px;
        }

        .mobile-header {
            display: none;
        }

        .page-heading {
            margin-bottom: 24px;
            opacity: 0;
            transform: translateY(12px);
            animation: muncul .6s ease forwards;
        }

        .page-heading h1 {
            font-size: 29px;
            font-weight: 700;
            color: #234e78;
            margin-bottom: 7px;
        }

        .page-heading p {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
        }

        .profile-card,
        .info-card,
        .technology-card {
            background: var(--white);
            border: 1px solid #e1eaf4;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(35, 78, 120, .04);
            transition: transform .25s ease, box-shadow .25s ease;
            opacity: 0;
            animation: muncul .65s ease forwards;
        }

        .profile-card:hover,
        .info-card:hover,
        .technology-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(35, 78, 120, .09);
        }

        .profile-card {
            padding: 32px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            margin-bottom: 18px;
            animation-delay: .08s;
        }

        .profile-label {
            color: var(--blue);
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .profile-card h2 {
            font-size: 35px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 13px;
        }

        .profile-description {
            color: #71819a;
            font-size: 13px;
            line-height: 1.9;
            max-width: 620px;
            margin-bottom: 12px;
        }

        .school-label {
            display: inline-block;
            background: #edf4fc;
            color: #285b91;
            font-size: 11px;
            padding: 9px 12px;
            border-radius: 4px;
        }

        .photo-wrapper {
            flex-shrink: 0;
            position: relative;
        }

        .profile-photo {
            width: 175px;
            height: 198px;
            object-fit: cover;
            object-position: center top;
            border-radius: 4px;
            display: block;
            transition: filter .25s ease, transform .25s ease;
        }

        .profile-photo:hover {
            filter: brightness(1.04);
            transform: scale(1.02);
        }

        .photo-status {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
            color: var(--muted);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1.1fr .8fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .info-card {
            padding: 25px;
            animation-delay: .18s;
        }

        .info-grid .info-card:nth-child(2) {
            animation-delay: .25s;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #18385f;
            margin-bottom: 7px;
        }

        .section-subtitle {
            font-size: 11px;
            color: #8592a7;
            margin-bottom: 24px;
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
            border-top: 1px solid #e4eaf2;
            margin: 17px 0;
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
            border-left: 2px solid #80b5f6;
            padding: 1px 0 1px 13px;
            margin-bottom: 16px;
        }

        .technology-card {
            padding: 23px 25px;
            animation-delay: .32s;
        }

        .technology-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 17px;
        }

        .technology-heading h3 {
            font-size: 15px;
            font-weight: 700;
            color: #18385f;
            margin: 0;
        }

        .technology-heading span {
            font-size: 10px;
            color: #8592a7;
        }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tech-item {
            padding: 9px 14px;
            border: 1px solid #dce6f2;
            border-radius: 4px;
            color: #34577f;
            font-size: 11px;
            background: #fbfcfe;
            transition: background .2s ease, color .2s ease,
                        border-color .2s ease, transform .2s ease;
            cursor: default;
        }

        .tech-item:hover {
            background: var(--blue);
            border-color: var(--blue);
            color: white;
            transform: translateY(-2px);
        }

        .tech-detail {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-left: 3px solid var(--blue);
            background: #f5f9ff;
            border-radius: 4px;
            color: #58718e;
            font-size: 12px;
            line-height: 1.8;
        }

        .tech-detail.show {
            display: block;
            animation: muncul .25s ease;
        }

        .detail-button {
            margin-top: 16px;
            background: #edf4fc;
            color: #285b91;
            border: 1px solid #dce9f8;
            border-radius: 5px;
            padding: 9px 13px;
            font-size: 12px;
            transition: .2s ease;
        }

        .detail-button:hover {
            background: var(--blue);
            color: #fff;
        }

        .footer-note {
            text-align: right;
            color: #9aa8ba;
            font-size: 10px;
            margin-top: 17px;
        }

        @keyframes muncul {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1050px) {
            .main {
                padding: 24px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
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
                padding: 18px 15px 28px;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 25px;
            }

            .menu-toggle {
                width: 40px;
                height: 40px;
                border: 1px solid var(--line);
                background: white;
                color: var(--blue-dark);
                border-radius: 7px;
                font-size: 20px;
                cursor: pointer;
            }

            .mobile-brand {
                font-size: 14px;
                font-weight: 700;
                color: var(--blue-dark);
            }

            .profile-card {
                padding: 23px;
                gap: 20px;
            }

            .profile-card h2 {
                font-size: 28px;
            }

            .profile-photo {
                width: 135px;
                height: 155px;
            }
        }

        @media (max-width: 500px) {
            .profile-card {
                flex-direction: column-reverse;
                align-items: flex-start;
            }

            .profile-card h2 {
                font-size: 26px;
            }

            .profile-photo {
                width: 125px;
                height: 145px;
            }

            .info-card,
            .technology-card {
                padding: 20px;
            }

            .technology-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-heading h1 {
                font-size: 25px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
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
        <a href="dashboard.php" class="menu-link">Dashboard</a>
        <a href="admin/guru.php" class="menu-link">Kelola Guru</a>
        <a href="admin/siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="admin/kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="admin/tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="admin/penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="admin/wali_kelas.php" class="menu-link">Wali Kelas</a>
        <a href="admin/kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="admin/jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="admin/cetak_export.php" class="menu-link">Cetak / Export</a>
        <a href="about.php" class="menu-link active">About Me</a>
    </nav>

    <div class="logout">
        <a href="logout.php" class="menu-link">Logout</a>
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
        <a href="guru/dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru/catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
        <a href="guru/tindakan.php" class="menu-link">Tindakan</a>
        <a href="guru/laporan.php" class="menu-link">Laporan</a>
        <a href="guru/riwayat.php" class="menu-link">Riwayat Pelanggaran</a>
        <a href="about.php" class="menu-link active">About Me</a>
    </nav>

    <div class="logout">
        <a href="logout.php" class="menu-link">Logout</a>
    </div>
</aside>
<?php endif; ?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<main class="main">

    <div class="mobile-header">
        <button type="button" class="menu-toggle" id="menuToggle"
                aria-label="Buka menu" aria-expanded="false">☰</button>
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
            <div class="photo-status" id="photoStatus">Profil Pengembang</div>
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
                    <div class="detail-value">Admin dan Guru</div>
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

    // Tandai menu aktif berdasarkan alamat halaman saat ini.
    const currentPage