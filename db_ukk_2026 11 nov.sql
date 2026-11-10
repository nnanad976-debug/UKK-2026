-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2026 at 02:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `t_guru`
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
-- Dumping data for table `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '197010101001', 'Wawan Kurniawan, S.Pd.I', 'wawan.kurniawan@gmail.com', 1, 52, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(2, '197020101002', 'Ai Nuryaidah, S.Pd', 'ai.nuryaidah@gmail.com', 1, 2, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(3, '197030101003', 'Maya Permata Endah, S. Pd', 'maya.permata.endah@gmail.com', 1, 3, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(4, '197040101004', 'Dody Jaenudin, S.Pd.', 'dody.jaenudin@gmail.com', 1, 4, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(5, '197050101005', 'Dedi Kardiman, S. Pd', 'dedi.kardiman@gmail.com', 1, 5, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(6, '197060101006', 'Uca Musa Yusenda, S.Pd', 'uca.musa.yusenda@gmail.com', 1, 6, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(7, '197070101007', 'Drs. Ahmad Nur Mukhlis', 'ahmad.nur.mukhlis@gmail.com', 1, 7, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(8, '197080101008', 'Drs. Heri', 'heri@gmail.com', 1, 8, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(9, '197090101009', 'Asep Bastaman', 'asep.bastaman@gmail.com', 1, 9, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(10, '197100101010', 'H. Encep Maemun, S.T., M.Pd', 'encep.maemun@gmail.com', 1, 10, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(11, '197110101011', 'Opik', 'opik@gmail.com', 1, 11, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(12, '197120101012', 'Ida Farida, S. Pd', 'ida.farida@gmail.com', 1, 12, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(13, '197130101013', 'Idah Mahmudah, S.Pd', 'idah.mahmudah@gmail.com', 1, 13, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(14, '197140101014', 'Drs. Muhamad Ali', 'muhamad.ali@gmail.com', 1, 14, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(15, '197150101015', 'Helmi Ali Akbar, S.Pd.I', 'helmi.ali.akbar@gmail.com', 1, 15, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(16, '197160101016', 'Syeni Suswarini, S.Pd', 'syeni.suswarini@gmail.com', 1, 16, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(17, '197170101017', 'Wiwid Yulianti, S.Pd', 'wiwid.yulianti@gmail.com', 1, 17, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(18, '197180101018', 'Ansor Muhajir, S.T', 'ansor.muhajir@gmail.com', 1, 18, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(19, '197190101019', 'Ogi Gaosun Gias, S.Pd.I', 'ogi.gaosun.gias@gmail.com', 1, 19, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(20, '197200101020', 'Sandi Mulyana, S.Pd', 'sandi.mulyana@gmail.com', 1, 20, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(21, '197210101021', 'Erna Suryani, S.Pd', 'erna.suryani@gmail.com', 1, 21, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(22, '197220101022', 'Ai Wahidah, S.Ag', 'ai.wahidah@gmail.com', 1, 22, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(23, '197230101023', 'Chandra Aditya Yusup, S.Pd', 'chandra.aditya.yusup@gmail.com', 1, 23, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(24, '197240101024', 'Arif Kusmawan, S.Pd', 'arif.kusmawan@gmail.com', 1, 24, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(25, '197250101025', 'Asep Taufiq Hidayat, S.Kom', 'asep.taufiq.hidayat@gmail.com', 1, 25, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(26, '197260101026', 'Rendy Ichsan Kusumah, S.T', 'rendy.ichsan.kusumah@gmail.com', 1, 26, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(27, '197270101027', 'Feri Rahmawan, S.Pd', 'feri.rahmawan@gmail.com', 1, 27, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(28, '197280101028', 'Ari Sandriana, S.Kom', 'ari.sandriana@gmail.com', 1, 28, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(29, '197290101029', 'Yaneu Nurdian, S.Pd, M.Pd', 'yaneu.nurdian@gmail.com', 1, 29, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(30, '197300101030', 'Ucu Muhammad Khoer N, S.Pd', 'ucu.muhammad.khoer.n@gmail.com', 1, 30, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(31, '197310101031', 'Mira Dwi Rahmawati, S.Ag', 'mira.dwi.rahmawati@gmail.com', 1, 31, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(32, '197320101032', 'Aldi Maulana, S.Pd', 'aldi.maulana@gmail.com', 1, 32, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(33, '197330101033', 'Eli Rosliah, S.Pd', 'eli.rosliah@gmail.com', 1, 33, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(34, '197340101034', 'Isni Nur Fitriani, S.Pd', 'isni.nur.fitriani@gmail.com', 1, 34, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(35, '197350101035', 'Aanisah Putri Nur Aini, S.Pd', 'aanisah.putri.nur.aini@gmail.com', 1, 35, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(36, '197360101036', 'Yusi Luthfiani, S.Sos.I', 'yusi.luthfiani@gmail.com', 1, 36, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(37, '197370101037', 'Lilis Rustandy Mawardi, S.Pd., M.Pd', 'lilis.rustandy.mawardi@gmail.com', 1, 37, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(38, '197380101038', 'Yusrin Noer Sabrina, S.Pd', 'yusrin.noer.sabrina@gmail.com', 1, 38, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(39, '197390101039', 'Senny Indayani, S.Pd', 'senny.indayani@gmail.com', 1, 39, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(40, '197400101040', 'Ilma Amalia, S.Pd', 'ilma.amalia@gmail.com', 1, 40, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(41, '197410101041', 'Alvira Sofwatunnisa, S.Pd', 'alvira.sofwatunnisa@gmail.com', 1, 41, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(42, '197420101042', 'Risna Andriyani, S.Pd', 'risna.andriyani@gmail.com', 1, 42, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(43, '197430101043', 'Dra. Tintin Noerhayati', 'tintin.noerhayati@gmail.com', 1, 43, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(44, '197440101044', 'Rizkia Fahira, S.Pd', 'rizkia.fahira@gmail.com', 1, 44, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(45, '197450101045', 'Frasetio', 'frasetio@gmail.com', 1, 45, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(46, '197460101046', 'Ratnaningsih Hatil Aeni, S.Pd.', 'ratnaningsih.hatil.aeni@gmail.com', 1, 46, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(47, '197470101047', 'Dheny Darajatul Hasani, S.Pd.', 'dheny.darajatul.hasani@gmail.com', 1, 47, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(48, '197480101048', 'Fahrullah, S.Pd', 'fahrullah@gmail.com', 1, 48, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(49, '197490101049', 'Aan Andriana, S.T', 'aan.andriana@gmail.com', 1, 49, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(50, '197500101050', 'Fitri Fauziah, S.T', 'fitri.fauziah@gmail.com', 1, 50, '2026-09-23 03:44:07', '2026-11-05 02:34:08'),
(51, '197500101051', 'Nadia Zulfa', 'guru51@sekolah.sch.id', 0, NULL, '2026-11-04 03:53:59', '2026-11-05 02:31:57'),
(52, '197500101052', 'Nida Aulia, S.Pd', 'nida.aulia@gmail.com', 1, 53, '2026-11-05 02:31:57', '2026-11-05 02:34:08'),
(53, '197500101053', 'Sri Nurrizki Putri Krisnadi, S.Pd', 'sri.nurrizki.putri.krisnadi@gmail.com', 1, 54, '2026-11-05 02:31:57', '2026-11-05 02:34:08'),
(54, '197500101054', 'Riesy Putri Navia, S.Kom', 'riesy.putri.navia@gmail.com', 1, 55, '2026-11-05 02:31:57', '2026-11-05 02:34:08'),
(55, '197500101054', 'Rurry Febriani Maryadi, S.Pd', 'rurry.febriani.maryadi@gmail.com', 1, 56, '2026-11-05 02:31:57', '2026-11-05 02:34:08'),
(56, '197500101055', 'Irvan Rizal, S.Pd', 'irvan.rizal@gmail.com', 1, 57, '2026-11-05 02:31:57', '2026-11-05 02:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas`
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
-- Dumping data for table `t_kelas`
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
-- Table structure for table `t_kelas_siswa`
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
-- Dumping data for table `t_kelas_siswa`
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
(51, 55, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(52, 56, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(53, 57, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(54, 58, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(55, 59, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(56, 60, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(57, 61, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(58, 62, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(59, 63, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(60, 64, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(61, 65, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(62, 66, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(63, 67, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(64, 68, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(65, 69, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(66, 70, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(67, 71, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(68, 72, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(69, 73, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(70, 74, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(71, 75, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(72, 76, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(73, 77, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(74, 78, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-05 04:11:14', '2026-11-05 04:11:14'),
(123, 3, 50, 1, '2026-07-01', '2027-06-30', 1, '2026-09-29 02:07:48', '2026-09-29 02:07:48'),
(124, 3, 50, 1, '2026-07-02', '2027-06-30', 1, '2026-09-29 02:09:17', '2026-09-29 02:09:17'),
(125, 52, 50, 21, '2026-10-20', '2027-10-20', 1, '2026-10-20 01:14:04', '2026-10-20 01:14:04'),
(126, 53, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-03 04:30:33', '2026-11-03 04:30:33'),
(127, 54, 50, 21, '2026-07-01', '2027-06-30', 1, '2026-11-04 04:21:04', '2026-11-04 04:21:04'),
(128, 86, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(129, 87, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(130, 88, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(131, 89, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(132, 90, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(133, 91, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(134, 92, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(135, 93, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(136, 94, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(137, 95, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(138, 96, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(139, 97, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(140, 98, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(141, 99, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(142, 100, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(143, 101, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(144, 102, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(145, 103, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(146, 104, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(147, 105, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(148, 106, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(149, 107, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(150, 108, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(151, 109, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(152, 110, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(153, 111, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(154, 112, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(155, 113, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(156, 114, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(157, 115, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(158, 116, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(159, 117, 50, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(192, 150, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(193, 151, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(194, 152, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(195, 153, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(196, 154, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(197, 155, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(198, 156, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(199, 157, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(200, 158, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(201, 159, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(202, 160, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(203, 161, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(204, 162, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(205, 163, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(206, 164, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(207, 165, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(208, 166, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(209, 167, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(210, 168, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(211, 169, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(212, 170, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(213, 171, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(214, 172, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27'),
(215, 173, 50, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 02:27:27', '2026-11-10 02:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran`
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
-- Dumping data for table `t_pelanggaran`
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
-- Table structure for table `t_pelanggaran_kategori`
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
-- Dumping data for table `t_pelanggaran_kategori`
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
-- Table structure for table `t_pelanggaran_siswa`
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
-- Dumping data for table `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`, `kelas_id`, `nama_kelas`) VALUES
(1, 50, 1, 'Adit Pratama', 1, 'Tidak membawa perlengkapan belajar', 1, 1, 'Wawan Kurniawan, S.Pd.I', '2025-01-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 1, 'X RPL 1'),
(2, 50, 2, 'Aulia Rahma', 2, 'Tidak mengerjakan tugas', 1, 2, 'Ai Nuryaidah, S.Pd', '2025-02-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Peringatan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 2, 'XI TKJ 1'),
(3, 50, 3, 'Bagas Saputra', 3, 'Tidur saat pembelajaran', 1, 3, 'Maya Permata Endah, S. Pd', '2025-03-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(4, 50, 4, 'Citra Lestari', 4, 'Tidak melaksanakan piket sesuai jadwal', 1, 4, 'Dody Jaenudin, S.Pd.', '2025-04-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 4, 'X TKR 1'),
(5, 50, 5, 'Daffa Ramadhan', 5, 'Mengabaikan instruksi guru', 1, 5, 'Dedi Kardiman, S. Pd', '2025-05-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 5, 'XI TSM 1'),
(6, 50, 6, 'Elsa Putri', 6, 'Tidak mematuhi tata tertib sekolah', 1, 6, 'Uca Musa Yusenda, S.Pd', '2025-06-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 6, 'XII RPL 1'),
(7, 50, 7, 'Fajar Nugraha', 7, 'Tidak mengikuti kegiatan sekolah tanpa alasan jelas', 1, 7, 'Drs. Ahmad Nur Mukhlis', '2025-07-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 7, 'X TKJ 1'),
(8, 50, 8, 'Gina Maharani', 8, 'Menyontek saat ulangan', 1, 8, 'Drs. Heri', '2025-08-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(9, 50, 9, 'Hafiz Akbar', 9, 'Memalsukan tanda tangan guru atau orang tua', 1, 9, 'Asep Bastaman', '2025-09-09', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 9, 'XII TKR 1'),
(10, 50, 10, 'Intan Permata', 10, 'Mengulangi pelanggaran setelah pembinaan', 1, 10, 'H. Encep Maemun, S.T., M.Pd', '2025-10-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 10, 'X TSM 1'),
(11, 50, 11, 'Jovan Setiawan', 11, 'Terlambat datang ke sekolah', 2, 11, 'Opik', '2025-11-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(12, 50, 12, 'Kania Salsabila', 12, 'Terlambat masuk kelas setelah istirahat', 2, 12, 'Ida Farida, S. Pd', '2025-12-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 12, 'XII TKJ 1'),
(13, 50, 13, 'Lutfi Hakim', 13, 'Terlambat masuk kelas tanpa alasan', 2, 13, 'Idah Mahmudah, S.Pd', '2025-01-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(14, 50, 14, 'Maya Anggraini', 14, 'Tidak masuk kelas tanpa izin', 2, 14, 'Drs. Muhamad Ali', '2025-02-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 14, 'XI TKR 1'),
(15, 50, 15, 'Naufal Rizky', 15, 'Tidak mengikuti pelajaran tanpa izin', 2, 15, 'Helmi Ali Akbar, S.Pd.I', '2025-03-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 15, 'XII TSM 1'),
(16, 50, 16, 'Olivia Amelia', 16, 'Tidak mengikuti upacara tanpa izin', 2, 16, 'Syeni Suswarini, S.Pd', '2025-04-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(17, 50, 17, 'Putra Wijaya', 17, 'Membolos pada jam pelajaran', 2, 17, 'Wiwid Yulianti, S.Pd', '2025-05-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 17, 'XI TKJ 2'),
(18, 50, 18, 'Qonita Zahra', 18, 'Pulang sebelum waktunya tanpa izin', 2, 18, 'Ansor Muhajir, S.T', '2025-06-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(19, 50, 19, 'Raka Firmansyah', 19, 'Meninggalkan sekolah tanpa izin', 2, 19, 'Ogi Gaosun Gias, S.Pd.I', '2025-07-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 19, 'X TKR 2'),
(20, 50, 20, 'Salsa Nabila', 20, 'Memalsukan surat izin atau surat sakit', 2, 20, 'Sandi Mulyana, S.Pd', '2025-08-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 20, 'XI TSM 2'),
(22, 50, 22, 'Ulfa Nuraini', 22, 'Tidak memakai ikat pinggang sesuai ketentuan', 3, 22, 'Ai Wahidah, S.Ag', '2025-10-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 22, 'X TKJ 2'),
(23, 50, 23, 'Vino Alfarizi', 23, 'Kaus kaki tidak sesuai ketentuan', 3, 23, 'Chandra Aditya Yusup, S.Pd', '2025-11-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(24, 50, 24, 'Wulan Sari', 24, 'Tidak memakai atribut lengkap', 3, 24, 'Arif Kusmawan, S.Pd', '2025-12-24', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 24, 'XII TKR 2'),
(25, 50, 25, 'Yoga Pratama', 25, 'Tidak memakai topi saat upacara', 3, 25, 'Asep Taufiq Hidayat, S.Kom', '2025-01-25', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 25, 'X TSM 2'),
(26, 50, 26, 'Zahra Aulia', 26, 'Memakai sepatu tidak sesuai ketentuan', 3, 26, 'Rendy Ichsan Kusumah, S.T', '2025-02-26', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(27, 50, 27, 'Andika Kurnia', 27, 'Memakai seragam tidak sesuai jadwal', 3, 27, 'Feri Rahmawan, S.Pd', '2025-03-27', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 27, 'XII TKJ 2'),
(28, 50, 28, 'Bella Safitri', 28, 'Mengeluarkan baju saat diwajibkan rapi', 3, 28, 'Ari Sandriana, S.Kom', '2025-04-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(29, 50, 29, 'Cahyo Aditya', 29, 'Memakai aksesori yang dilarang sekolah', 3, 29, 'Yaneu Nurdian, S.Pd, M.Pd', '2025-05-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 29, 'XI TKR 2'),
(30, 50, 30, 'Dinda Maharani', 30, 'Mengubah model seragam tanpa izin', 3, 30, 'Ucu Muhammad Khoer N, S.Pd', '2025-06-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 30, 'XII TSM 2'),
(31, 50, 31, 'Eko Saputra', 31, 'Berbicara saat guru menjelaskan', 4, 31, 'Mira Dwi Rahmawati, S.Ag', '2025-07-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(32, 50, 32, 'Fitri Handayani', 32, 'Mengganggu teman saat belajar', 4, 32, 'Aldi Maulana, S.Pd', '2025-08-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 32, 'XI TKJ 3'),
(33, 50, 33, 'Galih Ramadhan', 33, 'Membuat keributan di kelas', 4, 33, 'Eli Rosliah, S.Pd', '2025-09-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(34, 50, 34, 'Hana Aprilia', 34, 'Menggunakan HP tanpa izin saat belajar', 4, 34, 'Isni Nur Fitriani, S.Pd', '2025-10-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 34, 'X TKR 3'),
(35, 50, 35, 'Ilham Fauzan', 35, 'Berkata kasar kepada teman', 4, 35, 'Aanisah Putri Nur Aini, S.Pd', '2025-11-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 35, 'XI TSM 3'),
(37, 50, 37, 'Kevin Setiawan', 37, 'Berbicara tidak sopan kepada guru', 4, 37, 'Lilis Rustandy Mawardi, S.Pd., M.Pd', '2025-01-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 37, 'X TKJ 3'),
(38, 50, 38, 'Larasati Putri', 38, 'Membawa barang yang dilarang sekolah', 4, 38, 'Yusrin Noer Sabrina, S.Pd', '2025-02-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI BD 1'),
(39, 50, 39, 'Miko Pratama', 39, 'Merusak fasilitas sekolah dengan sengaja', 4, 39, 'Senny Indayani, S.Pd', '2025-03-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 39, 'XII TKR 3'),
(40, 50, 40, 'Nadia Safira', 40, 'Melakukan perundungan atau perkelahian', 4, 40, 'Ilma Amalia, S.Pd', '2025-04-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 40, 'X TSM 3'),
(41, 50, 41, 'Rian Hidayat', 41, 'Membuang sampah sembarangan', 5, 41, 'Alvira Sofwatunnisa, S.Pd', '2025-05-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI RPL 1'),
(42, 50, 42, 'Siti Aisyah', 42, 'Meninggalkan sampah setelah makan', 5, 42, 'Risna Andriyani, S.Pd', '2025-06-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 42, 'XII TKJ 3'),
(43, 50, 43, 'Taufik Haryanto', 43, 'Membiarkan meja dan kursi tidak rapi', 5, 43, 'Dra. Tintin Noerhayati', '2025-07-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 2, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X BD 1'),
(44, 50, 44, 'Vania Putri', 44, 'Meludah sembarangan', 5, 44, 'Rizkia Fahira, S.Pd', '2025-08-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 44, 'XI TKR 3'),
(45, 50, 45, 'Wahyu Nugroho', 45, 'Tidak membersihkan area piket', 5, 45, 'Frasetio', '2025-09-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 3, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 45, 'XII TSM 3'),
(46, 50, 46, 'Yuni Kartika', 46, 'Tidak menjaga kebersihan kelas', 5, 46, 'Ratnaningsih Hatil Aeni, S.Pd.', '2025-10-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X RPL 2'),
(47, 50, 47, 'Zaki Firmansyah', 47, 'Tidak menjaga kebersihan toilet', 5, 47, 'Dheny Darajatul Hasani, S.Pd.', '2025-11-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 47, 'XI TKJ 4'),
(48, 50, 48, 'Ari Maulana', 48, 'Mengotori fasilitas sekolah dengan sengaja', 5, 48, 'Fahrullah, S.Pd', '2025-12-21', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII BD 1'),
(49, 50, 49, 'Bima Setiawan', 49, 'Mencoret meja, kursi, atau dinding', 5, 49, 'Aan Andriana, S.T', '2025-01-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 49, 'X TKR 4'),
(50, 50, 50, 'Cindy Oktavia', 50, 'Merusak tanaman atau taman sekolah', 5, 50, 'Fitri Fauziah, S.T', '2025-02-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 35, 'XI TSM 3'),
(51, 50, 10, 'Intan Permata', 6, 'Tidak mematuhi tata tertib sekolah', 1, 2, 'Ai Nuryaidah, S.Pd', '2026-10-20', 'Tidak Patuh', 5, 'Dipanggil ortu', 'Diproses', '2026-10-20 01:31:15', '2026-10-20 01:31:15', 10, 'X TSM 1'),
(52, 50, 52, 'Nadia Zulfa Soraya', 26, 'Memakai sepatu tidak sesuai ketentuan', 3, 2, 'Ai Nuryaidah, S.Pd', '2026-10-20', 'Pakai Flastshoes', 5, 'Flastshoes nya diambil', 'Selesai', '2026-10-20 02:30:14', '2026-10-20 02:30:14', 21, 'XII RPL 2'),
(53, 50, 53, 'Fatir Rizky', 11, 'Terlambat datang ke sekolah', 2, 2, 'Ai Nuryaidah, S.Pd', '2026-11-03', 'Alasan kesiangan', 2, 'Diomongin doang supaya ga telat lagi datang harus lebih awal', 'Selesai', '2026-11-03 04:31:19', '2026-11-03 04:31:19', 39, 'XII TKR 3'),
(54, 50, 42, 'Siti Aisyah', 22, 'Tidak memakai ikat pinggang sesuai ketentuan', 3, 51, 'Nadia Zulfa', '2026-11-04', 'jkfkl', 2, 'sudah diberi peringatan', 'Ditindaklanjuti', '2026-11-04 04:04:31', '2026-11-04 04:04:31', 42, 'XII TKJ 3'),
(55, 50, 54, 'Anisa Amirula', 13, 'Terlambat masuk kelas tanpa alasan', 2, 2, 'Ai Nuryaidah, S.Pd', '2026-11-04', 'telat', 3, 'diomongin doang', 'Selesai', '2026-11-04 04:22:06', '2026-11-04 04:22:06', 21, 'XII RPL 2'),
(56, 50, 2, 'Aulia Rahma', 17, 'Membolos pada jam pelajaran', 2, 4, 'Dody Jaenudin, S.Pd.', '2026-11-05', 'bg', 10, 'gng', 'Selesai', '2026-11-05 02:40:45', '2026-11-05 02:40:45', 2, 'XI TKJ 1'),
(57, 50, 55, 'Adittia Nur Lantip', 1, 'Tidak membawa perlengkapan belajar', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak membawa perlengkapan belajar', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(58, 50, 56, 'Agnia Rosya Noerillah', 2, 'Tidak mengerjakan tugas', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak mengerjakan tugas', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(59, 50, 57, 'Aradea Kandi', 3, 'Tidur saat pembelajaran', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidur saat pembelajaran', 3, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(60, 50, 58, 'Arfa Diesa Okamaleliza', 4, 'Tidak melaksanakan piket sesuai jadwal', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak melaksanakan piket sesuai jadwal', 3, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(61, 50, 59, 'Fitri Nur Syami', 5, 'Mengabaikan instruksi guru', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Mengabaikan instruksi guru', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(62, 50, 60, 'Junita Putri Nur Rahman', 6, 'Tidak mematuhi tata tertib sekolah', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak mematuhi tata tertib sekolah', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(63, 50, 61, 'Mohammad Atalarik Ali Syahbana', 7, 'Tidak mengikuti kegiatan sekolah tanpa alasan jelas', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak mengikuti kegiatan sekolah tanpa alasan jelas', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(64, 50, 62, 'Muhamad Fahril Fahrurozi', 8, 'Menyontek saat ulangan', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Menyontek saat ulangan', 10, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(65, 50, 63, 'Muhammad Adnan Syaeful Islami', 9, 'Memalsukan tanda tangan guru atau orang tua', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Memalsukan tanda tangan guru atau orang tua', 15, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(66, 50, 64, 'Muhammad Zahru Aqila Fikri', 10, 'Mengulangi pelanggaran setelah pembinaan', 1, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Mengulangi pelanggaran setelah pembinaan', 15, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(67, 50, 65, 'Nazril Ilham', 11, 'Terlambat datang ke sekolah', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Terlambat datang ke sekolah', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(68, 50, 66, 'Puspita Dewi', 12, 'Terlambat masuk kelas setelah istirahat', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Terlambat masuk kelas setelah istirahat', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(69, 50, 67, 'Rakha Muhammad Haykal', 14, 'Tidak masuk kelas tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak masuk kelas tanpa izin', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(70, 50, 68, 'Rani Meilani', 15, 'Tidak mengikuti pelajaran tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak mengikuti pelajaran tanpa izin', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(71, 50, 69, 'Reihan Muzaki', 16, 'Tidak mengikuti upacara tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak mengikuti upacara tanpa izin', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(72, 50, 70, 'Resa Juliansah', 17, 'Membolos pada jam pelajaran', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Membolos pada jam pelajaran', 10, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(73, 50, 71, 'Rizki Permata Sugianto', 18, 'Pulang sebelum waktunya tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Pulang sebelum waktunya tanpa izin', 10, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(74, 50, 72, 'Sahira Firaj', 19, 'Meninggalkan sekolah tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Meninggalkan sekolah tanpa izin', 10, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(75, 50, 73, 'Sendi Mardian', 20, 'Memalsukan surat izin atau surat sakit', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Memalsukan surat izin atau surat sakit', 15, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(76, 50, 74, 'Shahaidan Nizzar', 21, 'Seragam kurang rapi', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Seragam kurang rapi', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(77, 50, 75, 'Sherly Satriani', 22, 'Tidak memakai ikat pinggang sesuai ketentuan', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak memakai ikat pinggang sesuai ketentuan', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(78, 50, 76, 'Sofi Nurjanah', 23, 'Kaus kaki tidak sesuai ketentuan', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Kaus kaki tidak sesuai ketentuan', 2, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(79, 50, 77, 'Suraiya Salsabila Azzahra', 24, 'Tidak memakai atribut lengkap', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak memakai atribut lengkap', 3, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(80, 50, 78, 'Toni Antonio', 25, 'Tidak memakai topi saat upacara', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-09', 'Tidak memakai topi saat upacara', 5, 'Menunggu tindak lanjut wali kelas', 'Diproses', '2026-11-09 07:38:11', '2026-11-09 07:38:11', 21, 'XII RPL 2'),
(88, 50, 64, 'Muhammad Zahru Aqila Fikri', 8, 'Menyontek saat ulangan', 1, 4, 'Dody Jaenudin, S.Pd.', '2026-11-09', 'nyontek terus', 10, 'sudah', 'Selesai', '2026-11-09 08:10:13', '2026-11-09 08:10:13', 21, 'XII RPL 2'),
(89, 50, 52, 'Nadia Zulfa Soraya', 2, 'Tidak mengerjakan tugas', 1, 1, 'Wawan Kurniawan, S.Pd.I', '2026-11-09', 'fghjzhjfg', 2, '', 'Selesai', '2026-11-09 08:27:03', '2026-11-09 08:27:03', 21, 'XII RPL 2'),
(90, 50, 39, 'Miko Pratama', 39, 'Merusak fasilitas sekolah dengan sengaja', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa sengaja merusak fasilitas milik sekolah.', 15, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(91, 50, 53, 'Fatir Rizky', 3, 'Tidur saat pembelajaran', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidur saat proses pembelajaran berlangsung.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(92, 50, 86, 'Adam Lasmana Nugraha', 36, 'Mengganggu kegiatan sekolah', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa mengganggu pelaksanaan kegiatan resmi sekolah.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(93, 50, 87, 'Afep Saepul Hidayah', 37, 'Berbicara tidak sopan kepada guru', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa berkata tidak sopan kepada guru.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(94, 50, 88, 'Agis Pratama', 38, 'Membawa barang yang dilarang sekolah', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa membawa barang yang secara jelas dilarang tata tertib sekolah.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(95, 50, 89, 'Dawam Ariasa Putra', 39, 'Merusak fasilitas sekolah dengan sengaja', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa sengaja merusak fasilitas milik sekolah.', 15, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(96, 50, 90, 'Dika Aditya', 40, 'Melakukan perundungan atau perkelahian', 4, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa melakukan perundungan atau terlibat perkelahian yang terbukti.', 20, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(97, 50, 91, 'Diki Saputra', 41, 'Membuang sampah sembarangan', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa membuang sampah bukan pada tempat yang disediakan.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(98, 50, 92, 'Dimas Anugrah Pranatama', 42, 'Meninggalkan sampah setelah makan', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa meninggalkan sampah makanan atau minuman di area sekolah.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(99, 50, 93, 'Ergi Zulvian Razvi', 43, 'Membiarkan meja dan kursi tidak rapi', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa meninggalkan meja dan kursi dalam keadaan tidak rapi.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(100, 50, 94, 'Evan Rasendriya', 44, 'Meludah sembarangan', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa meludah bukan pada tempat yang semestinya.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(101, 50, 95, 'Faiz Fauzan Abdurahman', 45, 'Tidak membersihkan area piket', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak membersihkan area yang menjadi tanggung jawab piketnya.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(102, 50, 96, 'Guntur Muhamad Azril', 46, 'Tidak menjaga kebersihan kelas', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak menjaga kebersihan kelas sesuai tanggung jawabnya.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(103, 50, 97, 'Hiban Sa\'ban', 47, 'Tidak menjaga kebersihan toilet', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa mengotori toilet dan tidak menjaga kebersihan fasilitas tersebut.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(104, 50, 98, 'Hilman Sohibul Kiyam', 48, 'Mengotori fasilitas sekolah dengan sengaja', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa sengaja mengotori fasilitas sekolah.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(105, 50, 99, 'Ijad Rayi Nurfais Ansori', 49, 'Mencoret meja, kursi, atau dinding', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa mencoret fasilitas sekolah tanpa izin.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(106, 50, 100, 'Iqbal Muharrom', 50, 'Merusak tanaman atau taman sekolah', 5, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa sengaja merusak tanaman atau fasilitas taman sekolah.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(107, 50, 101, 'Izaz Syakir', 1, 'Tidak membawa perlengkapan belajar', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak membawa perlengkapan belajar yang diperlukan.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(108, 50, 102, 'Moch Faizal Gilang Permana', 2, 'Tidak mengerjakan tugas', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak menyelesaikan tugas yang diberikan guru.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(109, 50, 103, 'Muhamad Rizky Gozali', 3, 'Tidur saat pembelajaran', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidur saat proses pembelajaran berlangsung.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(110, 50, 104, 'Muhammad Rikman Nulhakim', 4, 'Tidak melaksanakan piket sesuai jadwal', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak melaksanakan tugas piket sesuai jadwal.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(111, 50, 105, 'Muhammad Siddiq Ibrahim Sobari', 5, 'Mengabaikan instruksi guru', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa mengabaikan instruksi guru yang berkaitan dengan kegiatan belajar.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(112, 50, 106, 'Nizar Suryana', 6, 'Tidak mematuhi tata tertib sekolah', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa melanggar ketentuan tata tertib sekolah yang telah disosialisasikan.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(113, 50, 107, 'Rasya Abdul Basith', 7, 'Tidak mengikuti kegiatan sekolah tanpa alasan jelas', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak mengikuti kegiatan wajib sekolah tanpa keterangan yang dapat dipertanggungjawabkan.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(114, 50, 108, 'Rausan Vikri', 8, 'Menyontek saat ulangan', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa menggunakan jawaban atau bantuan yang tidak diizinkan saat ulangan.', 10, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(115, 50, 109, 'Revaldi Nurwansah', 9, 'Memalsukan tanda tangan guru atau orang tua', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa memalsukan tanda tangan pada dokumen sekolah.', 15, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(116, 50, 110, 'Revan Rudiansah', 10, 'Mengulangi pelanggaran setelah pembinaan', 1, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa mengulangi pelanggaran setelah menjalani pembinaan resmi.', 15, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(117, 50, 111, 'Rizki Raditya', 11, 'Terlambat datang ke sekolah', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tiba setelah waktu masuk sekolah yang ditentukan.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(118, 50, 112, 'Roby Orie Al Rasya', 12, 'Terlambat masuk kelas setelah istirahat', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa terlambat kembali ke kelas setelah waktu istirahat.', 2, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(119, 50, 113, 'Ruly Rudiansah', 13, 'Terlambat masuk kelas tanpa alasan', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa terlambat masuk kelas tanpa alasan yang jelas.', 3, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(120, 50, 114, 'Syahdan Rasdani', 14, 'Tidak masuk kelas tanpa izin', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak berada di kelas saat pembelajaran tanpa izin.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(121, 50, 115, 'Taba Haerul Salim', 15, 'Tidak mengikuti pelajaran tanpa izin', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak mengikuti kegiatan pembelajaran tanpa izin guru.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(122, 50, 116, 'Tirta Nur Zamzam', 16, 'Tidak mengikuti upacara tanpa izin', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa tidak mengikuti upacara sekolah tanpa izin atau alasan yang sah.', 5, 'Data simulasi untuk pengujian sistem', 'Diproses', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(123, 50, 117, 'Zilhad Ayalthuhaq', 17, 'Membolos pada jam pelajaran', 2, 42, 'Bu Risna Andriyani, S.Pd', '2026-11-10', 'Siswa sengaja menghindari kegiatan belajar pada jam pelajaran.', 10, 'Data simulasi untuk pengujian sistem', 'Selesai', '2026-11-10 01:59:41', '2026-11-10 01:59:41', 39, 'XII TKR 3'),
(153, 50, 53, 'Fatir Rizky', 18, 'Pulang sebelum waktunya tanpa izin', 2, 4, 'Dody Jaenudin, S.Pd.', '2026-11-10', 'pulang duluan', 10, 'sudah dikasi tau', 'Selesai', '2026-11-10 02:34:35', '2026-11-10 02:34:35', 39, 'XII TKR 3'),
(154, 50, 150, 'ALFIN RAHADIAN', 1, 'Tidak membawa perlengkapan belajar', 1, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak membawa perlengkapan belajar yang diperlukan.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(155, 50, 151, 'ALISA RAMADHANI', 11, 'Terlambat datang ke sekolah', 2, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tiba setelah waktu masuk sekolah yang ditentukan.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(156, 50, 152, 'ALMA AURA LIVIA', 21, 'Seragam kurang rapi', 3, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Seragam tidak dikenakan dengan rapi sesuai ketentuan sekolah.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(157, 50, 153, 'ANIS KHOERUNNISA NUR RAMADANI', 31, 'Berbicara saat guru menjelaskan', 4, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa berbicara sehingga mengganggu proses penjelasan guru.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(158, 50, 154, 'AZMIL AMIRA', 41, 'Membuang sampah sembarangan', 5, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa membuang sampah bukan pada tempat yang disediakan.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(159, 50, 155, 'AZRIL AKBAR FAWJI ABDILLAH', 2, 'Tidak mengerjakan tugas', 1, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak menyelesaikan tugas yang diberikan guru.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(160, 50, 156, 'ERLANGGA SAPUTRA HIDAYAT', 12, 'Terlambat masuk kelas setelah istirahat', 2, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa terlambat kembali ke kelas setelah waktu istirahat.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(161, 50, 157, 'HILMA HUMMA TUNNAJAN', 24, 'Tidak memakai atribut lengkap', 3, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak memakai atribut seragam yang diwajibkan.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(162, 50, 158, 'ILIMA NAJYA', 32, 'Mengganggu teman saat belajar', 4, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa mengganggu teman sehingga menghambat kegiatan belajar.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(163, 50, 159, 'JULIANDA DHARMA PANCESTU', 42, 'Meninggalkan sampah setelah makan', 5, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa meninggalkan sampah makanan atau minuman di area sekolah.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(164, 50, 160, 'KAYILA NUR FADILAN', 3, 'Tidur saat pembelajaran', 1, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidur saat proses pembelajaran berlangsung.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(165, 50, 161, 'MOZA DINA NABILLAH', 13, 'Terlambat masuk kelas tanpa alasan', 2, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa terlambat masuk kelas tanpa alasan yang jelas.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(166, 50, 162, 'MUHAMAD ALFARIZ', 25, 'Tidak memakai topi saat upacara', 3, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak memakai topi saat diwajibkan dalam upacara.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(167, 50, 163, 'MUHAMAD FAHRIN ADAM', 33, 'Membuat keributan di kelas', 4, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa membuat keributan yang mengganggu pembelajaran.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(168, 50, 164, 'MUHAMMAD ZIDANE MAULIDAN', 43, 'Membiarkan meja dan kursi tidak rapi', 5, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa meninggalkan meja dan kursi dalam keadaan tidak rapi.', 2, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(169, 50, 165, 'MUHAMMAD ZULFAN NURALFANSYAH', 4, 'Tidak melaksanakan piket sesuai jadwal', 1, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak melaksanakan tugas piket sesuai jadwal.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(170, 50, 166, 'NATIYA NUR AZKIYA', 14, 'Tidak masuk kelas tanpa izin', 2, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak berada di kelas saat pembelajaran tanpa izin.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(171, 50, 167, 'NOVITA PUSPITASARI', 26, 'Memakai sepatu tidak sesuai ketentuan', 3, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa menggunakan sepatu yang tidak sesuai aturan sekolah.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(172, 50, 168, 'NUCKY ANDREAS FEBRIAN', 34, 'Menggunakan HP tanpa izin saat belajar', 4, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa menggunakan telepon genggam saat pembelajaran tanpa izin.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(173, 50, 169, 'RADITYA JETI PRASETYA', 44, 'Meludah sembarangan', 5, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa meludah bukan pada tempat yang semestinya.', 3, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(174, 50, 170, 'RANGGA RAMADIYANTO NURHAKIM', 5, 'Mengabaikan instruksi guru', 1, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa mengabaikan instruksi guru yang berkaitan dengan kegiatan belajar.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(175, 50, 171, 'RISQI MUHAMAD IHWANI', 15, 'Tidak mengikuti pelajaran tanpa izin', 2, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa tidak mengikuti kegiatan pembelajaran tanpa izin guru.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(176, 50, 172, 'SALWA SABILATUL BARKAN', 27, 'Memakai seragam tidak sesuai jadwal', 3, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa memakai seragam yang tidak sesuai jadwal yang ditetapkan.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(177, 50, 173, 'TAZKIA ZAHRA', 35, 'Berkata kasar kepada teman', 4, 28, 'Ari Sandriana, S.Kom', '2026-11-10', 'Siswa menggunakan kata-kata kasar kepada teman.', 5, 'Pembinaan dan teguran (simulasi)', 'Selesai', '2026-11-10 02:37:21', '2026-11-10 02:37:21', 6, 'XII RPL 1'),
(185, 50, 53, 'Fatir Rizky', 26, 'Memakai sepatu tidak sesuai ketentuan', 3, 32, 'Aldi Maulana, S.Pd', '2026-11-10', 'nsjkfkjhnsgf', 5, '', 'Diproses', '2026-11-10 03:28:49', '2026-11-10 03:28:49', 39, 'XII TKR 3'),
(186, 50, 37, 'Kevin Setiawan', 18, 'Pulang sebelum waktunya tanpa izin', 2, 32, 'Aldi Maulana, S.Pd', '2026-11-10', 'jnk', 10, '', 'Diproses', '2026-11-10 04:40:27', '2026-11-10 04:40:27', 37, 'X TKJ 3'),
(187, 50, 19, 'Raka Firmansyah', 1, 'Tidak membawa perlengkapan belajar', 1, 4, 'Dody Jaenudin, S.Pd.', '2026-11-10', 'vhgh', 2, '', 'Diproses', '2026-11-10 04:55:22', '2026-11-10 04:55:22', 19, 'X TKR 2'),
(188, 50, 53, 'Fatir Rizky', 11, 'Terlambat datang ke sekolah', 2, 4, 'Dody Jaenudin, S.Pd.', '2026-11-10', 'kesiangan wae', 2, '', 'Selesai', '2026-11-10 08:01:23', '2026-11-10 08:01:23', 39, 'XII TKR 3'),
(189, 50, 53, 'Fatir Rizky', 11, 'Terlambat datang ke sekolah', 2, 4, 'Dody Jaenudin, S.Pd.', '2026-11-10', 'BDBGNBDNNG', 2, 'DIBERIKAN PENGARAHAN', 'Selesai', '2026-11-10 08:58:56', '2026-11-10 08:58:56', 39, 'XII TKR 3'),
(190, 50, 52, 'Nadia Zulfa Soraya', 12, 'Terlambat masuk kelas setelah istirahat', 2, 42, 'Risna Andriyani, S.Pd', '2026-11-10', 'dgd', 2, 'bjbj', 'Selesai', '2026-11-10 12:49:03', '2026-11-10 12:49:03', 21, 'XII RPL 2');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(10) NOT NULL,
  `nisn` varchar(10) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa`
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
(37, '20260037', '0070000037', 'Kevin Setiawan', 'L', '2007-01-10', 'Jl. Pendidikan No. 37, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(38, '20260038', '0080000038', 'Larasati Putri', 'P', '2008-02-11', 'Jl. Pendidikan No. 38, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(39, '20260039', '0090000039', 'Miko Pratama', 'L', '2009-03-12', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
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
(50, '20260050', '0080000050', 'Cindy Okta', 'P', '2008-05-23', 'Paseh, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22'),
(52, '20260051', '0080000051', 'Nadia Zulfa Soraya', 'P', '2009-06-21', 'Jln drs moch hatta kalangsari', 1, '2026-10-20 01:13:07', '2026-10-20 01:13:07'),
(53, '20260052', '0080000052', 'Fatir Rizky', 'L', '2008-07-07', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-03 04:30:10', '2026-11-03 04:30:10'),
(54, '20260053', '0080000053', 'Anisa Amirula', 'P', '2009-12-30', 'Paseh', 1, '2026-11-04 04:20:46', '2026-11-04 04:20:46'),
(55, '20260054', '0080000054', 'Adittia Nur Lantip', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(56, '20260055', '0090000055', 'Agnia Rosya Noerillah', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(57, '20260056', '0100000056', 'Aradea Kandi', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(58, '20260057', '0070000057', 'Arfa Diesa Okamaleliza', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(59, '20260058', '0080000058', 'Fitri Nur Syami', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(60, '20260059', '0090000059', 'Junita Putri Nur Rahman', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(61, '20260060', '0100000060', 'Mohammad Atalarik Ali Syahbana', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(62, '20260061', '0070000061', 'Muhamad Fahril Fahrurozi', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(63, '20260062', '0080000062', 'Muhammad Adnan Syaeful Islami', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(64, '20260063', '0090000063', 'Muhammad Zahru Aqila Fikri', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(65, '20260064', '0100000064', 'Nazril Ilham', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(66, '20260065', '0070000065', 'Puspita Dewi', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(67, '20260066', '0080000066', 'Rakha Muhammad Haykal', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(68, '20260067', '0090000067', 'Rani Meilani', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(69, '20260068', '0100000068', 'Reihan Muzaki', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(70, '20260069', '0070000069', 'Resa Juliansah', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(71, '20260070', '0080000070', 'Rizki Permata Sugianto', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(72, '20260071', '0090000071', 'Sahira Firaj', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(73, '20260072', '0100000072', 'Sendi Mardian', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(74, '20260073', '0070000073', 'Shahaidan Nizzar', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(75, '20260074', '0080000074', 'Sherly Satriani', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(76, '20260075', '0090000075', 'Sofi Nurjanah', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(77, '20260076', '0100000076', 'Suraiya Salsabila Azzahra', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(78, '20260077', '0070000077', 'Toni Antonio', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-05 04:10:27', '2026-11-05 04:10:27'),
(86, '20260078', '9990000078', 'Adam Lasmana Nugraha', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(87, '20260079', '9990000079', 'Afep Saepul Hidayah', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(88, '20260080', '9990000080', 'Agis Pratama', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(89, '20260081', '9990000081', 'Dawam Ariasa Putra', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(90, '20260082', '9990000082', 'Dika Aditya', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(91, '20260083', '9990000083', 'Diki Saputra', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(92, '20260084', '9990000084', 'Dimas Anugrah Pranatama', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(93, '20260085', '9990000085', 'Ergi Zulvian Razvi', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(94, '20260086', '9990000086', 'Evan Rasendriya', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(95, '20260087', '9990000087', 'Faiz Fauzan Abdurahman', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(96, '20260088', '9990000088', 'Guntur Muhamad Azril', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(97, '20260089', '9990000089', 'Hiban Sa\'ban', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(98, '20260090', '9990000090', 'Hilman Sohibul Kiyam', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(99, '20260091', '9990000091', 'Ijad Rayi Nurfais Ansori', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(100, '20260092', '9990000092', 'Iqbal Muharrom', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(101, '20260093', '9990000093', 'Izaz Syakir', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(102, '20260094', '9990000094', 'Moch Faizal Gilang Permana', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(103, '20260095', '9990000095', 'Muhamad Rizky Gozali', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(104, '20260096', '9990000096', 'Muhammad Rikman Nulhakim', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(105, '20260097', '9990000097', 'Muhammad Siddiq Ibrahim Sobari', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(106, '20260098', '9990000098', 'Nizar Suryana', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(107, '20260099', '9990000099', 'Rasya Abdul Basith', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(108, '20260100', '9990000100', 'Rausan Vikri', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(109, '20260101', '9990000101', 'Revaldi Nurwansah', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(110, '20260102', '9990000102', 'Revan Rudiansah', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(111, '20260103', '9990000103', 'Rizki Raditya', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(112, '20260104', '9990000104', 'Roby Orie Al Rasya', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(113, '20260105', '9990000105', 'Ruly Rudiansah', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(114, '20260106', '9990000106', 'Syahdan Rasdani', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(115, '20260107', '9990000107', 'Taba Haerul Salim', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(116, '20260108', '9990000108', 'Tirta Nur Zamzam', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(117, '20260109', '9990000109', 'Zilhad Ayalthuhaq', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 01:46:55', '2026-11-10 01:46:55'),
(150, '20260110', '9990000110', 'ALFIN RAHADIAN', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(151, '20260111', '9990000111', 'ALISA RAMADHANI', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(152, '20260112', '9990000112', 'ALMA AURA LIVIA', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(153, '20260113', '9990000113', 'ANIS KHOERUNNISA NUR RAMADANI', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(154, '20260114', '9990000114', 'AZMIL AMIRA', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(155, '20260115', '9990000115', 'AZRIL AKBAR FAWJI ABDILLAH', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(156, '20260116', '9990000116', 'ERLANGGA SAPUTRA HIDAYAT', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(157, '20260117', '9990000117', 'HILMA HUMMA TUNNAJAN', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(158, '20260118', '9990000118', 'ILIMA NAJYA', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(159, '20260119', '9990000119', 'JULIANDA DHARMA PANCESTU', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(160, '20260120', '9990000120', 'KAYILA NUR FADILAN', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(161, '20260121', '9990000121', 'MOZA DINA NABILLAH', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(162, '20260122', '9990000122', 'MUHAMAD ALFARIZ', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(163, '20260123', '9990000123', 'MUHAMAD FAHRIN ADAM', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(164, '20260124', '9990000124', 'MUHAMMAD ZIDANE MAULIDAN', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(165, '20260125', '9990000125', 'MUHAMMAD ZULFAN NURALFANSYAH', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(166, '20260126', '9990000126', 'NATIYA NUR AZKIYA', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(167, '20260127', '9990000127', 'NOVITA PUSPITASARI', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(168, '20260128', '9990000128', 'NUCKY ANDREAS FEBRIAN', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(169, '20260129', '9990000129', 'RADITYA JETI PRASETYA', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(170, '20260130', '9990000130', 'RANGGA RAMADIYANTO NURHAKIM', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(171, '20260131', '9990000131', 'RISQI MUHAMAD IHWANI', 'L', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(172, '20260132', '9990000132', 'SALWA SABILATUL BARKAN', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04'),
(173, '20260133', '9990000133', 'TAZKIA ZAHRA', 'P', '2008-01-01', 'Jl. Pendidikan, Tasikmalaya', 1, '2026-11-10 02:27:04', '2026-11-10 02:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `t_tahun_ajaran`
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
-- Dumping data for table `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(2, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(3, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(4, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(5, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(6, '2025/2026', '2025-07-01', '2026-06-30', 0, '2026-09-23 03:45:34', '2026-11-03 04:16:19'),
(50, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-09-23 03:45:34', '2026-11-03 04:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
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
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'User 1', 'user1@sekolah.sch.id', NULL, '$2y$10$42wZ2hUB/8h3oxmQJ6YJI.zNKkvVS.R2lIhy0ffVXGqS.AnplFWvG', NULL, 'admin', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(2, 'Ai Nuryaidah, S.Pd', 'ai.nuryaidah@gmail.com', NULL, '$2y$12$dVcdTVcAUeoX3civzu64sORpUWgod6ZLcne343KinypjBvdr42ID6', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(3, 'Maya Permata Endah, S. Pd', 'maya.permata.endah@gmail.com', NULL, '$2y$12$1iNWgBIlYvLZwM4eCwGdg.J3VqxlS48a3BGb2nXLcadsNmUUGVKmy', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(4, 'Dody Jaenudin, S.Pd.', 'dody.jaenudin@gmail.com', NULL, '$2y$12$hUi2WpyZDpfk0ZYCBXKjVuCAKu5oPF5ayFIICoNgyYhFfuTM9gXfm', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(5, 'Dedi Kardiman, S. Pd', 'dedi.kardiman@gmail.com', NULL, '$2y$12$NfDnY8fs2yE0UUMIx.mxmOdbXSjOniz/Bi8CpCgJ949ge/4XQC5by', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(6, 'Uca Musa Yusenda, S.Pd', 'uca.musa.yusenda@gmail.com', NULL, '$2y$12$pMknFz9p2yxbtD4KgMFC.ev5mAkGQhjZ5pqsWhuN3QIeBj7ZbBjMe', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(7, 'Drs. Ahmad Nur Mukhlis', 'ahmad.nur.mukhlis@gmail.com', NULL, '$2y$12$BJ/ckfMxMivmVHNp08Xn/OwGHwpHiCsgZVcNACi3julXrh/B8Hs9G', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(8, 'Drs. Heri', 'heri@gmail.com', NULL, '$2y$12$LiSmoQiGfKi99QVKrdpGd.7c8jZAfi0bJxnt4/Mu9OsGWcwOWhs7C', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(9, 'Asep Bastaman', 'asep.bastaman@gmail.com', NULL, '$2y$12$aPUwa8VyewzTvhNXSndew.4OQCmIfYp9b09IMOhvEdsvOnFRz5fAq', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(10, 'H. Encep Maemun, S.T., M.Pd', 'encep.maemun@gmail.com', NULL, '$2y$12$QJEvisvVCElTUYFRZg.EsOFt3dYrfqFKlcGVrLBDdgPnxfXJDoBxq', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(11, 'Opik', 'opik@gmail.com', NULL, '$2y$12$xPhfJh2sjtu0YudzFmoSouifCv2CEZk90QxIUppDjN2nzvmTtVZMy', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(12, 'Ida Farida, S. Pd', 'ida.farida@gmail.com', NULL, '$2y$12$dNDUZyuk21xmWeg7sXfAc.e3/V1DjNxCcbSiwUn33Sx.Gai8nR7H6', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(13, 'Idah Mahmudah, S.Pd', 'idah.mahmudah@gmail.com', NULL, '$2y$12$xdcOLAh6TAyQ9aIZ4zyufeo7OpPfvFIfxjDzrMCk/95s08IiJccFm', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(14, 'Drs. Muhamad Ali', 'muhamad.ali@gmail.com', NULL, '$2y$12$Dldztqj8E2QnsM/JfhOqb.4W.7sZ/1DM.IYIy3ZO9.OFLs8LFWjM6', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(15, 'Helmi Ali Akbar, S.Pd.I', 'helmi.ali.akbar@gmail.com', NULL, '$2y$12$NkyF66JdeLUTp7C4RRGhre4UPDLyNsi82KzWJfA9/TgSPQJL0LXkK', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(16, 'Syeni Suswarini, S.Pd', 'syeni.suswarini@gmail.com', NULL, '$2y$12$kOqZX99ZV/PdKFObFc8TQe1L.miGFK/yYZiof8rUmWN7LrdPNbxSW', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(17, 'Wiwid Yulianti, S.Pd', 'wiwid.yulianti@gmail.com', NULL, '$2y$12$NHbMNqE3tRw0HxFkg/yTfutSVkzD4ohc6ePAvizcEUrrvuW8uFEZi', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(18, 'Ansor Muhajir, S.T', 'ansor.muhajir@gmail.com', NULL, '$2y$12$S71nJE5aW8e6NqkvP7H1Ru5BkMHcXTHW6/phPj1U6gdQQdmbMVePe', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(19, 'Ogi Gaosun Gias, S.Pd.I', 'ogi.gaosun.gias@gmail.com', NULL, '$2y$12$YLZyeY4k0rdjFxvhRpXM4OJQD.5UL1oRYSO3qKpS9DqSwiipIcFOi', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(20, 'Sandi Mulyana, S.Pd', 'sandi.mulyana@gmail.com', NULL, '$2y$12$MTSIoh4FTVEPN8YsXUqWrOL/NnTWA9dx/5McjPo8sGL8pd3N16veq', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(21, 'Erna Suryani, S.Pd', 'erna.suryani@gmail.com', NULL, '$2y$12$pL3x8i233CJw329BtUhvEuEB4hqxqDzIYzqsJDdJ9C99ytd3.DzwS', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(22, 'Ai Wahidah, S.Ag', 'ai.wahidah@gmail.com', NULL, '$2y$12$g2pgYA1MZuWVxfH.l1QJQu36SXZoCBoOGpFNmVrfzKkzZ8IERdxqG', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(23, 'Chandra Aditya Yusup, S.Pd', 'chandra.aditya.yusup@gmail.com', NULL, '$2y$12$nJ/XpGQGluzJncBqnE8brelgvT2esCdYLgOZ2K75YnXOVfXXMLbXe', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(24, 'Arif Kusmawan, S.Pd', 'arif.kusmawan@gmail.com', NULL, '$2y$12$FiDIRAVfWEQjN4xq6bwj.unAO7l37FJR07bcmsQPvi93ct2XAHHRi', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(25, 'Asep Taufiq Hidayat, S.Kom', 'asep.taufiq.hidayat@gmail.com', NULL, '$2y$12$Tm2eguvXH4B1/M8CDK9X5OX5LESYPizcpYs3wc5xlB2mqBAIwUZce', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(26, 'Rendy Ichsan Kusumah, S.T', 'rendy.ichsan.kusumah@gmail.com', NULL, '$2y$12$PZOy5IpGQ1LzDPcd2pGEvOCzubfC1qwXVPwu5BjsFk/16u0aa78mK', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(27, 'Feri Rahmawan, S.Pd', 'feri.rahmawan@gmail.com', NULL, '$2y$12$DJb3JLxwewrQwk1nXIZVfetXvj4URzvkhQRAwOfYmqDdH5.RwUGSq', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(28, 'Ari Sandriana, S.Kom', 'ari.sandriana@gmail.com', NULL, '$2y$12$SCRSSPhlEYjzyoTwicViYujXCYyH9EG9Y.sQA2kDwp8F/Is.ttCw.', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(29, 'Yaneu Nurdian, S.Pd, M.Pd', 'yaneu.nurdian@gmail.com', NULL, '$2y$12$h6gxgh8bfIXH63sTmz0u7O2/.GLmaFJ0c7Z3UGh5h1N6kX9koejR6', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(30, 'Ucu Muhammad Khoer N, S.Pd', 'ucu.muhammad.khoer.n@gmail.com', NULL, '$2y$12$OlV.5mXKxCSObOqOhjKg2.vDEGSbkjV/1bX0qr/b/XrB7E4cR32DC', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(31, 'Mira Dwi Rahmawati, S.Ag', 'mira.dwi.rahmawati@gmail.com', NULL, '$2y$12$a6cQdDNnDbtb1LnkBqC1juTT47ah6BxuI7j6zIsEARiRvmRZLxB8u', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(32, 'Aldi Maulana, S.Pd', 'aldi.maulana@gmail.com', NULL, '$2y$12$GeFteVwbD/hNc./N5w4fz.fXKO/DpXFEZjZ8c/YcjRbCAfuhPdrJu', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(33, 'Eli Rosliah, S.Pd', 'eli.rosliah@gmail.com', NULL, '$2y$12$TF1rMS/qz8a.yJODfmNnau3UopVYCmFBc7zUxA5AHm1Q59J1BSN9i', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(34, 'Isni Nur Fitriani, S.Pd', 'isni.nur.fitriani@gmail.com', NULL, '$2y$12$2XabMbrrM7qTHKNKKGCKCennmKXd44ZpZj6CX213Ksr0Um3wsyTHm', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(35, 'Aanisah Putri Nur Aini, S.Pd', 'aanisah.putri.nur.aini@gmail.com', NULL, '$2y$12$77mo1ChhaCsBcYSZPGRYceZvEAVTOO97j9ORDql8X3Qmny6.8thtC', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(36, 'Yusi Luthfiani, S.Sos.I', 'yusi.luthfiani@gmail.com', NULL, '$2y$12$.J9G.hvmOL2U19Ua1efg5uozVLxsQfjxJsrlUqp8LPVnZXv/DrN2S', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(37, 'Lilis Rustandy Mawardi, S.Pd., M.Pd', 'lilis.rustandy.mawardi@gmail.com', NULL, '$2y$12$4K7e/SpJov3nskWBtA4sLur8jEZFVv0wWZIchU7tS9V2CQmRnPQ4G', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(38, 'Yusrin Noer Sabrina, S.Pd', 'yusrin.noer.sabrina@gmail.com', NULL, '$2y$12$EjvNq/aKp0yD1ZVYiyzI/u/Sx9mgjySwN.wq1jhBqzSJmjE5zlWkW', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(39, 'Senny Indayani, S.Pd', 'senny.indayani@gmail.com', NULL, '$2y$12$jaVoNdgocZ6d0MecS2PZdOiQzsWAMhMAlGQeKHwdrtNKHyacdqbL6', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(40, 'Ilma Amalia, S.Pd', 'ilma.amalia@gmail.com', NULL, '$2y$12$p6/tnnzN9tNUpNoJG/CbFuWJdWfVDLn8mpGVPDpEynf3KoG6C5g.W', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(41, 'Alvira Sofwatunnisa, S.Pd', 'alvira.sofwatunnisa@gmail.com', NULL, '$2y$12$PGH7wHqyDkNbaDQZ2JZjRevUKciPiDuH58fdmAWzbkwLsJbicmKAO', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(42, 'Risna Andriyani, S.Pd', 'risna.andriyani@gmail.com', NULL, '$2y$12$/3.eBzdAlb5XKGTSuCHbpO.NKiYHuPjaAD/UGesfB.DoIBoEP9ula', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(43, 'Dra. Tintin Noerhayati', 'tintin.noerhayati@gmail.com', NULL, '$2y$12$dVBiep3MzXfYDY.hakJ8ze5oHrxWaIC1HX/hTPSWjVTyrz8cEubxy', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(44, 'Rizkia Fahira, S.Pd', 'rizkia.fahira@gmail.com', NULL, '$2y$12$lJvaD5mtzdRzqK.iL63GiORLJpk5LWh203ZImXf2eVlY2dGIbs2qm', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(45, 'Frasetio', 'frasetio@gmail.com', NULL, '$2y$12$hVgDSe3FKPhdr86OgrAUDuE.TRJhDUa1a24nQzPkM6TlBLzGrcvVK', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(46, 'Ratnaningsih Hatil Aeni, S.Pd.', 'ratnaningsih.hatil.aeni@gmail.com', NULL, '$2y$12$LS4C1n7USOFqAYe7RQ80n.0uvy4melFg8AqpL4u/JMCcQb9Zf0ZEO', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(47, 'Dheny Darajatul Hasani, S.Pd.', 'dheny.darajatul.hasani@gmail.com', NULL, '$2y$12$b/MRTiO0/kDK0twtPaocpesDd1yydZ0JQzjKhaO9Ynfo743p2Gc46', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(48, 'Fahrullah, S.Pd', 'fahrullah@gmail.com', NULL, '$2y$12$9MPWt3XdJVqCFWxf8xDCKuG9gyF2sbEljWH0KNW.zHPRM9pI9kTcy', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(49, 'Aan Andriana, S.T', 'aan.andriana@gmail.com', NULL, '$2y$12$aQkNplr3.siF5dklA7pTqOsd7t7O.JOZR1oZYNErVhiWl7my7ZpQC', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(50, 'Fitri Fauziah, S.T', 'fitri.fauziah@gmail.com', NULL, '$2y$12$MOTTUlhSm8l6BEpnHTLbCuDsW0LyqQSSye/ok.OFU76pgjPG4m71u', NULL, 'guru', '2026-09-23 03:43:52', '2026-11-05 02:34:08'),
(51, 'Nadia Zulfa', 'guru51@sekolah.sch.id', NULL, '$2y$10$i1RIByp4sFwE6pHZWDHXoOoMljvbuJnT1YLiKa/H0/aoOhydgWbmm', NULL, 'nonaktif', '2026-11-04 03:53:59', '2026-11-05 02:31:56'),
(52, 'Wawan Kurniawan, S.Pd.I', 'wawan.kurniawan@gmail.com', NULL, '$2y$12$iilFg26Os5Z0tUCJ6yjZCOmM6.hwdA7sZ2dE3hSheP7CtzbbceXq2', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08'),
(53, 'Nida Aulia, S.Pd', 'nida.aulia@gmail.com', NULL, '$2y$12$NVvEZ6eSaZBY0NgTwG1LU.UNHVQl7UHLoD2CzGUPtGOfT5pPQyCXG', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08'),
(54, 'Sri Nurrizki Putri Krisnadi, S.Pd', 'sri.nurrizki.putri.krisnadi@gmail.com', NULL, '$2y$12$V99E7fJtkIf44UDuH8j2guhFhh/krO2pEdldyBwbTCZDUOUrxblx2', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08'),
(55, 'Riesy Putri Navia, S.Kom', 'riesy.putri.navia@gmail.com', NULL, '$2y$12$AdfEaB/dMwKjSXXQK9NxSOI1Xtk3e2VQdhPw2Rs8TIG6A.IiSuqiO', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08'),
(56, 'Rurry Febriani Maryadi, S.Pd', 'rurry.febriani.maryadi@gmail.com', NULL, '$2y$12$rVOxcpXw3pAWVgtlDPxSWuJvaWFzFHo8BsZh.n6JyVKqSv0Piu.BG', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08'),
(57, 'Irvan Rizal, S.Pd', 'irvan.rizal@gmail.com', NULL, '$2y$12$yuxu6BnARejFcwVUce/nQepwL3RClSRQ6vcvQykJpA.rlFeBEYUsa', NULL, 'guru', '2026-11-05 02:34:08', '2026-11-05 02:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `t_wali_kelas`
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
-- Dumping data for table `t_wali_kelas`
--

INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, 50, 1, 18, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(2, 50, 16, 38, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(3, 50, 21, 32, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(4, 50, 13, 27, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(5, 50, 27, 2, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(6, 50, 2, 39, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(7, 50, 63, 20, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(8, 50, 64, 46, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(9, 50, 39, 42, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(10, 50, 6, 28, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(11, 50, 9, 36, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(12, 50, 11, 55, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(13, 50, 42, 47, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(14, 50, 25, 35, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(15, 50, 24, 56, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(16, 50, 3, 29, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(17, 50, 59, 41, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(18, 50, 22, 48, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(19, 50, 8, 16, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(20, 50, 37, 34, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(21, 50, 7, 21, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(22, 50, 5, 23, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(23, 50, 17, 26, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(24, 50, 29, 11, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(25, 50, 4, 15, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(26, 50, 60, 33, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(27, 50, 12, 40, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(28, 50, 40, 37, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(29, 50, 15, 50, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(30, 50, 20, 49, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(31, 50, 30, 53, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(32, 50, 45, 54, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(33, 50, 15, 22, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(34, 50, 44, 30, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(35, 50, 19, 56, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(36, 50, 34, 44, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(37, 50, 14, 31, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(38, 50, 49, 24, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran_siswa`
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
-- Indexes for table `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_pelanggaran_siswa`
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
