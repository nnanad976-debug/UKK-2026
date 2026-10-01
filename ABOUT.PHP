<?php
include "middleware/auth.php";

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me - Sistem Pelanggaran</title>

    <style>

        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --page: #f6f9fd;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--page);
            color: var(--text);
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 214px;
            height: 100vh;

            background: white;
            border-right: 1px solid var(--line);

            padding: 23px 14px;

            display: flex;
            flex-direction: column;

            overflow-y: auto;
        }

        .brand {
            padding: 0 9px 20px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 20px;
        }

        .brand h5 {
            font-size: 14px;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .brand small {
            font-size: 11px;
            color: var(--muted);
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #9aabc0;
            text-transform: uppercase;

            margin: 10px 9px 8px;
            letter-spacing: .4px;
        }

        .menu-link {
            display: block;

            padding: 9px 10px;
            margin-bottom: 3px;

            border-radius: 7px;

            color: var(--text);
            text-decoration: none;

            font-size: 12px;

            transition: .2s;
        }

        .menu-link:hover {
            background: #edf5ff;
            color: var(--blue);
        }

        .menu-link.active {
            background: #e9f3ff;
            color: var(--blue);
            font-weight: 600;
        }

        .logout {
            margin-top: auto;

            padding-top: 14px;

            border-top: 1px solid var(--line);
        }

        .logout a {
            display: block;

            padding: 9px 10px;

            color: #d9534f;
            text-decoration: none;

            font-size: 12px;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 214px;
            min-height: 100vh;

            padding: 35px 45px;
        }

        .topbar {
            margin-bottom: 28px;
        }

        .topbar h3 {
            font-size: 20px;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .topbar p {
            font-size: 13px;
            color: var(--muted);
        }

        /* =========================
           ABOUT SECTION
        ========================= */

        .about-section {
            max-width: 1120px;
            margin: 0 auto;

            min-height: 590px;

            background: white;

            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);

            padding: 65px 70px;

            display: grid;
            grid-template-columns: 1fr 300px;

            align-items: center;

            column-gap: 70px;
        }

        /* =========================
           TEXT
        ========================= */

        .about-text {
            max-width: 650px;
        }

        .small-title {
            font-size: 11px;
            font-weight: 700;

            color: var(--blue);

            letter-spacing: 2px;

            margin-bottom: 15px;
        }

        .about-title {
            font-size: 64px;
            line-height: .95;

            color: var(--blue-dark);

            font-weight: 800;

            letter-spacing: -3px;

            margin-bottom: 25px;
        }

        .about-title span {
            color: var(--blue);
        }

        .intro {
            font-size: 17px;
            line-height: 1.65;

            color: var(--blue-dark);

            font-weight: 600;

            max-width: 610px;

            margin-bottom: 20px;
        }

        .description {
            font-size: 13px;
            line-height: 1.8;

            color: #687f9c;

            max-width: 620px;

            margin-bottom: 12px;
        }

        /* =========================
           DATA DIRI
        ========================= */

        .profile-info {
            margin-top: 27px;

            display: grid;

            grid-template-columns:
                120px
                1fr;

            row-gap: 9px;

            max-width: 520px;

            border-top: 1px solid var(--line);

            padding-top: 20px;
        }

        .profile-info .label {
            font-size: 11px;
            color: #91a3b9;
        }

        .profile-info .value {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
        }

        /* =========================
           FOTO
        ========================= */

        .photo-area {
            display: flex;

            justify-content: center;
            align-items: center;
        }

        .photo-frame {
            width: 220px;
            height: 220px;

            border-radius: 50%;

            overflow: hidden;

            border: 7px solid white;

            box-shadow:
                0 0 0 1px var(--line),
                0 12px 30px rgba(32, 62, 104, .12);

            background: #edf5ff;
        }

        .photo-frame img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            object-position: center top;

            display: block;
        }

        /* =========================
           FOOTER TEXT
        ========================= */

        .bottom-text {
            margin-top: 22px;

            font-size: 11px;

            color: #9aabc0;

            text-align: center;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 950px) {

            .about-section {
                grid-template-columns: 1fr;
                text-align: center;

                padding: 50px 35px;

                row-gap: 45px;
            }

            .about-text {
                max-width: 100%;
            }

            .intro,
            .description {
                margin-left: auto;
                margin-right: auto;
            }

            .profile-info {
                margin-left: auto;
                margin-right: auto;

                text-align: left;
            }

            .photo-area {
                order: -1;
            }

        }

        @media (max-width: 768px) {

            .sidebar {
                position: relative;

                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;

                padding: 25px 20px;
            }

            .about-section {
                padding: 40px 25px;
            }

            .about-title {
                font-size: 48px;
            }

            .photo-frame {
                width: 180px;
                height: 180px;
            }

        }

    </style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="brand">

        <h5>
            Sistem Pelanggaran
        </h5>

        <small>
            SMK Muhammadiyah
        </small>

    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <a href="dashboard.php" class="menu-link">
        Dashboard
    </a>


    <?php if ($role == 'admin') : ?>

        <a href="admin/guru.php" class="menu-link">
            Kelola Guru
        </a>

        <a href="admin/siswa.php" class="menu-link">
            Kelola Siswa
        </a>

        <a href="admin/kelas.php" class="menu-link">
            Kelola Kelas
        </a>

        <a href="admin/tahun_ajaran.php" class="menu-link">
            Tahun Ajaran
        </a>

        <a href="admin/penempatan_siswa.php" class="menu-link">
            Penempatan Siswa
        </a>

        <a href="admin/wali_kelas.php" class="menu-link">
            Wali Kelas
        </a>

        <a href="admin/kategori_pelanggaran.php" class="menu-link">
            Kategori Pelanggaran
        </a>

        <a href="admin/jenis_pelanggaran.php" class="menu-link">
            Jenis Pelanggaran
        </a>

        <a href="admin/cetak_export.php" class="menu-link">
            Cetak / Export
        </a>

    <?php else : ?>

        <a href="guru/catat_pelanggaran.php" class="menu-link">
            Catat Pelanggaran
        </a>

        <a href="guru/tindakan.php" class="menu-link">
            Tindakan
        </a>

        <a href="guru/laporan.php" class="menu-link">
            Laporan
        </a>

        <a href="guru/riwayat.php" class="menu-link">
            Riwayat
        </a>

    <?php endif; ?>


    <a href="about.php" class="menu-link active">
        About Me
    </a>


    <div class="logout">

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<!-- =========================
     MAIN
