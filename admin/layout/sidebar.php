
<?php
$halamanAktifSidebar = basename($_SERVER['PHP_SELF'] ?? '');
?>

<div class="brand">
    <div class="brand-title">Sistem Pelanggaran</div>
    <div class="brand-subtitle">SMK Muhammadiyah</div>
</div>

<div class="menu-title">Menu Utama</div>

<nav class="menu">

    <a href="../dashboard.php"
       class="menu-link <?= $halamanAktifSidebar === 'dashboard.php' ? 'active' : ''; ?>">
        Dashboard
    </a>

    <a href="guru.php"
       class="menu-link <?= $halamanAktifSidebar === 'guru.php' ? 'active' : ''; ?>">
        Kelola Guru
    </a>

    <a href="siswa.php"
       class="menu-link <?= $halamanAktifSidebar === 'siswa.php' ? 'active' : ''; ?>">
        Kelola Siswa
    </a>

    <a href="kelas.php"
       class="menu-link <?= $halamanAktifSidebar === 'kelas.php' ? 'active' : ''; ?>">
        Kelola Kelas
    </a>

    <a href="tahun_ajaran.php"
       class="menu-link <?= $halamanAktifSidebar === 'tahun_ajaran.php' ? 'active' : ''; ?>">
        Tahun Ajaran
    </a>

    <a href="penempatan_siswa.php"
       class="menu-link <?= $halamanAktifSidebar === 'penempatan_siswa.php' ? 'active' : ''; ?>">
        Penempatan Siswa
    </a>

    <a href="wali_kelas.php"
       class="menu-link <?= $halamanAktifSidebar === 'wali_kelas.php' ? 'active' : ''; ?>">
        Wali Kelas
    </a>

    <a href="kategori_pelanggaran.php"
       class="menu-link <?= $halamanAktifSidebar === 'kategori_pelanggaran.php' ? 'active' : ''; ?>">
        Kategori Pelanggaran
    </a>

    <a href="jenis_pelanggaran.php"
       class="menu-link <?= $halamanAktifSidebar === 'jenis_pelanggaran.php' ? 'active' : ''; ?>">
        Jenis Pelanggaran
    </a>

    <a href="cetak_export.php"
       class="menu-link <?= $halamanAktifSidebar === 'cetak_export.php' ? 'active' : ''; ?>">
        Laporan
    </a>

    <a href="../about.php"
       class="menu-link <?= $halamanAktifSidebar === 'about.php' ? 'active' : ''; ?>">
        About Me
    </a>

</nav>

<div class="logout">
    <a href="../logout.php" class="menu-link">Logout</a>
</div>
