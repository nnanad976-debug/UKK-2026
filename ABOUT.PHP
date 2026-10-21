<?php
include "middleware/auth.php";

$role = $_SESSION['role'] ?? '';
$foto_path = __DIR__ . '/assets/foto-nadia.png.png';
$foto_url = 'assets/foto-nadia.png.png';

if (is_file($foto_path)) {
    $foto_url .= '?v=' . filemtime($foto_path);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Me - Sistem Pelanggaran</title>
<style>
:root{
    --blue:#2878cf;
    --blue-dark:#203e68;
    --blue-soft:#edf5ff;
    --text:#405873;
    --muted:#7890ad;
    --line:#dce7f3;
    --page:#f5f8fc;
    --white:#fff;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:"Segoe UI",Arial,sans-serif;background:var(--page);color:var(--text);font-size:14px}
a{color:inherit;text-decoration:none}

/* Sidebar dipertahankan */
.sidebar{
    position:fixed;inset:0 auto 0 0;width:282px;height:100vh;overflow-y:auto;
    background:#fff;border-right:1px solid var(--line);padding:25px 14px 16px;
    display:flex;flex-direction:column;z-index:1000
}
.brand{padding:0 15px 22px;border-bottom:1px solid var(--line);margin-bottom:22px}
.brand-title{font-size:18px;font-weight:700;color:var(--blue-dark);margin-bottom:5px}
.brand-subtitle{font-size:12px;color:var(--muted)}
.menu-title{font-size:10px;font-weight:700;color:#9aabc0;letter-spacing:1px;text-transform:uppercase;margin:0 15px 10px}
.menu{display:flex;flex-direction:column;gap:3px}
.menu-link{min-height:43px;display:flex;align-items:center;padding:10px 15px;border-radius:10px;color:#5e7695;font-size:13px;transition:background .2s ease,color .2s ease}
.menu-link:hover,.menu-link.active{background:var(--blue-soft);color:var(--blue)}
.menu-link.active{font-weight:600;box-shadow:inset 4px 0 0 var(--blue)}
.logout{margin-top:auto;padding-top:14px;border-top:1px solid var(--line)}
.logout .menu-link{color:#d9534f}
.logout .menu-link:hover{background:#fff0ef;color:#c03935}

/* Main content */
.main{margin-left:282px;min-height:100vh;background:var(--page)}
.content{max-width:1500px;margin:0 auto;padding:28px 34px 34px}
.page-heading{margin-bottom:22px}
.page-kicker{font-size:10px;letter-spacing:1.7px;text-transform:uppercase;color:var(--blue);font-weight:800;margin-bottom:6px}
.page-heading h1{font-size:29px;line-height:1.2;letter-spacing:-.5px;color:var(--blue-dark);font-weight:750}
.page-heading p{font-size:12px;color:var(--muted);margin-top:6px}
.profile-layout{display:grid;grid-template-columns:255px minmax(0,1fr);gap:30px;align-items:start}
.profile-rail{min-width:0;background:#fff;border:1px solid var(--line);padding:14px}
.portrait{width:100%;aspect-ratio:1/1;overflow:hidden;background:#e9f0f8;border:1px solid #d9e5f3;border-radius:0}
.portrait img{display:block;width:100%;height:100%;object-fit:cover;object-position:center 16%;border-radius:0}
.portrait-fallback{width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:12px;text-align:center;color:#7188a4;font-size:12px;background:#edf3fa}
.name-panel{padding:14px 2px 13px;border-bottom:1px solid var(--line)}
.name-panel .label{font-size:9px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase;color:var(--blue);margin-bottom:5px}
.name-panel h2{font-size:20px;line-height:1.25;letter-spacing:-.35px;color:var(--blue-dark)}
.name-panel p{font-size:11px;line-height:1.5;color:var(--muted);margin-top:5px}
.tech-block{padding:14px 2px 2px}
.tech-heading{font-size:13px;font-weight:750;color:var(--blue-dark);margin-bottom:10px}
.tech-names{display:flex;flex-wrap:wrap;gap:7px}
.tech-names span{display:inline-flex;align-items:center;justify-content:center;min-width:42px;padding:7px 10px;border:1px solid #d9e7f8;background:#f4f8fe;color:#2c67a7;font-size:11px;font-weight:700}
.profile-main{min-width:0;padding:5px 0 0}
.about-section{padding:8px 0 21px;border-bottom:1px solid var(--line)}
.section-label{font-size:10px;letter-spacing:1.7px;text-transform:uppercase;color:var(--blue);font-weight:800;margin-bottom:9px}
.intro-title{font-size:clamp(26px,2.4vw,35px);line-height:1.16;letter-spacing:-.85px;color:var(--blue-dark);font-weight:760;max-width:740px;margin-bottom:12px}
.intro-copy{font-size:13px;line-height:1.85;color:#5c7390;max-width:780px}
.project-section{padding:20px 0 18px;border-bottom:1px solid var(--line)}
.section-heading{font-size:17px;line-height:1.35;color:var(--blue-dark);font-weight:750;margin-bottom:13px}
.project-rows{display:grid;gap:0}
.project-row{display:grid;grid-template-columns:95px minmax(0,1fr);gap:14px;padding:10px 0;border-bottom:1px solid #e8eef6}
.project-row:last-child{border-bottom:0;padding-bottom:2px}
.row-label{font-size:10px;letter-spacing:.9px;text-transform:uppercase;color:#8196af;font-weight:700;padding-top:2px}
.row-content strong{display:block;color:#2d4b70;font-size:12px;font-weight:700;margin-bottom:3px}
.row-content p{font-size:11.5px;line-height:1.65;color:#71849b}
.goal-section{padding:19px 0 0}
.goal-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px}
.goal-item{padding:3px 0 3px 13px;border-left:2px solid #c8defa}
.goal-item span{display:block;font-size:10px;color:#8196af;margin-bottom:5px}
.goal-item strong{font-size:12px;line-height:1.55;color:#2d4b70;font-weight:700}
@media(max-width:1050px){
    .content{padding:24px}
    .profile-layout{grid-template-columns:225px minmax(0,1fr);gap:23px}
    .intro-title{font-size:29px}
}
@media(max-width:900px){
    .sidebar{position:relative;width:100%;height:auto;min-height:auto}
    .main{margin-left:0}
    .logout{margin-top:14px}
}
@media(max-width:650px){
    .content{padding:20px 16px 26px}
    .page-heading h1{font-size:25px}
    .profile-layout{grid-template-columns:165px minmax(0,1fr);gap:17px}
    .profile-rail{padding:10px}
    .name-panel h2{font-size:16px}
    .name-panel p{font-size:10px}
    .tech-heading{font-size:12px}
    .tech-names{gap:5px}
    .tech-names span{min-width:35px;padding:6px 7px;font-size:10px}
    .intro-title{font-size:24px}
    .intro-copy{font-size:12px}
    .project-row{grid-template-columns:73px minmax(0,1fr);gap:9px}
    .goal-grid{gap:14px}
}
@media(max-width:500px){
    .profile-layout{grid-template-columns:1fr}
    .profile-rail{display:grid;grid-template-columns:105px minmax(0,1fr);column-gap:12px;align-items:start;padding:12px}
    .portrait{width:105px;grid-row:span 2}
    .name-panel{padding:5px 0 10px;border-bottom:1px solid var(--line)}
    .name-panel h2{font-size:16px}
    .tech-block{grid-column:1 / -1;padding-top:12px}
    .profile-main{padding-top:0}
    .about-section{padding-top:0}
    .intro-title{font-size:27px}
    .goal-grid{grid-template-columns:1fr;gap:12px}
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
        <a href="dashboard.php" class="menu-link">Dashboard</a>
        <?php if ($role === 'admin') : ?>
            <a href="admin/guru.php" class="menu-link">Kelola Guru</a>
            <a href="admin/siswa.php" class="menu-link">Kelola Siswa</a>
            <a href="admin/kelas.php" class="menu-link">Kelola Kelas</a>
            <a href="admin/tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
            <a href="admin/penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
            <a href="admin/wali_kelas.php" class="menu-link">Wali Kelas</a>
            <a href="admin/kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
            <a href="admin/jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
            <a href="admin/cetak_export.php" class="menu-link">Catat / Export</a>
        <?php else : ?>
            <a href="guru/catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
            <a href="guru/tindakan.php" class="menu-link">Tindakan</a>
            <a href="guru/laporan.php" class="menu-link">Laporan</a>
            <a href="guru/riwayat.php" class="menu-link">Riwayat</a>
        <?php endif; ?>
        <a href="about.php" class="menu-link active" aria-current="page">About Me</a>
    </nav>
    <div class="logout"><a href="logout.php" class="menu-link">Logout</a></div>
</aside>

<main class="main">
    <div class="content">
        <header class="page-heading">
            <div class="page-kicker">Profil</div>
            <h1>Tentang Saya</h1>
            <p>Rekayasa Perangkat Lunak · SMK Muhammadiyah Kota Tasikmalaya</p>
        </header>

        <section class="profile-layout" aria-label="Profil Nadia Zulfa Soraya">
            <aside class="profile-rail">
                <div class="portrait">
                    <?php if (is_file($foto_path)) : ?>
                        <img src="<?= htmlspecialchars($foto_url, ENT_QUOTES, 'UTF-8') ?>" alt="Foto Nadia Zulfa Soraya" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="portrait-fallback" style="display:none">Foto tidak tersedia.</div>
                    <?php else : ?>
                        <div class="portrait-fallback">Foto tidak tersedia.</div>
                    <?php endif; ?>
                </div>
                <div class="name-panel">
                    <div class="label">Nama</div>
                    <h2>Nadia Zulfa Soraya</h2>
                    <p>Siswi Rekayasa Perangkat Lunak</p>
                </div>
                <section class="tech-block" aria-labelledby="tech-heading">
                    <h2 class="tech-heading" id="tech-heading">Bahasa Pemrograman</h2>
                    <div class="tech-names">
                        <span>HTML</span><span>CSS</span><span>PHP</span><span>SQL</span>
                    </div>
                </section>
            </aside>

            <div class="profile-main">
                <section class="about-section" aria-labelledby="about-heading">
                    <div class="section-label">About Me</div>
                    <h2 class="intro-title" id="about-heading">Belajar merancang solusi digital yang berguna.</h2>
                    <p class="intro-copy">Saya tertarik pada pengembangan website, pengelolaan data, dan tampilan yang mudah digunakan. Melalui Sistem Pelanggaran Sekolah, saya menerapkan pembelajaran Rekayasa Perangkat Lunak dalam sebuah aplikasi.</p>
                </section>

                <section class="project-section" aria-labelledby="project-heading">
                    <h2 class="section-heading" id="project-heading">Informasi Proyek</h2>
                    <div class="project-rows">
                        <div class="project-row">
                            <div class="row-label">Aplikasi</div>
                            <div class="row-content">
                                <strong>Sistem Pelanggaran Sekolah</strong>
                                <p>Website untuk pencatatan dan pengelolaan data pelanggaran siswa.</p>
                            </div>
                        </div>
                        <div class="project-row">
                            <div class="row-label">Jenis</div>
                            <div class="row-content">
                                <strong>Aplikasi berbasis web</strong>
                                <p>Dibangun dengan halaman antarmuka dan basis data.</p>
                            </div>
                        </div>
                        <div class="project-row">
                            <div class="row-label">Fokus</div>
                            <div class="row-content">
                                <strong>Pengelolaan data pelanggaran</strong>
                                <p>Membuat pencatatan dan pencarian data lebih teratur.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="goal-section" aria-labelledby="goal-heading">
                    <h2 class="section-heading" id="goal-heading">Tujuan Proyek</h2>
                    <div class="goal-grid">
                        <div class="goal-item">
                            <span>Pengembangan</span>
                            <strong>Aplikasi berbasis web</strong>
                        </div>
                        <div class="goal-item">
                            <span>Tujuan</span>
                            <strong>Mempermudah pengelolaan data</strong>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</main>
</body>
</html>
  