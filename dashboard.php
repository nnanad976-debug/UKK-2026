<?php
include __DIR__ . "/middleware/auth.php";

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'User';
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
            --page: #f6f9fd;
            --white: #ffffff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32, 62, 104, .07);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--page);
            color: var(--text);
        }

        a {
            text-decoration: none;
        }

        /* LAYOUT */
        .layout {
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 282px;
            height: 100vh;
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

        /* MAIN */
        .main {
            margin-left: 282px;
            min-height: 100vh;
            padding: 24px 28px 40px;
        }

        /* TOPBAR */
        .topbar {
            background: linear-gradient(
                135deg,
                #f4f9ff 0%,
                #edf6ff 100%
            );
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
            box-shadow: var(--shadow);
        }

        .welcome h2 {
            font-size: 25px;
            color: var(--blue-dark);
            margin-bottom: 6px;
        }

        .welcome p {
            font-size: 13px;
            color: var(--muted);
        }

        .user-box {
            display: flex;
            align-items: center;
            color: var(--blue-dark);
            font-size: 13px;
            font-weight: 600;
            background: rgba(255,255,255,.65);
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 8px 15px;
        }

        .user-name {
            white-space: nowrap;
        }

        /* PAGE HEADER */
        .page-head {
            margin: 4px 6px 18px;
        }

        .page-head h3 {
            font-size: 28px;
            color: var(--blue-dark);
            margin-bottom: 5px;
        }

        .page-head p {
            color: var(--muted);
            font-size: 13px;
        }

        /* DASHBOARD 3 KOTAK */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            min-height: 175px;
            padding: 22px;
            box-shadow: var(--shadow);
            transition: .2s ease;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(32, 62, 104, .10);
        }

        .card-content {
            flex: 1;
        }

        .card-content h4 {
            font-size: 18px;
            color: var(--blue-dark);
            margin-bottom: 8px;
        }

        .card-content p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6;
        }

        .card-button {
            align-self: flex-start;
            margin-top: 18px;
            background: var(--blue);
            color: white;
            font-size: 12px;
            font-weight: 600;
            border-radius: 9px;
            padding: 9px 17px;
            transition: .2s ease;
        }

        .card-button:hover {
            background: #1f68b6;
            color: white;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 28px;
            color: #9aabc0;
            font-size: 10px;
        }

        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 650px) {
            .main {
                padding: 18px 14px 30px;
            }

            .topbar {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-title">
                Sistem Pelanggaran
            </div>

            <div class="brand-subtitle">
                SMK Muhammadiyah
            </div>
        </div>

        <div class="menu-title">
            Menu Utama
        </div>

        <nav class="menu">

            <a href="dashboard.php" class="menu-link active">
                Dashboard
            </a>

            <?php if ($role === 'admin'): ?>

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

            <?php else: ?>

                <a href="guru/catat_pelanggaran.php" class="menu-link">
                    Catat Pelanggaran
                </a>

                <a href="guru/tindakan.php" class="menu-link">
                    Tindakan
                </a>


                <a href="guru/riwayat.php" class="menu-link">
                    Riwayat
                </a>

            <?php endif; ?>

            <a href="about.php" class="menu-link">
                About Me
            </a>

        </nav>

        <div class="logout">
            <a href="logout.php" class="menu-link">
                Logout
            </a>
        </div>

    </aside>


    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <section class="topbar">

            <div class="welcome">
                <h2>
                    Selamat datang,
                    <?= htmlspecialchars($nama); ?>
                </h2>

                <p>
                    Kelola data sekolah dengan mudah dan terstruktur.
                </p>
            </div>

            <div class="user-box">
                <span class="user-name">
                    <?= htmlspecialchars($nama); ?>
                </span>
            </div>

        </section>


        <!-- PAGE HEADER -->
        <div class="page-head">

            <h3>
                Dashboard
            </h3>

            <p>
                Akses fitur sesuai dengan hak akses akun Anda.
            </p>

        </div>


        <!-- DASHBOARD -->
        <section class="dashboard-grid">

            <?php if ($role === 'admin'): ?>

                <!-- KOTAK 1 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Kelola Data
                        </h4>

                        <p>
                            Kelola data guru, siswa, kelas,
                            tahun ajaran, penempatan siswa,
                            dan wali kelas.
                        </p>
                    </div>

                    <a href="admin/guru.php" class="card-button">
                        Buka
                    </a>

                </div>


                <!-- KOTAK 2 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Data Pelanggaran
                        </h4>

                        <p>
                            Kelola kategori dan jenis
                            pelanggaran siswa.
                        </p>
                    </div>

                    <a href="admin/kategori_pelanggaran.php" class="card-button">
                        Buka
                    </a>

                </div>


                <!-- KOTAK 3 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Cetak / Export
                        </h4>

                        <p>
                            Cetak dan export data
                            pelanggaran sekolah.
                        </p>
                    </div>

                    <a href="admin/cetak_export.php" class="card-button">
                        Buka
                    </a>

                </div>


            <?php else: ?>

                <!-- KOTAK 1 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Catat Pelanggaran
                        </h4>

                        <p>
                            Catat pelanggaran siswa
                            dengan mudah dan terstruktur.
                        </p>
                    </div>

                    <a href="guru/catat_pelanggaran.php" class="card-button">
                        Buka
                    </a>

                </div>


                <!-- KOTAK 2 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Tindakan
                        </h4>

                        <p>
                            Kelola tindakan yang diberikan
                            terhadap pelanggaran siswa.
                        </p>
                    </div>

                    <a href="guru/tindakan.php" class="card-button">
                        Buka
                    </a>

                </div>


                <!-- KOTAK 3 -->
                <div class="card">

                    <div class="card-content">
                        <h4>
                            Laporan & Riwayat
                        </h4>

                        <p>
                            Lihat laporan dan riwayat
                            pelanggaran siswa.
                        </p>
                    </div>

                    <a href="guru/laporan.php" class="card-button">
                        Buka
                    </a>

                </div>

            <?php endif; ?>

        </section>


        <div class="footer">
            Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
        </div>

    </main>

</div>

</body>
</html>