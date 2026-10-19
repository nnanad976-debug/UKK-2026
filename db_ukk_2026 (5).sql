-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Okt 2026 pada 09.42
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '197010101001', 'Andi Setiawan, S.Pd.', 'guru1@sekolah.sch.id', 1, 1, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(2, '197020101002', 'Siti Nurhaliza, S.Pd.', 'guru2@sekolah.sch.id', 1, 2, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(3, '197030101003', 'Dedi Kurniawan, S.Pd.', 'guru3@sekolah.sch.id', 1, 3, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(4, '197040101004', 'Rina Marlina, M.Pd.', 'guru4@sekolah.sch.id', 1, 4, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(5, '197050101005', 'Budi Hartono, S.Pd.', 'guru5@sekolah.sch.id', 1, 5, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(6, '197060101006', 'Dewi Anggraini, S.Pd.', 'guru6@sekolah.sch.id', 1, 6, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(7, '197070101007', 'Ahmad Fauzi, S.Pd.I.', 'guru7@sekolah.sch.id', 1, 7, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(8, '197080101008', 'Nina Herlina, S.Pd.', 'guru8@sekolah.sch.id', 1, 8, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(9, '197090101009', 'Agus Setiawan, M.Pd.', 'guru9@sekolah.sch.id', 1, 9, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(10, '197100101010', 'Fitri Handayani, S.Pd.', 'guru10@sekolah.sch.id', 1, 10, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(11, '197110101011', 'Hendra Wijaya, S.Kom.', 'guru11@sekolah.sch.id', 1, 11, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(12, '197120101012', 'Yuni Kartika, S.Pd.', 'guru12@sekolah.sch.id', 1, 12, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(13, '197130101013', 'Rudi Hermawan, M.Pd.', 'guru13@sekolah.sch.id', 1, 13, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(14, '197140101014', 'Lilis Suryani, S.Pd.', 'guru14@sekolah.sch.id', 1, 14, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(15, '197150101015', 'Dadan Supriatna, S.Pd.', 'guru15@sekolah.sch.id', 1, 15, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(16, '197160101016', 'Maya Puspita, M.Pd.', 'guru16@sekolah.sch.id', 1, 16, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(17, '197170101017', 'Rizal Maulana, S.Pd.', 'guru17@sekolah.sch.id', 1, 17, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(18, '197180101018', 'Tuti Alawiyah, S.Pd.I.', 'guru18@sekolah.sch.id', 1, 18, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(19, '197190101019', 'Asep Mulyana, M.Pd.', 'guru19@sekolah.sch.id', 1, 19, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(20, '197200101020', 'Nia Kurniasih, S.Pd.', 'guru20@sekolah.sch.id', 1, 20, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(21, '197210101021', 'Drs. Rahmat Hidayat, M.Pd.', 'guru21@sekolah.sch.id', 1, 21, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(22, '197220101022', 'Sri Wahyuni, S.Pd.', 'guru22@sekolah.sch.id', 1, 22, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(23, '197230101023', 'Wawan Gunawan, S.Pd.', 'guru23@sekolah.sch.id', 1, 23, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(24, '197240101024', 'Euis Komariah, M.Pd.', 'guru24@sekolah.sch.id', 1, 24, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(25, '197250101025', 'Yusuf Maulana, S.Pd.', 'guru25@sekolah.sch.id', 1, 25, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(26, '197260101026', 'Ratna Dewi, S.Pd.', 'guru26@sekolah.sch.id', 1, 26, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(27, '197270101027', 'H. Mulyadi, S.Pd.', 'guru27@sekolah.sch.id', 1, 27, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(28, '197280101028', 'Imas Sulastri, S.Pd.', 'guru28@sekolah.sch.id', 1, 28, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(29, '197290101029', 'Tatang Suherman, M.Pd.', 'guru29@sekolah.sch.id', 1, 29, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(30, '197300101030', 'Yayah Nurhayati, S.Pd.', 'guru30@sekolah.sch.id', 1, 30, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(31, '197310101031', 'Iwan Setiawan, S.Kom.', 'guru31@sekolah.sch.id', 1, 31, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(32, '197320101032', 'Nenden Hasanah, S.Pd.', 'guru32@sekolah.sch.id', 1, 32, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(33, '197330101033', 'Cecep Saepudin, S.Pd.', 'guru33@sekolah.sch.id', 1, 33, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(34, '197340101034', 'Kokom Komariah, M.Pd.', 'guru34@sekolah.sch.id', 1, 34, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(35, '197350101035', 'Usep Ruhiyat, S.Pd.', 'guru35@sekolah.sch.id', 1, 35, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(36, '197360101036', 'Ai Suryani, S.Pd.', 'guru36@sekolah.sch.id', 1, 36, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(37, '197370101037', 'Agung Pratama, S.Kom.', 'guru37@sekolah.sch.id', 1, 37, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(38, '197380101038', 'Ema Kusnawati, S.Pd.', 'guru38@sekolah.sch.id', 1, 38, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(39, '197390101039', 'Dede Supriadi, M.Kom.', 'guru39@sekolah.sch.id', 1, 39, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(40, '197400101040', 'Enok Nurjanah, S.Pd.', 'guru40@sekolah.sch.id', 1, 40, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(41, '197410101041', 'Drs. Bambang Saputra, M.Pd.', 'guru41@sekolah.sch.id', 1, 41, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(42, '197420101042', 'Lina Oktaviani, S.Pd.', 'guru42@sekolah.sch.id', 1, 42, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(43, '197430101043', 'H. Asep Saepuloh, S.Pd.I.', 'guru43@sekolah.sch.id', 1, 43, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(44, '197440101044', 'Rina Nuraeni, M.Pd.', 'guru44@sekolah.sch.id', 1, 44, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(45, '197450101045', 'Oman Abdurahman, S.Pd.', 'guru45@sekolah.sch.id', 1, 45, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(46, '197460101046', 'Yeti Supriatin, S.Pd.', 'guru46@sekolah.sch.id', 1, 46, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(47, '197470101047', 'Fajar Nugraha, S.Kom.', 'guru47@sekolah.sch.id', 1, 47, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(48, '197480101048', 'Siti Maryam, S.Pd.', 'guru48@sekolah.sch.id', 1, 48, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(49, '197490101049', 'Hj. Tati Sumarni, S.Pd.', 'guru49@sekolah.sch.id', 1, 49, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(50, '197500101050', 'Agus Supriyadi, M.Pd.', 'guru50@sekolah.sch.id', 1, 50, '2026-09-23 03:44:07', '2026-09-23 03:44:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_kelas`
--

INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, 'X RPL 1', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(2, 'XI TKJ 1', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(3, 'XII BD 1', 'XII', 'BD', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(4, 'X TKR 1', 'X', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(5, 'XI TSM 1', 'XI', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(6, 'XII RPL 1', 'XII', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(7, 'X TKJ 1', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(8, 'XI BD 1', 'XI', 'BD', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(9, 'XII TKR 1', 'XII', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(10, 'X TSM 1', 'X', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(11, 'XI RPL 1', 'XI', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(12, 'XII TKJ 1', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(13, 'X BD 1', 'X', 'BD', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(14, 'XI TKR 1', 'XI', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(15, 'XII TSM 1', 'XII', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(16, 'X RPL 2', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(17, 'XI TKJ 2', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(19, 'X TKR 2', 'X', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(20, 'XI TSM 2', 'XI', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(21, 'XII RPL 2', 'XII', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(22, 'X TKJ 2', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(24, 'XII TKR 2', 'XII', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(25, 'X TSM 2', 'X', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(27, 'XII TKJ 2', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(29, 'XI TKR 2', 'XI', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(30, 'XII TSM 2', 'XII', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(32, 'XI TKJ 3', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(34, 'X TKR 3', 'X', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(35, 'XI TSM 3', 'XI', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(37, 'X TKJ 3', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(39, 'XII TKR 3', 'XII', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(40, 'X TSM 3', 'X', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(42, 'XII TKJ 3', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(44, 'XI TKR 3', 'XI', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(45, 'XII TSM 3', 'XII', 'TSM', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(47, 'XI TKJ 4', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(49, 'X TKR 4', 'X', 'TKR', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(58, 'X TKJ 4', 'X', 'TKJ', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(59, 'XII TKJ 4', 'XII', 'TKJ', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(60, 'X TKR 5', 'X', 'TKR', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(61, 'XI TKR 4', 'XI', 'TKR', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(62, 'XI TKR 5', 'XI', 'TKR', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(63, 'XII TKR 4', 'XII', 'TKR', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22'),
(64, 'XII TKR 5', 'XII', 'TKR', 1, '2026-10-19 07:36:22', '2026-10-19 07:36:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, 1, 50, 1, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(2, 2, 50, 2, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(3, 3, 50, 3, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(4, 4, 50, 4, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(5, 5, 50, 5, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(6, 6, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(7, 7, 50, 7, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(8, 8, 50, 8, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(9, 9, 50, 9, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(10, 10, 50, 10, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(11, 11, 50, 11, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(12, 12, 50, 12, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(13, 13, 50, 13, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(14, 14, 50, 14, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(15, 15, 50, 15, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(16, 16, 50, 16, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(17, 17, 50, 17, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(18, 18, 50, 3, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(19, 19, 50, 19, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(20, 20, 50, 20, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(21, 21, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(22, 22, 50, 22, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(23, 23, 50, 8, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(24, 24, 50, 24, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(25, 25, 50, 25, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(26, 26, 50, 11, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(27, 27, 50, 27, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(28, 28, 50, 13, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(29, 29, 50, 29, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(30, 30, 50, 30, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(31, 31, 50, 16, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(32, 32, 50, 32, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(33, 33, 50, 3, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(34, 34, 50, 34, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(35, 35, 50, 35, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(36, 36, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(37, 37, 50, 37, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(38, 38, 50, 8, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(39, 39, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(40, 40, 50, 40, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(41, 41, 50, 11, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(42, 42, 50, 42, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(43, 43, 50, 13, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(44, 44, 50, 44, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(45, 45, 50, 45, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(46, 46, 50, 16, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(47, 47, 50, 47, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(48, 48, 50, 3, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(49, 49, 50, 49, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(50, 50, 50, 35, '2026-07-01', '2027-06-30', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(0, 3, 50, 1, '2026-07-01', '2027-06-30', 1, '2026-09-29 02:07:48', '2026-09-29 02:07:48'),
(0, 3, 50, 1, '2026-07-02', '2027-06-30', 1, '2026-09-29 02:09:17', '2026-09-29 02:09:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tingkat` varchar(20) NOT NULL DEFAULT 'Ringan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `update_at`, `tingkat`) VALUES
(1, 1, 'PLG-001', 'Tidak membawa perlengkapan belajar', 2, 'Siswa tidak membawa perlengkapan belajar yang diperlukan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(2, 1, 'PLG-002', 'Tidak mengerjakan tugas', 2, 'Siswa tidak menyelesaikan tugas yang diberikan guru.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(3, 1, 'PLG-003', 'Tidur saat pembelajaran', 3, 'Siswa tidur saat proses pembelajaran berlangsung.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(4, 1, 'PLG-004', 'Tidak melaksanakan piket sesuai jadwal', 3, 'Siswa tidak melaksanakan tugas piket sesuai jadwal.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(5, 1, 'PLG-005', 'Mengabaikan instruksi guru', 5, 'Siswa mengabaikan instruksi guru yang berkaitan dengan kegiatan belajar.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(6, 1, 'PLG-006', 'Tidak mematuhi tata tertib sekolah', 5, 'Siswa melanggar ketentuan tata tertib sekolah yang telah disosialisasikan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(7, 1, 'PLG-007', 'Tidak mengikuti kegiatan sekolah tanpa alasan jelas', 5, 'Siswa tidak mengikuti kegiatan wajib sekolah tanpa keterangan yang dapat dipertanggungjawabkan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(8, 1, 'PLG-008', 'Menyontek saat ulangan', 10, 'Siswa menggunakan jawaban atau bantuan yang tidak diizinkan saat ulangan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(9, 1, 'PLG-009', 'Memalsukan tanda tangan guru atau orang tua', 15, 'Siswa memalsukan tanda tangan pada dokumen sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(10, 1, 'PLG-010', 'Mengulangi pelanggaran setelah pembinaan', 15, 'Siswa mengulangi pelanggaran setelah menjalani pembinaan resmi.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(11, 2, 'PLG-011', 'Terlambat datang ke sekolah', 2, 'Siswa tiba setelah waktu masuk sekolah yang ditentukan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(12, 2, 'PLG-012', 'Terlambat masuk kelas setelah istirahat', 2, 'Siswa terlambat kembali ke kelas setelah waktu istirahat.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(13, 2, 'PLG-013', 'Terlambat masuk kelas tanpa alasan', 3, 'Siswa terlambat masuk kelas tanpa alasan yang jelas.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(14, 2, 'PLG-014', 'Tidak masuk kelas tanpa izin', 5, 'Siswa tidak berada di kelas saat pembelajaran tanpa izin.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(15, 2, 'PLG-015', 'Tidak mengikuti pelajaran tanpa izin', 5, 'Siswa tidak mengikuti kegiatan pembelajaran tanpa izin guru.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(16, 2, 'PLG-016', 'Tidak mengikuti upacara tanpa izin', 5, 'Siswa tidak mengikuti upacara sekolah tanpa izin atau alasan yang sah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(17, 2, 'PLG-017', 'Membolos pada jam pelajaran', 10, 'Siswa sengaja menghindari kegiatan belajar pada jam pelajaran.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(18, 2, 'PLG-018', 'Pulang sebelum waktunya tanpa izin', 10, 'Siswa meninggalkan kegiatan sekolah sebelum waktunya tanpa izin.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(19, 2, 'PLG-019', 'Meninggalkan sekolah tanpa izin', 10, 'Siswa keluar dari lingkungan sekolah tanpa izin pihak berwenang.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(20, 2, 'PLG-020', 'Memalsukan surat izin atau surat sakit', 15, 'Siswa menggunakan surat izin atau surat sakit palsu.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(21, 3, 'PLG-021', 'Seragam kurang rapi', 2, 'Seragam tidak dikenakan dengan rapi sesuai ketentuan sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(22, 3, 'PLG-022', 'Tidak memakai ikat pinggang sesuai ketentuan', 2, 'Siswa tidak menggunakan ikat pinggang sesuai aturan seragam.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(23, 3, 'PLG-023', 'Kaus kaki tidak sesuai ketentuan', 2, 'Siswa memakai kaus kaki yang tidak sesuai ketentuan sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(24, 3, 'PLG-024', 'Tidak memakai atribut lengkap', 3, 'Siswa tidak memakai atribut seragam yang diwajibkan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(25, 3, 'PLG-025', 'Tidak memakai topi saat upacara', 5, 'Siswa tidak memakai topi saat diwajibkan dalam upacara.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(26, 3, 'PLG-026', 'Memakai sepatu tidak sesuai ketentuan', 5, 'Siswa menggunakan sepatu yang tidak sesuai aturan sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(27, 3, 'PLG-027', 'Memakai seragam tidak sesuai jadwal', 5, 'Siswa memakai seragam yang tidak sesuai jadwal yang ditetapkan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(28, 3, 'PLG-028', 'Mengeluarkan baju saat diwajibkan rapi', 5, 'Siswa tidak memasukkan atau merapikan baju saat diwajibkan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(29, 3, 'PLG-029', 'Memakai aksesori yang dilarang sekolah', 5, 'Siswa memakai aksesori yang dilarang dalam tata tertib sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(30, 3, 'PLG-030', 'Mengubah model seragam tanpa izin', 10, 'Siswa mengubah model seragam bertentangan dengan ketentuan sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(31, 4, 'PLG-031', 'Berbicara saat guru menjelaskan', 2, 'Siswa berbicara sehingga mengganggu proses penjelasan guru.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(32, 4, 'PLG-032', 'Mengganggu teman saat belajar', 3, 'Siswa mengganggu teman sehingga menghambat kegiatan belajar.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(33, 4, 'PLG-033', 'Membuat keributan di kelas', 5, 'Siswa membuat keributan yang mengganggu pembelajaran.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(34, 4, 'PLG-034', 'Menggunakan HP tanpa izin saat belajar', 5, 'Siswa menggunakan telepon genggam saat pembelajaran tanpa izin.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(35, 4, 'PLG-035', 'Berkata kasar kepada teman', 5, 'Siswa menggunakan kata-kata kasar kepada teman.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(36, 4, 'PLG-036', 'Mengganggu kegiatan sekolah', 5, 'Siswa mengganggu pelaksanaan kegiatan resmi sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(37, 4, 'PLG-037', 'Berbicara tidak sopan kepada guru', 10, 'Siswa berkata tidak sopan kepada guru.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(38, 4, 'PLG-038', 'Membawa barang yang dilarang sekolah', 10, 'Siswa membawa barang yang secara jelas dilarang tata tertib sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(39, 4, 'PLG-039', 'Merusak fasilitas sekolah dengan sengaja', 15, 'Siswa sengaja merusak fasilitas milik sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(40, 4, 'PLG-040', 'Melakukan perundungan atau perkelahian', 20, 'Siswa melakukan perundungan atau terlibat perkelahian yang terbukti.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(41, 5, 'PLG-041', 'Membuang sampah sembarangan', 2, 'Siswa membuang sampah bukan pada tempat yang disediakan.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(42, 5, 'PLG-042', 'Meninggalkan sampah setelah makan', 2, 'Siswa meninggalkan sampah makanan atau minuman di area sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(43, 5, 'PLG-043', 'Membiarkan meja dan kursi tidak rapi', 2, 'Siswa meninggalkan meja dan kursi dalam keadaan tidak rapi.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(44, 5, 'PLG-044', 'Meludah sembarangan', 3, 'Siswa meludah bukan pada tempat yang semestinya.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Ringan'),
(45, 5, 'PLG-045', 'Tidak membersihkan area piket', 3, 'Siswa tidak membersihkan area yang menjadi tanggung jawab piketnya.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(46, 5, 'PLG-046', 'Tidak menjaga kebersihan kelas', 5, 'Siswa tidak menjaga kebersihan kelas sesuai tanggung jawabnya.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(47, 5, 'PLG-047', 'Tidak menjaga kebersihan toilet', 5, 'Siswa mengotori toilet dan tidak menjaga kebersihan fasilitas tersebut.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Sedang'),
(48, 5, 'PLG-048', 'Mengotori fasilitas sekolah dengan sengaja', 10, 'Siswa sengaja mengotori fasilitas sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(49, 5, 'PLG-049', 'Mencoret meja, kursi, atau dinding', 10, 'Siswa mencoret fasilitas sekolah tanpa izin.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat'),
(50, 5, 'PLG-050', 'Merusak tanaman atau taman sekolah', 10, 'Siswa sengaja merusak tanaman atau fasilitas taman sekolah.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22', 'Berat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_pelanggaran_kategori`
--

INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Kedisiplinan', 'Pelanggaran terkait kepatuhan terhadap aturan dan tata tertib sekolah.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(2, 'Kehadiran', 'Pelanggaran terkait keterlambatan, ketidakhadiran, dan kehadiran siswa.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(3, 'Seragam', 'Pelanggaran terkait seragam dan atribut sekolah.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(4, 'Ketertiban', 'Pelanggaran terkait ketertiban selama di sekolah dan kegiatan pembelajaran.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(5, 'Kebersihan', 'Pelanggaran terkait kebersihan diri, kelas, dan lingkungan sekolah.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(6, 'Kedisiplinan 6', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(7, 'Kehadiran 7', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(8, 'Seragam 8', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(9, 'Ketertiban 9', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(10, 'Kebersihan 10', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(11, 'Kedisiplinan 11', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(12, 'Kehadiran 12', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(13, 'Seragam 13', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(14, 'Ketertiban 14', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(15, 'Kebersihan 15', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(16, 'Kedisiplinan 16', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(17, 'Kehadiran 17', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(18, 'Seragam 18', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(19, 'Ketertiban 19', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(20, 'Kebersihan 20', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(21, 'Kedisiplinan 21', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(22, 'Kehadiran 22', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(23, 'Seragam 23', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(24, 'Ketertiban 24', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(25, 'Kebersihan 25', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(26, 'Kedisiplinan 26', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(27, 'Kehadiran 27', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(28, 'Seragam 28', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(29, 'Ketertiban 29', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(30, 'Kebersihan 30', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(31, 'Kedisiplinan 31', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(32, 'Kehadiran 32', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(33, 'Seragam 33', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(34, 'Ketertiban 34', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(35, 'Kebersihan 35', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(36, 'Kedisiplinan 36', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(37, 'Kehadiran 37', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(38, 'Seragam 38', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(39, 'Ketertiban 39', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(40, 'Kebersihan 40', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(41, 'Kedisiplinan 41', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(42, 'Kehadiran 42', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(43, 'Seragam 43', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(44, 'Ketertiban 44', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(45, 'Kebersihan 45', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(46, 'Kedisiplinan 46', 'Pelanggaran yang berkaitan dengan kedisiplinan dan kepatuhan siswa terhadap aturan serta tata tertib sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(47, 'Kehadiran 47', 'Pelanggaran yang berkaitan dengan kehadiran, ketepatan waktu, dan partisipasi siswa dalam kegiatan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(48, 'Seragam 48', 'Pelanggaran yang berkaitan dengan penggunaan seragam dan atribut sekolah yang tidak sesuai dengan ketentuan.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(49, 'Ketertiban 49', 'Pelanggaran yang berkaitan dengan ketertiban siswa selama berada di lingkungan sekolah dan saat kegiatan pembelajaran.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(50, 'Kebersihan 50', 'Pelanggaran yang berkaitan dengan kebersihan diri, kelas, dan lingkungan sekolah.', 0, '2026-09-23 03:53:54', '2026-09-23 03:53:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `pelanggaran_id` int(11) NOT NULL,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kelas_id` int(11) NOT NULL,
  `nama_kelas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`, `kelas_id`, `nama_kelas`) VALUES
(1, 50, 1, 'Adit Pratama', 1, 'Datang terlambat 1', 1, 1, 'Guru 1', '2025-01-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 1, 'X RPL 1'),
(2, 50, 2, 'Aulia Rahma', 2, 'Tidak memakai atribut lengkap 2', 2, 2, 'Guru 2', '2025-02-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 2, 'XI TKJ 1'),
(3, 50, 3, 'Bagas Saputra', 3, 'Tidak memakai seragam sesuai ketentuan 3', 3, 3, 'Guru 3', '2025-03-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(4, 50, 4, 'Citra Lestari', 4, 'Rambut tidak sesuai ketentuan 4', 4, 4, 'Guru 4', '2025-04-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 4, 'X TKR 1'),
(5, 50, 5, 'Daffa Ramadhan', 5, 'Meninggalkan kelas tanpa izin 5', 5, 5, 'Guru 5', '2025-05-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 5, 'XI TSM 1'),
(6, 50, 6, 'Elsa Putri', 6, 'Tidak mengerjakan tugas 6', 6, 6, 'Guru 6', '2025-06-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 6, 'XII RPL 1'),
(7, 50, 7, 'Fajar Nugraha', 7, 'Berbicara saat pembelajaran 7', 7, 7, 'Guru 7', '2025-07-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 7, 'X TKJ 1'),
(8, 50, 8, 'Gina Maharani', 8, 'Membuang sampah sembarangan 8', 8, 8, 'Guru 8', '2025-08-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(9, 50, 9, 'Hafiz Akbar', 9, 'Tidak mengikuti upacara 9', 9, 9, 'Guru 9', '2025-09-09', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 9, 'XII TKR 1'),
(10, 50, 10, 'Intan Permata', 10, 'Tidak membawa perlengkapan belajar 10', 10, 10, 'Guru 10', '2025-10-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 10, 'X TSM 1'),
(11, 50, 11, 'Jovan Setiawan', 11, 'Datang terlambat 11', 11, 11, 'Guru 11', '2025-11-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(12, 50, 12, 'Kania Salsabila', 12, 'Tidak memakai atribut lengkap 12', 12, 12, 'Guru 12', '2025-12-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 12, 'XII TKJ 1'),
(13, 50, 13, 'Lutfi Hakim', 13, 'Tidak memakai seragam sesuai ketentuan 13', 13, 13, 'Guru 13', '2025-01-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(14, 50, 14, 'Maya Anggraini', 14, 'Rambut tidak sesuai ketentuan 14', 14, 14, 'Guru 14', '2025-02-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 14, 'XI TKR 1'),
(15, 50, 15, 'Naufal Rizky', 15, 'Meninggalkan kelas tanpa izin 15', 15, 15, 'Guru 15', '2025-03-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 15, 'XII TSM 1'),
(16, 50, 16, 'Olivia Amelia', 16, 'Tidak mengerjakan tugas 16', 16, 16, 'Guru 16', '2025-04-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(17, 50, 17, 'Putra Wijaya', 17, 'Berbicara saat pembelajaran 17', 17, 17, 'Guru 17', '2025-05-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 17, 'XI TKJ 2'),
(18, 50, 18, 'Qonita Zahra', 18, 'Membuang sampah sembarangan 18', 18, 18, 'Guru 18', '2025-06-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(19, 50, 19, 'Raka Firmansyah', 19, 'Tidak mengikuti upacara 19', 19, 19, 'Guru 19', '2025-07-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 19, 'X TKR 2'),
(20, 50, 20, 'Salsa Nabila', 20, 'Tidak membawa perlengkapan belajar 20', 20, 20, 'Guru 20', '2025-08-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 20, 'XI TSM 2'),
(21, 50, 21, 'Tegar Maulana', 21, 'Datang terlambat 21', 21, 21, 'Guru 21', '2025-09-21', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 21, 'XII RPL 2'),
(22, 50, 22, 'Ulfa Nuraini', 22, 'Tidak memakai atribut lengkap 22', 22, 22, 'Guru 22', '2025-10-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 22, 'X TKJ 2'),
(23, 50, 23, 'Vino Alfarizi', 23, 'Tidak memakai seragam sesuai ketentuan 23', 23, 23, 'Guru 23', '2025-11-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(24, 50, 24, 'Wulan Sari', 24, 'Rambut tidak sesuai ketentuan 24', 24, 24, 'Guru 24', '2025-12-24', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 24, 'XII TKR 2'),
(25, 50, 25, 'Yoga Pratama', 25, 'Meninggalkan kelas tanpa izin 25', 25, 25, 'Guru 25', '2025-01-25', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 25, 'X TSM 2'),
(26, 50, 26, 'Zahra Aulia', 26, 'Tidak mengerjakan tugas 26', 26, 26, 'Guru 26', '2025-02-26', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(27, 50, 27, 'Andika Kurnia', 27, 'Berbicara saat pembelajaran 27', 27, 27, 'Guru 27', '2025-03-27', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 27, 'XII TKJ 2'),
(28, 50, 28, 'Bella Safitri', 28, 'Membuang sampah sembarangan 28', 28, 28, 'Guru 28', '2025-04-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(29, 50, 29, 'Cahyo Aditya', 29, 'Tidak mengikuti upacara 29', 29, 29, 'Guru 29', '2025-05-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 29, 'XI TKR 2'),
(30, 50, 30, 'Dinda Maharani', 30, 'Tidak membawa perlengkapan belajar 30', 30, 30, 'Guru 30', '2025-06-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 30, 'XII TSM 2'),
(31, 50, 31, 'Eko Saputra', 31, 'Datang terlambat 31', 31, 31, 'Guru 31', '2025-07-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(32, 50, 32, 'Fitri Handayani', 32, 'Tidak memakai atribut lengkap 32', 32, 32, 'Guru 32', '2025-08-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 32, 'XI TKJ 3'),
(33, 50, 33, 'Galih Ramadhan', 33, 'Tidak memakai seragam sesuai ketentuan 33', 33, 33, 'Guru 33', '2025-09-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(34, 50, 34, 'Hana Aprilia', 34, 'Rambut tidak sesuai ketentuan 34', 34, 34, 'Guru 34', '2025-10-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 34, 'X TKR 3'),
(35, 50, 35, 'Ilham Fauzan', 35, 'Meninggalkan kelas tanpa izin 35', 35, 35, 'Guru 35', '2025-11-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 35, 'XI TSM 3'),
(36, 50, 36, 'Jihan Lestari', 36, 'Tidak mengerjakan tugas 36', 36, 36, 'Guru 36', '2025-12-09', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 21, 'XII RPL 2'),
(37, 50, 37, 'Kevin Setiawan', 37, 'Berbicara saat pembelajaran 37', 37, 37, 'Guru 37', '2025-01-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 37, 'X TKJ 3'),
(38, 50, 38, 'Larasati Putri', 38, 'Membuang sampah sembarangan 38', 38, 38, 'Guru 38', '2025-02-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(39, 50, 39, 'Miko Pratama', 39, 'Tidak mengikuti upacara 39', 39, 39, 'Guru 39', '2025-03-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 39, 'XII TKR 3'),
(40, 50, 40, 'Nadia Safira', 40, 'Tidak membawa perlengkapan belajar 40', 40, 40, 'Guru 40', '2025-04-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 40, 'X TSM 3'),
(41, 50, 41, 'Rian Hidayat', 41, 'Datang terlambat 41', 41, 41, 'Guru 41', '2025-05-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(42, 50, 42, 'Siti Aisyah', 42, 'Tidak memakai atribut lengkap 42', 42, 42, 'Guru 42', '2025-06-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 42, 'XII TKJ 3'),
(43, 50, 43, 'Taufik Haryanto', 43, 'Tidak memakai seragam sesuai ketentuan 43', 43, 43, 'Guru 43', '2025-07-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(44, 50, 44, 'Vania Putri', 44, 'Rambut tidak sesuai ketentuan 44', 44, 44, 'Guru 44', '2025-08-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 44, 'XI TKR 3'),
(45, 50, 45, 'Wahyu Nugroho', 45, 'Meninggalkan kelas tanpa izin 45', 45, 45, 'Guru 45', '2025-09-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 45, 'XII TSM 3'),
(46, 50, 46, 'Yuni Kartika', 46, 'Tidak mengerjakan tugas 46', 46, 46, 'Guru 46', '2025-10-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(47, 50, 47, 'Zaki Firmansyah', 47, 'Berbicara saat pembelajaran 47', 47, 47, 'Guru 47', '2025-11-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 47, 'XI TKJ 4'),
(48, 50, 48, 'Ari Maulana', 48, 'Membuang sampah sembarangan 48', 48, 48, 'Guru 48', '2025-12-21', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(49, 50, 49, 'Bima Setiawan', 49, 'Tidak mengikuti upacara 49', 49, 49, 'Guru 49', '2025-01-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 49, 'X TKR 4'),
(50, 50, 50, 'Cindy Oktavia', 50, 'Tidak membawa perlengkapan belajar 50', 50, 50, 'Guru 50', '2025-02-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 35, 'XI TSM 3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, '20260001', '0070000001', 'Adit Pratama', 'L', '2007-01-01', 'Jl. Pendidikan No. 1, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(2, '20260002', '0080000002', 'Aulia Rahma', 'P', '2008-02-02', 'Jl. Pendidikan No. 2, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(3, '20260003', '0090000003', 'Bagas Saputra', 'L', '2009-03-03', 'Jl. Pendidikan No. 3, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(4, '20260004', '0100000004', 'Citra Lestari', 'P', '2010-04-04', 'Jl. Pendidikan No. 4, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(5, '20260005', '0070000005', 'Daffa Ramadhan', 'L', '2007-05-05', 'Jl. Pendidikan No. 5, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(6, '20260006', '0080000006', 'Elsa Putri', 'P', '2008-06-06', 'Jl. Pendidikan No. 6, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(7, '20260007', '0090000007', 'Fajar Nugraha', 'L', '2009-07-07', 'Jl. Pendidikan No. 7, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(8, '20260008', '0100000008', 'Gina Maharani', 'P', '2010-08-08', 'Jl. Pendidikan No. 8, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(9, '20260009', '0070000009', 'Hafiz Akbar', 'L', '2007-09-09', 'Jl. Pendidikan No. 9, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(10, '20260010', '0080000010', 'Intan Permata', 'P', '2008-01-10', 'Jl. Pendidikan No. 10, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(11, '20260011', '0090000011', 'Jovan Setiawan', 'L', '2009-02-11', 'Jl. Pendidikan No. 11, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(12, '20260012', '0100000012', 'Kania Salsabila', 'P', '2010-03-12', 'Jl. Pendidikan No. 12, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(13, '20260013', '0070000013', 'Lutfi Hakim', 'L', '2007-04-13', 'Jl. Pendidikan No. 13, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(14, '20260014', '0080000014', 'Maya Anggraini', 'P', '2008-05-14', 'Jl. Pendidikan No. 14, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(15, '20260015', '0090000015', 'Naufal Rizky', 'L', '2009-06-15', 'Jl. Pendidikan No. 15, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(16, '20260016', '0100000016', 'Olivia Amelia', 'P', '2010-07-16', 'Jl. Pendidikan No. 16, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(17, '20260017', '0070000017', 'Putra Wijaya', 'L', '2007-08-17', 'Jl. Pendidikan No. 17, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(18, '20260018', '0080000018', 'Qonita Zahra', 'P', '2008-09-18', 'Jl. Pendidikan No. 18, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(19, '20260019', '0090000019', 'Raka Firmansyah', 'L', '2009-01-19', 'Jl. Pendidikan No. 19, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(20, '20260020', '0100000020', 'Salsa Nabila', 'P', '2010-02-20', 'Jl. Pendidikan No. 20, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(21, '20260021', '0070000021', 'Tegar Maulana', 'L', '2007-03-21', 'Jl. Pendidikan No. 21, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(22, '20260022', '0080000022', 'Ulfa Nuraini', 'P', '2008-04-22', 'Jl. Pendidikan No. 22, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(23, '20260023', '0090000023', 'Vino Alfarizi', 'L', '2009-05-23', 'Jl. Pendidikan No. 23, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(24, '20260024', '0100000024', 'Wulan Sari', 'P', '2010-06-24', 'Jl. Pendidikan No. 24, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(25, '20260025', '0070000025', 'Yoga Pratama', 'L', '2007-07-25', 'Jl. Pendidikan No. 25, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(26, '20260026', '0080000026', 'Zahra Aulia', 'P', '2008-08-26', 'Jl. Pendidikan No. 26, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(27, '20260027', '0090000027', 'Andika Kurnia', 'L', '2009-09-27', 'Jl. Pendidikan No. 27, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(28, '20260028', '0100000028', 'Bella Safitri', 'P', '2010-01-01', 'Jl. Pendidikan No. 28, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(29, '20260029', '0070000029', 'Cahyo Aditya', 'L', '2007-02-02', 'Jl. Pendidikan No. 29, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(30, '20260030', '0080000030', 'Dinda Maharani', 'P', '2008-03-03', 'Jl. Pendidikan No. 30, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(31, '20260031', '0090000031', 'Eko Saputra', 'L', '2009-04-04', 'Jl. Pendidikan No. 31, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(32, '20260032', '0100000032', 'Fitri Handayani', 'P', '2010-05-05', 'Jl. Pendidikan No. 32, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(33, '20260033', '0070000033', 'Galih Ramadhan', 'L', '2007-06-06', 'Jl. Pendidikan No. 33, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(34, '20260034', '0080000034', 'Hana Aprilia', 'P', '2008-07-07', 'Jl. Pendidikan No. 34, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(35, '20260035', '0090000035', 'Ilham Fauzan', 'L', '2009-08-08', 'Jl. Pendidikan No. 35, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(36, '20260036', '0100000036', 'Jihan Lestari', 'P', '2010-09-09', 'Jl. Pendidikan No. 36, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(37, '20260037', '0070000037', 'Kevin Setiawan', 'L', '2007-01-10', 'Jl. Pendidikan No. 37, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(38, '20260038', '0080000038', 'Larasati Putri', 'P', '2008-02-11', 'Jl. Pendidikan No. 38, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(39, '20260039', '0090000039', 'Miko Pratama', 'L', '2009-03-12', 'Jl. Pendidikan No. 39, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(40, '20260040', '0100000040', 'Nadia Safira', 'P', '2010-04-13', 'Jl. Pendidikan No. 40, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(41, '20260041', '0070000041', 'Rian Hidayat', 'L', '2007-05-14', 'Jl. Pendidikan No. 41, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(42, '20260042', '0080000042', 'Siti Aisyah', 'P', '2008-06-15', 'Jl. Pendidikan No. 42, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(43, '20260043', '0090000043', 'Taufik Haryanto', 'L', '2009-07-16', 'Jl. Pendidikan No. 43, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(44, '20260044', '0100000044', 'Vania Putri', 'P', '2010-08-17', 'Jl. Pendidikan No. 44, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(45, '20260045', '0070000045', 'Wahyu Nugroho', 'L', '2007-09-18', 'Jl. Pendidikan No. 45, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(46, '20260046', '0080000046', 'Yuni Kartika', 'P', '2008-01-19', 'Jl. Pendidikan No. 46, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(47, '20260047', '0090000047', 'Zaki Firmansyah', 'L', '2009-02-20', 'Jl. Pendidikan No. 47, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(48, '20260048', '0100000048', 'Ari Maulana', 'P', '2010-03-21', 'Jl. Pendidikan No. 48, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(49, '20260049', '0070000049', 'Bima Setiawan', 'L', '2007-04-22', 'Jl. Pendidikan No. 49, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(50, '20260050', '0080000050', 'Cindy Okta', 'P', '2008-05-23', 'Jl. Pendidikan No. 50, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(2, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(3, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(4, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(5, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(6, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(7, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(8, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(9, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(10, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(11, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(12, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(13, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(14, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(15, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(16, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(17, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(18, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(19, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(20, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(21, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(22, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(23, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(24, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(25, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(26, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(27, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(28, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(29, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(30, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(31, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(32, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(33, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(34, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(35, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(36, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(37, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(38, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(39, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(40, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(41, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(42, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(43, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(44, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(45, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(46, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(47, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(48, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(49, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(50, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'User 1', 'user1@sekolah.sch.id', NULL, '$2y$10$IMfIBRNj9gLvjKb4XK/SGuWXieKNlJoKL0e3bsLG4AqPYAeK9LIiu', NULL, 'admin', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(2, 'User 2', 'user2@sekolah.sch.id', NULL, '$2y$10$HLkKMgj.Rqoe4iMVqQTtuOvROiPqoXar8AIENT.w7sWlPIpULkJYC', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(3, 'User 3', 'user3@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(4, 'User 4', 'user4@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(5, 'User 5', 'user5@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(6, 'User 6', 'user6@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(7, 'User 7', 'user7@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(8, 'User 8', 'user8@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(9, 'User 9', 'user9@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(10, 'User 10', 'user10@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(11, 'User 11', 'user11@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(12, 'User 12', 'user12@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(13, 'User 13', 'user13@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(14, 'User 14', 'user14@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(15, 'User 15', 'user15@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(16, 'User 16', 'user16@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(17, 'User 17', 'user17@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(18, 'User 18', 'user18@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(19, 'User 19', 'user19@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(20, 'User 20', 'user20@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(21, 'User 21', 'user21@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(22, 'User 22', 'user22@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(23, 'User 23', 'user23@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(24, 'User 24', 'user24@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(25, 'User 25', 'user25@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(26, 'User 26', 'user26@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(27, 'User 27', 'user27@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(28, 'User 28', 'user28@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(29, 'User 29', 'user29@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(30, 'User 30', 'user30@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(31, 'User 31', 'user31@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(32, 'User 32', 'user32@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(33, 'User 33', 'user33@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(34, 'User 34', 'user34@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(35, 'User 35', 'user35@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(36, 'User 36', 'user36@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(37, 'User 37', 'user37@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(38, 'User 38', 'user38@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(39, 'User 39', 'user39@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(40, 'User 40', 'user40@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(41, 'User 41', 'user41@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(42, 'User 42', 'user42@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(43, 'User 43', 'user43@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(44, 'User 44', 'user44@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(45, 'User 45', 'user45@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(46, 'User 46', 'user46@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(47, 'User 47', 'user47@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(48, 'User 48', 'user48@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(49, 'User 49', 'user49@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(50, 'User 50', 'user50@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_wali_kelas`
--

INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, 50, 1, 1, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(2, 50, 2, 2, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(3, 50, 3, 3, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(4, 50, 4, 4, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(5, 50, 5, 5, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(6, 50, 6, 6, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(7, 50, 7, 7, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(8, 50, 8, 8, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(9, 50, 9, 9, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(10, 50, 10, 10, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(11, 50, 11, 11, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(12, 50, 12, 12, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(13, 50, 13, 13, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(14, 50, 14, 14, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(15, 50, 15, 15, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(16, 50, 16, 16, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(17, 50, 17, 17, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(18, 50, 3, 18, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(19, 50, 19, 19, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(20, 50, 20, 20, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(21, 50, 21, 21, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(22, 50, 22, 22, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(23, 50, 8, 23, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(24, 50, 24, 24, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(25, 50, 25, 25, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(26, 50, 11, 26, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(27, 50, 27, 27, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(28, 50, 13, 28, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(29, 50, 29, 29, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(30, 50, 30, 30, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(31, 50, 16, 31, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(32, 50, 32, 32, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(33, 50, 3, 33, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(34, 50, 34, 34, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(35, 50, 35, 35, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(36, 50, 21, 36, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(37, 50, 37, 37, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(38, 50, 8, 38, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(39, 50, 39, 39, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(40, 50, 40, 40, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(41, 50, 11, 41, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(42, 50, 42, 42, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(43, 50, 13, 43, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(44, 50, 44, 44, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(45, 50, 45, 45, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(46, 50, 16, 46, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(47, 50, 47, 47, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(48, 50, 3, 48, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(49, 50, 49, 49, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(50, 50, 35, 50, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(51, 50, 1, 10, '2026-07-01', '2027-06-30', 1, '2026-09-29 02:11:31', '2026-09-29 02:11:31'),
(52, 50, 1, 10, '2026-07-01', '2027-06-30', 1, '2026-09-29 02:13:18', '2026-09-29 02:13:18');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `pelanggaran_id` (`pelanggaran_id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_2` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_4` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