========================= -->

<div class="main">


    <div class="topbar">

        <h3>
            About Me
        </h3>

        <p>
            Mengenal lebih dekat pengembang Sistem Pelanggaran
        </p>

    </div>


    <!-- =========================
         ABOUT
    ========================= -->

    <section class="about-section">


        <!-- TEXT -->

        <div class="about-text">

            <div class="small-title">
                PROFILE
            </div>


            <h1 class="about-title">
                ABOUT<br>
                <span>ME.</span>
            </h1>


            <p class="intro">
                Saya adalah seorang siswi yang memiliki ketertarikan
                pada dunia teknologi, khususnya dalam pengembangan
                website dan sistem informasi.
            </p>


            <p class="description">
                Halo, saya <strong>Nadia Zulfa</strong>, siswi
                SMK Muhammadiyah Kota Tasikmalaya dari jurusan
                Rekayasa Perangkat Lunak. Saya sedang mempelajari
                bagaimana sebuah sistem dapat dirancang agar
                memiliki tampilan yang sederhana, terstruktur,
                dan mudah digunakan.
            </p>


            <p class="description">
                Dalam proses pembelajaran, saya mempelajari berbagai
                teknologi seperti HTML, CSS, PHP, JavaScript,
                serta database MySQL. Saya juga belajar memahami
                bagaimana membuat sebuah website mulai dari
                perancangan database, proses login, pengelolaan
                data, hingga penyajian informasi kepada pengguna.
            </p>


            <p class="description">
                Sistem Pelanggaran ini merupakan salah satu project
                pembelajaran yang saya kembangkan sebagai penerapan
                dari ilmu Rekayasa Perangkat Lunak. Melalui project
                ini, saya belajar menggabungkan kemampuan pemrograman,
                pengelolaan database, dan perancangan antarmuka
                menjadi sebuah sistem yang dapat digunakan untuk
                membantu pengelolaan data pelanggaran di sekolah.
            </p>


            <!-- DATA DIRI -->

            <div class="profile-info">

                <div class="label">
                    Nama
                </div>

                <div class="value">
                    Nadia Zulfa
                </div>


                <div class="label">
                    Sekolah
                </div>

                <div class="value">
                    SMK Muhammadiyah Kota Tasikmalaya
                </div>


                <div class="label">
                    Jurusan
                </div>

                <div class="value">
                    Rekayasa Perangkat Lunak
                </div>


                <div class="label">
                    Project
                </div>

                <div class="value">
                    Sistem Pelanggaran Sekolah
                </div>

            </div>

        </div>


        <!-- FOTO -->

        <div class="photo-area">

            <div class="photo-frame">

                <img
                    src="assets/foto-nadia.png.png"
                    alt="Foto Nadia"
                >

            </div>

        </div>


    </section>


    <div class="bottom-text">
        Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
    </div>


</div>


</body>
</html>