-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 07:27 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
(1, '197010101001', 'Guru 1', 'guru1@sekolah.sch.id', 1, 1, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(2, '197020101002', 'Guru 2', 'guru2@sekolah.sch.id', 1, 2, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(3, '197030101003', 'Guru 3', 'guru3@sekolah.sch.id', 1, 3, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(4, '197040101004', 'Guru 4', 'guru4@sekolah.sch.id', 1, 4, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(5, '197050101005', 'Guru 5', 'guru5@sekolah.sch.id', 1, 5, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(6, '197060101006', 'Guru 6', 'guru6@sekolah.sch.id', 1, 6, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(7, '197070101007', 'Guru 7', 'guru7@sekolah.sch.id', 1, 7, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(8, '197080101008', 'Guru 8', 'guru8@sekolah.sch.id', 1, 8, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(9, '197090101009', 'Guru 9', 'guru9@sekolah.sch.id', 1, 9, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(10, '197100101010', 'Guru 10', 'guru10@sekolah.sch.id', 1, 10, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(11, '197110101011', 'Guru 11', 'guru11@sekolah.sch.id', 1, 11, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(12, '197120101012', 'Guru 12', 'guru12@sekolah.sch.id', 1, 12, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(13, '197130101013', 'Guru 13', 'guru13@sekolah.sch.id', 1, 13, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(14, '197140101014', 'Guru 14', 'guru14@sekolah.sch.id', 1, 14, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(15, '197150101015', 'Guru 15', 'guru15@sekolah.sch.id', 1, 15, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(16, '197160101016', 'Guru 16', 'guru16@sekolah.sch.id', 1, 16, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(17, '197170101017', 'Guru 17', 'guru17@sekolah.sch.id', 1, 17, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(18, '197180101018', 'Guru 18', 'guru18@sekolah.sch.id', 1, 18, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(19, '197190101019', 'Guru 19', 'guru19@sekolah.sch.id', 1, 19, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(20, '197200101020', 'Guru 20', 'guru20@sekolah.sch.id', 1, 20, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(21, '197210101021', 'Guru 21', 'guru21@sekolah.sch.id', 1, 21, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(22, '197220101022', 'Guru 22', 'guru22@sekolah.sch.id', 1, 22, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(23, '197230101023', 'Guru 23', 'guru23@sekolah.sch.id', 1, 23, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(24, '197240101024', 'Guru 24', 'guru24@sekolah.sch.id', 1, 24, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(25, '197250101025', 'Guru 25', 'guru25@sekolah.sch.id', 1, 25, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(26, '197260101026', 'Guru 26', 'guru26@sekolah.sch.id', 1, 26, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(27, '197270101027', 'Guru 27', 'guru27@sekolah.sch.id', 1, 27, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(28, '197280101028', 'Guru 28', 'guru28@sekolah.sch.id', 1, 28, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(29, '197290101029', 'Guru 29', 'guru29@sekolah.sch.id', 1, 29, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(30, '197300101030', 'Guru 30', 'guru30@sekolah.sch.id', 1, 30, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(31, '197310101031', 'Guru 31', 'guru31@sekolah.sch.id', 1, 31, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(32, '197320101032', 'Guru 32', 'guru32@sekolah.sch.id', 1, 32, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(33, '197330101033', 'Guru 33', 'guru33@sekolah.sch.id', 1, 33, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(34, '197340101034', 'Guru 34', 'guru34@sekolah.sch.id', 1, 34, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(35, '197350101035', 'Guru 35', 'guru35@sekolah.sch.id', 1, 35, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(36, '197360101036', 'Guru 36', 'guru36@sekolah.sch.id', 1, 36, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(37, '197370101037', 'Guru 37', 'guru37@sekolah.sch.id', 1, 37, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(38, '197380101038', 'Guru 38', 'guru38@sekolah.sch.id', 1, 38, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(39, '197390101039', 'Guru 39', 'guru39@sekolah.sch.id', 1, 39, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(40, '197400101040', 'Guru 40', 'guru40@sekolah.sch.id', 1, 40, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(41, '197410101041', 'Guru 41', 'guru41@sekolah.sch.id', 1, 41, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(42, '197420101042', 'Guru 42', 'guru42@sekolah.sch.id', 1, 42, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(43, '197430101043', 'Guru 43', 'guru43@sekolah.sch.id', 1, 43, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(44, '197440101044', 'Guru 44', 'guru44@sekolah.sch.id', 1, 44, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(45, '197450101045', 'Guru 45', 'guru45@sekolah.sch.id', 1, 45, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(46, '197460101046', 'Guru 46', 'guru46@sekolah.sch.id', 1, 46, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(47, '197470101047', 'Guru 47', 'guru47@sekolah.sch.id', 1, 47, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(48, '197480101048', 'Guru 48', 'guru48@sekolah.sch.id', 1, 48, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(49, '197490101049', 'Guru 49', 'guru49@sekolah.sch.id', 1, 49, '2026-09-23 03:44:07', '2026-09-23 03:44:07'),
(50, '197500101050', 'Guru 50', 'guru50@sekolah.sch.id', 1, 50, '2026-09-23 03:44:07', '2026-09-23 03:44:07');

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
(1, 'X-1', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(2, 'XI-1', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(3, 'XII-1', 'XII', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(4, 'X-2', 'X', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(5, 'XI-2', 'XI', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(6, 'XII-2', 'XII', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(7, 'X-3', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(8, 'XI-3', 'XI', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(9, 'XII-3', 'XII', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(10, 'X-4', 'X', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(11, 'XI-4', 'XI', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(12, 'XII-4', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(13, 'X-5', 'X', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(14, 'XI-5', 'XI', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(15, 'XII-5', 'XII', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(16, 'X-6', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(17, 'XI-6', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(18, 'XII-6', 'XII', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(19, 'X-7', 'X', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(20, 'XI-7', 'XI', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(21, 'XII-7', 'XII', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(22, 'X-8', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(23, 'XI-8', 'XI', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(24, 'XII-8', 'XII', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(25, 'X-9', 'X', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(26, 'XI-9', 'XI', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(27, 'XII-9', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(28, 'X-10', 'X', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(29, 'XI-10', 'XI', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(30, 'XII-10', 'XII', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(31, 'X-11', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(32, 'XI-11', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(33, 'XII-11', 'XII', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(34, 'X-12', 'X', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(35, 'XI-12', 'XI', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(36, 'XII-12', 'XII', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(37, 'X-13', 'X', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(38, 'XI-13', 'XI', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(39, 'XII-13', 'XII', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(40, 'X-14', 'X', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(41, 'XI-14', 'XI', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(42, 'XII-14', 'XII', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(43, 'X-15', 'X', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(44, 'XI-15', 'XI', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(45, 'XII-15', 'XII', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(46, 'X-16', 'X', 'RPL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(47, 'XI-16', 'XI', 'TKJ', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(48, 'XII-16', 'XII', 'DKV', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(49, 'X-17', 'X', 'AKL', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41'),
(50, 'XI-17', 'XI', 'MPLB', 1, '2026-09-23 03:47:41', '2026-09-23 03:47:41');

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
(1, 1, 50, 1, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(2, 2, 50, 2, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(3, 3, 50, 3, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(4, 4, 50, 4, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(5, 5, 50, 5, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(6, 6, 50, 6, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(7, 7, 50, 7, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(8, 8, 50, 8, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(9, 9, 50, 9, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(10, 10, 50, 10, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(11, 11, 50, 11, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(12, 12, 50, 12, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(13, 13, 50, 13, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(14, 14, 50, 14, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(15, 15, 50, 15, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(16, 16, 50, 16, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(17, 17, 50, 17, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(18, 18, 50, 18, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(19, 19, 50, 19, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(20, 20, 50, 20, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(21, 21, 50, 21, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(22, 22, 50, 22, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(23, 23, 50, 23, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(24, 24, 50, 24, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(25, 25, 50, 25, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(26, 26, 50, 26, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(27, 27, 50, 27, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(28, 28, 50, 28, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(29, 29, 50, 29, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(30, 30, 50, 30, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(31, 31, 50, 31, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(32, 32, 50, 32, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(33, 33, 50, 33, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(34, 34, 50, 34, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(35, 35, 50, 35, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(36, 36, 50, 36, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(37, 37, 50, 37, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(38, 38, 50, 38, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(39, 39, 50, 39, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(40, 40, 50, 40, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(41, 41, 50, 41, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(42, 42, 50, 42, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(43, 43, 50, 43, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(44, 44, 50, 44, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(45, 45, 50, 45, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(46, 46, 50, 46, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(47, 47, 50, 47, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(48, 48, 50, 48, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(49, 49, 50, 49, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50'),
(50, 50, 50, 50, '2025-07-01', '0000-00-00', 1, '2026-09-23 03:48:50', '2026-09-23 03:48:50');

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
  `update_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `update_at`) VALUES
(1, 1, 'PLG-001', 'Datang terlambat 1', 5, 'Deskripsi pelanggaran 1.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(2, 2, 'PLG-002', 'Tidak memakai atribut lengkap 2', 10, 'Deskripsi pelanggaran 2.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(3, 3, 'PLG-003', 'Tidak memakai seragam sesuai ketentuan 3', 15, 'Deskripsi pelanggaran 3.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(4, 4, 'PLG-004', 'Rambut tidak sesuai ketentuan 4', 20, 'Deskripsi pelanggaran 4.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(5, 5, 'PLG-005', 'Meninggalkan kelas tanpa izin 5', 25, 'Deskripsi pelanggaran 5.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(6, 6, 'PLG-006', 'Tidak mengerjakan tugas 6', 30, 'Deskripsi pelanggaran 6.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(7, 7, 'PLG-007', 'Berbicara saat pembelajaran 7', 35, 'Deskripsi pelanggaran 7.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(8, 8, 'PLG-008', 'Membuang sampah sembarangan 8', 40, 'Deskripsi pelanggaran 8.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(9, 9, 'PLG-009', 'Tidak mengikuti upacara 9', 45, 'Deskripsi pelanggaran 9.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(10, 10, 'PLG-010', 'Tidak membawa perlengkapan belajar 10', 50, 'Deskripsi pelanggaran 10.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(11, 11, 'PLG-011', 'Datang terlambat 11', 5, 'Deskripsi pelanggaran 11.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(12, 12, 'PLG-012', 'Tidak memakai atribut lengkap 12', 10, 'Deskripsi pelanggaran 12.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(13, 13, 'PLG-013', 'Tidak memakai seragam sesuai ketentuan 13', 15, 'Deskripsi pelanggaran 13.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(14, 14, 'PLG-014', 'Rambut tidak sesuai ketentuan 14', 20, 'Deskripsi pelanggaran 14.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(15, 15, 'PLG-015', 'Meninggalkan kelas tanpa izin 15', 25, 'Deskripsi pelanggaran 15.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(16, 16, 'PLG-016', 'Tidak mengerjakan tugas 16', 30, 'Deskripsi pelanggaran 16.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(17, 17, 'PLG-017', 'Berbicara saat pembelajaran 17', 35, 'Deskripsi pelanggaran 17.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(18, 18, 'PLG-018', 'Membuang sampah sembarangan 18', 40, 'Deskripsi pelanggaran 18.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(19, 19, 'PLG-019', 'Tidak mengikuti upacara 19', 45, 'Deskripsi pelanggaran 19.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(20, 20, 'PLG-020', 'Tidak membawa perlengkapan belajar 20', 50, 'Deskripsi pelanggaran 20.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(21, 21, 'PLG-021', 'Datang terlambat 21', 5, 'Deskripsi pelanggaran 21.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(22, 22, 'PLG-022', 'Tidak memakai atribut lengkap 22', 10, 'Deskripsi pelanggaran 22.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(23, 23, 'PLG-023', 'Tidak memakai seragam sesuai ketentuan 23', 15, 'Deskripsi pelanggaran 23.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(24, 24, 'PLG-024', 'Rambut tidak sesuai ketentuan 24', 20, 'Deskripsi pelanggaran 24.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(25, 25, 'PLG-025', 'Meninggalkan kelas tanpa izin 25', 25, 'Deskripsi pelanggaran 25.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(26, 26, 'PLG-026', 'Tidak mengerjakan tugas 26', 30, 'Deskripsi pelanggaran 26.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(27, 27, 'PLG-027', 'Berbicara saat pembelajaran 27', 35, 'Deskripsi pelanggaran 27.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(28, 28, 'PLG-028', 'Membuang sampah sembarangan 28', 40, 'Deskripsi pelanggaran 28.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(29, 29, 'PLG-029', 'Tidak mengikuti upacara 29', 45, 'Deskripsi pelanggaran 29.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(30, 30, 'PLG-030', 'Tidak membawa perlengkapan belajar 30', 50, 'Deskripsi pelanggaran 30.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(31, 31, 'PLG-031', 'Datang terlambat 31', 5, 'Deskripsi pelanggaran 31.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(32, 32, 'PLG-032', 'Tidak memakai atribut lengkap 32', 10, 'Deskripsi pelanggaran 32.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(33, 33, 'PLG-033', 'Tidak memakai seragam sesuai ketentuan 33', 15, 'Deskripsi pelanggaran 33.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(34, 34, 'PLG-034', 'Rambut tidak sesuai ketentuan 34', 20, 'Deskripsi pelanggaran 34.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(35, 35, 'PLG-035', 'Meninggalkan kelas tanpa izin 35', 25, 'Deskripsi pelanggaran 35.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(36, 36, 'PLG-036', 'Tidak mengerjakan tugas 36', 30, 'Deskripsi pelanggaran 36.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(37, 37, 'PLG-037', 'Berbicara saat pembelajaran 37', 35, 'Deskripsi pelanggaran 37.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(38, 38, 'PLG-038', 'Membuang sampah sembarangan 38', 40, 'Deskripsi pelanggaran 38.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(39, 39, 'PLG-039', 'Tidak mengikuti upacara 39', 45, 'Deskripsi pelanggaran 39.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(40, 40, 'PLG-040', 'Tidak membawa perlengkapan belajar 40', 50, 'Deskripsi pelanggaran 40.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(41, 41, 'PLG-041', 'Datang terlambat 41', 5, 'Deskripsi pelanggaran 41.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(42, 42, 'PLG-042', 'Tidak memakai atribut lengkap 42', 10, 'Deskripsi pelanggaran 42.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(43, 43, 'PLG-043', 'Tidak memakai seragam sesuai ketentuan 43', 15, 'Deskripsi pelanggaran 43.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(44, 44, 'PLG-044', 'Rambut tidak sesuai ketentuan 44', 20, 'Deskripsi pelanggaran 44.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(45, 45, 'PLG-045', 'Meninggalkan kelas tanpa izin 45', 25, 'Deskripsi pelanggaran 45.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(46, 46, 'PLG-046', 'Tidak mengerjakan tugas 46', 30, 'Deskripsi pelanggaran 46.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(47, 47, 'PLG-047', 'Berbicara saat pembelajaran 47', 35, 'Deskripsi pelanggaran 47.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(48, 48, 'PLG-048', 'Membuang sampah sembarangan 48', 40, 'Deskripsi pelanggaran 48.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(49, 49, 'PLG-049', 'Tidak mengikuti upacara 49', 45, 'Deskripsi pelanggaran 49.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22'),
(50, 50, 'PLG-050', 'Tidak membawa perlengkapan belajar 50', 50, 'Deskripsi pelanggaran 50.', 1, '2026-09-23 03:54:22', '2026-09-23 03:54:22');

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
(1, 'Kedisiplinan 1', 'Kategori pelanggaran kedisiplinan nomor 1.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(2, 'Kehadiran 2', 'Kategori pelanggaran kehadiran nomor 2.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(3, 'Seragam 3', 'Kategori pelanggaran seragam nomor 3.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(4, 'Ketertiban 4', 'Kategori pelanggaran ketertiban nomor 4.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(5, 'Kebersihan 5', 'Kategori pelanggaran kebersihan nomor 5.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(6, 'Kedisiplinan 6', 'Kategori pelanggaran kedisiplinan nomor 6.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(7, 'Kehadiran 7', 'Kategori pelanggaran kehadiran nomor 7.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(8, 'Seragam 8', 'Kategori pelanggaran seragam nomor 8.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(9, 'Ketertiban 9', 'Kategori pelanggaran ketertiban nomor 9.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(10, 'Kebersihan 10', 'Kategori pelanggaran kebersihan nomor 10.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(11, 'Kedisiplinan 11', 'Kategori pelanggaran kedisiplinan nomor 11.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(12, 'Kehadiran 12', 'Kategori pelanggaran kehadiran nomor 12.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(13, 'Seragam 13', 'Kategori pelanggaran seragam nomor 13.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(14, 'Ketertiban 14', 'Kategori pelanggaran ketertiban nomor 14.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(15, 'Kebersihan 15', 'Kategori pelanggaran kebersihan nomor 15.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(16, 'Kedisiplinan 16', 'Kategori pelanggaran kedisiplinan nomor 16.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(17, 'Kehadiran 17', 'Kategori pelanggaran kehadiran nomor 17.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(18, 'Seragam 18', 'Kategori pelanggaran seragam nomor 18.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(19, 'Ketertiban 19', 'Kategori pelanggaran ketertiban nomor 19.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(20, 'Kebersihan 20', 'Kategori pelanggaran kebersihan nomor 20.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(21, 'Kedisiplinan 21', 'Kategori pelanggaran kedisiplinan nomor 21.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(22, 'Kehadiran 22', 'Kategori pelanggaran kehadiran nomor 22.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(23, 'Seragam 23', 'Kategori pelanggaran seragam nomor 23.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(24, 'Ketertiban 24', 'Kategori pelanggaran ketertiban nomor 24.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(25, 'Kebersihan 25', 'Kategori pelanggaran kebersihan nomor 25.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(26, 'Kedisiplinan 26', 'Kategori pelanggaran kedisiplinan nomor 26.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(27, 'Kehadiran 27', 'Kategori pelanggaran kehadiran nomor 27.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(28, 'Seragam 28', 'Kategori pelanggaran seragam nomor 28.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(29, 'Ketertiban 29', 'Kategori pelanggaran ketertiban nomor 29.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(30, 'Kebersihan 30', 'Kategori pelanggaran kebersihan nomor 30.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(31, 'Kedisiplinan 31', 'Kategori pelanggaran kedisiplinan nomor 31.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(32, 'Kehadiran 32', 'Kategori pelanggaran kehadiran nomor 32.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(33, 'Seragam 33', 'Kategori pelanggaran seragam nomor 33.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(34, 'Ketertiban 34', 'Kategori pelanggaran ketertiban nomor 34.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(35, 'Kebersihan 35', 'Kategori pelanggaran kebersihan nomor 35.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(36, 'Kedisiplinan 36', 'Kategori pelanggaran kedisiplinan nomor 36.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(37, 'Kehadiran 37', 'Kategori pelanggaran kehadiran nomor 37.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(38, 'Seragam 38', 'Kategori pelanggaran seragam nomor 38.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(39, 'Ketertiban 39', 'Kategori pelanggaran ketertiban nomor 39.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(40, 'Kebersihan 40', 'Kategori pelanggaran kebersihan nomor 40.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(41, 'Kedisiplinan 41', 'Kategori pelanggaran kedisiplinan nomor 41.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(42, 'Kehadiran 42', 'Kategori pelanggaran kehadiran nomor 42.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(43, 'Seragam 43', 'Kategori pelanggaran seragam nomor 43.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(44, 'Ketertiban 44', 'Kategori pelanggaran ketertiban nomor 44.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(45, 'Kebersihan 45', 'Kategori pelanggaran kebersihan nomor 45.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(46, 'Kedisiplinan 46', 'Kategori pelanggaran kedisiplinan nomor 46.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(47, 'Kehadiran 47', 'Kategori pelanggaran kehadiran nomor 47.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(48, 'Seragam 48', 'Kategori pelanggaran seragam nomor 48.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(49, 'Ketertiban 49', 'Kategori pelanggaran ketertiban nomor 49.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54'),
(50, 'Kebersihan 50', 'Kategori pelanggaran kebersihan nomor 50.', 1, '2026-09-23 03:53:54', '2026-09-23 03:53:54');

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
(1, 50, 1, 'Adit Pratama', 1, 'Datang terlambat 1', 1, 1, 'Guru 1', '2025-01-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 1, 'X-1'),
(2, 50, 2, 'Aulia Rahma', 2, 'Tidak memakai atribut lengkap 2', 2, 2, 'Guru 2', '2025-02-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 2, 'XI-1'),
(3, 50, 3, 'Bagas Saputra', 3, 'Tidak memakai seragam sesuai ketentuan 3', 3, 3, 'Guru 3', '2025-03-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 3, 'XII-1'),
(4, 50, 4, 'Citra Lestari', 4, 'Rambut tidak sesuai ketentuan 4', 4, 4, 'Guru 4', '2025-04-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 4, 'X-2'),
(5, 50, 5, 'Daffa Ramadhan', 5, 'Meninggalkan kelas tanpa izin 5', 5, 5, 'Guru 5', '2025-05-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 5, 'XI-2'),
(6, 50, 6, 'Elsa Putri', 6, 'Tidak mengerjakan tugas 6', 6, 6, 'Guru 6', '2025-06-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 6, 'XII-2'),
(7, 50, 7, 'Fajar Nugraha', 7, 'Berbicara saat pembelajaran 7', 7, 7, 'Guru 7', '2025-07-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 7, 'X-3'),
(8, 50, 8, 'Gina Maharani', 8, 'Membuang sampah sembarangan 8', 8, 8, 'Guru 8', '2025-08-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 8, 'XI-3'),
(9, 50, 9, 'Hafiz Akbar', 9, 'Tidak mengikuti upacara 9', 9, 9, 'Guru 9', '2025-09-09', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 9, 'XII-3'),
(10, 50, 10, 'Intan Permata', 10, 'Tidak membawa perlengkapan belajar 10', 10, 10, 'Guru 10', '2025-10-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 10, 'X-4'),
(11, 50, 11, 'Jovan Setiawan', 11, 'Datang terlambat 11', 11, 11, 'Guru 11', '2025-11-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 11, 'XI-4'),
(12, 50, 12, 'Kania Salsabila', 12, 'Tidak memakai atribut lengkap 12', 12, 12, 'Guru 12', '2025-12-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 12, 'XII-4'),
(13, 50, 13, 'Lutfi Hakim', 13, 'Tidak memakai seragam sesuai ketentuan 13', 13, 13, 'Guru 13', '2025-01-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 13, 'X-5'),
(14, 50, 14, 'Maya Anggraini', 14, 'Rambut tidak sesuai ketentuan 14', 14, 14, 'Guru 14', '2025-02-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 14, 'XI-5'),
(15, 50, 15, 'Naufal Rizky', 15, 'Meninggalkan kelas tanpa izin 15', 15, 15, 'Guru 15', '2025-03-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 15, 'XII-5'),
(16, 50, 16, 'Olivia Amelia', 16, 'Tidak mengerjakan tugas 16', 16, 16, 'Guru 16', '2025-04-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 16, 'X-6'),
(17, 50, 17, 'Putra Wijaya', 17, 'Berbicara saat pembelajaran 17', 17, 17, 'Guru 17', '2025-05-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 17, 'XI-6'),
(18, 50, 18, 'Qonita Zahra', 18, 'Membuang sampah sembarangan 18', 18, 18, 'Guru 18', '2025-06-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 18, 'XII-6'),
(19, 50, 19, 'Raka Firmansyah', 19, 'Tidak mengikuti upacara 19', 19, 19, 'Guru 19', '2025-07-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 19, 'X-7'),
(20, 50, 20, 'Salsa Nabila', 20, 'Tidak membawa perlengkapan belajar 20', 20, 20, 'Guru 20', '2025-08-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 20, 'XI-7'),
(21, 50, 21, 'Tegar Maulana', 21, 'Datang terlambat 21', 21, 21, 'Guru 21', '2025-09-21', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 21, 'XII-7'),
(22, 50, 22, 'Ulfa Nuraini', 22, 'Tidak memakai atribut lengkap 22', 22, 22, 'Guru 22', '2025-10-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 22, 'X-8'),
(23, 50, 23, 'Vino Alfarizi', 23, 'Tidak memakai seragam sesuai ketentuan 23', 23, 23, 'Guru 23', '2025-11-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 23, 'XI-8'),
(24, 50, 24, 'Wulan Sari', 24, 'Rambut tidak sesuai ketentuan 24', 24, 24, 'Guru 24', '2025-12-24', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 24, 'XII-8'),
(25, 50, 25, 'Yoga Pratama', 25, 'Meninggalkan kelas tanpa izin 25', 25, 25, 'Guru 25', '2025-01-25', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 25, 'X-9'),
(26, 50, 26, 'Zahra Aulia', 26, 'Tidak mengerjakan tugas 26', 26, 26, 'Guru 26', '2025-02-26', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 26, 'XI-9'),
(27, 50, 27, 'Andika Kurnia', 27, 'Berbicara saat pembelajaran 27', 27, 27, 'Guru 27', '2025-03-27', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 27, 'XII-9'),
(28, 50, 28, 'Bella Safitri', 28, 'Membuang sampah sembarangan 28', 28, 28, 'Guru 28', '2025-04-01', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 28, 'X-10'),
(29, 50, 29, 'Cahyo Aditya', 29, 'Tidak mengikuti upacara 29', 29, 29, 'Guru 29', '2025-05-02', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 29, 'XI-10'),
(30, 50, 30, 'Dinda Maharani', 30, 'Tidak membawa perlengkapan belajar 30', 30, 30, 'Guru 30', '2025-06-03', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 30, 'XII-10'),
(31, 50, 31, 'Eko Saputra', 31, 'Datang terlambat 31', 31, 31, 'Guru 31', '2025-07-04', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 31, 'X-11'),
(32, 50, 32, 'Fitri Handayani', 32, 'Tidak memakai atribut lengkap 32', 32, 32, 'Guru 32', '2025-08-05', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 32, 'XI-11'),
(33, 50, 33, 'Galih Ramadhan', 33, 'Tidak memakai seragam sesuai ketentuan 33', 33, 33, 'Guru 33', '2025-09-06', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 33, 'XII-11'),
(34, 50, 34, 'Hana Aprilia', 34, 'Rambut tidak sesuai ketentuan 34', 34, 34, 'Guru 34', '2025-10-07', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 34, 'X-12'),
(35, 50, 35, 'Ilham Fauzan', 35, 'Meninggalkan kelas tanpa izin 35', 35, 35, 'Guru 35', '2025-11-08', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 35, 'XI-12'),
(36, 50, 36, 'Jihan Lestari', 36, 'Tidak mengerjakan tugas 36', 36, 36, 'Guru 36', '2025-12-09', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 36, 'XII-12'),
(37, 50, 37, 'Kevin Setiawan', 37, 'Berbicara saat pembelajaran 37', 37, 37, 'Guru 37', '2025-01-10', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 37, 'X-13'),
(38, 50, 38, 'Larasati Putri', 38, 'Membuang sampah sembarangan 38', 38, 38, 'Guru 38', '2025-02-11', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 38, 'XI-13'),
(39, 50, 39, 'Miko Pratama', 39, 'Tidak mengikuti upacara 39', 39, 39, 'Guru 39', '2025-03-12', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 39, 'XII-13'),
(40, 50, 40, 'Nadia Safira', 40, 'Tidak membawa perlengkapan belajar 40', 40, 40, 'Guru 40', '2025-04-13', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 40, 'X-14'),
(41, 50, 41, 'Rian Hidayat', 41, 'Datang terlambat 41', 41, 41, 'Guru 41', '2025-05-14', 'Pelanggaran tercatat pada kegiatan sekolah.', 5, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 41, 'XI-14'),
(42, 50, 42, 'Siti Aisyah', 42, 'Tidak memakai atribut lengkap 42', 42, 42, 'Guru 42', '2025-06-15', 'Pelanggaran tercatat pada kegiatan sekolah.', 10, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 42, 'XII-14'),
(43, 50, 43, 'Taufik Haryanto', 43, 'Tidak memakai seragam sesuai ketentuan 43', 43, 43, 'Guru 43', '2025-07-16', 'Pelanggaran tercatat pada kegiatan sekolah.', 15, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 43, 'X-15'),
(44, 50, 44, 'Vania Putri', 44, 'Rambut tidak sesuai ketentuan 44', 44, 44, 'Guru 44', '2025-08-17', 'Pelanggaran tercatat pada kegiatan sekolah.', 20, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 44, 'XI-15'),
(45, 50, 45, 'Wahyu Nugroho', 45, 'Meninggalkan kelas tanpa izin 45', 45, 45, 'Guru 45', '2025-09-18', 'Pelanggaran tercatat pada kegiatan sekolah.', 25, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 45, 'XII-15'),
(46, 50, 46, 'Yuni Kartika', 46, 'Tidak mengerjakan tugas 46', 46, 46, 'Guru 46', '2025-10-19', 'Pelanggaran tercatat pada kegiatan sekolah.', 30, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 46, 'X-16'),
(47, 50, 47, 'Zaki Firmansyah', 47, 'Berbicara saat pembelajaran 47', 47, 47, 'Guru 47', '2025-11-20', 'Pelanggaran tercatat pada kegiatan sekolah.', 35, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 47, 'XI-16'),
(48, 50, 48, 'Ari Maulana', 48, 'Membuang sampah sembarangan 48', 48, 48, 'Guru 48', '2025-12-21', 'Pelanggaran tercatat pada kegiatan sekolah.', 40, 'Pembinaan', 'Ditindaklanjuti', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 48, 'XII-16'),
(49, 50, 49, 'Bima Setiawan', 49, 'Tidak mengikuti upacara 49', 49, 49, 'Guru 49', '2025-01-22', 'Pelanggaran tercatat pada kegiatan sekolah.', 45, 'Teguran lisan', 'Diproses', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 49, 'X-17'),
(50, 50, 50, 'Cindy Oktavia', 50, 'Tidak membawa perlengkapan belajar 50', 50, 50, 'Guru 50', '2025-02-23', 'Pelanggaran tercatat pada kegiatan sekolah.', 50, 'Peringatan', 'Selesai', '2026-09-23 03:54:57', '2026-09-23 03:54:57', 50, 'XI-17');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
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
(50, '20260050', '0080000050', 'Cindy Oktavia', 'P', '2008-05-23', 'Jl. Pendidikan No. 50, Tasikmalaya', 1, '2026-09-23 03:48:22', '2026-09-23 03:48:22');

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
(1, '1976/1977', '1976-07-01', '1977-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(2, '1977/1978', '1977-07-01', '1978-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(3, '1978/1979', '1978-07-01', '1979-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(4, '1979/1980', '1979-07-01', '1980-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(5, '1980/1981', '1980-07-01', '1981-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(6, '1981/1982', '1981-07-01', '1982-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(7, '1982/1983', '1982-07-01', '1983-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(8, '1983/1984', '1983-07-01', '1984-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(9, '1984/1985', '1984-07-01', '1985-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(10, '1985/1986', '1985-07-01', '1986-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(11, '1986/1987', '1986-07-01', '1987-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(12, '1987/1988', '1987-07-01', '1988-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(13, '1988/1989', '1988-07-01', '1989-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(14, '1989/1990', '1989-07-01', '1990-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(15, '1990/1991', '1990-07-01', '1991-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(16, '1991/1992', '1991-07-01', '1992-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(17, '1992/1993', '1992-07-01', '1993-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(18, '1993/1994', '1993-07-01', '1994-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(19, '1994/1995', '1994-07-01', '1995-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(20, '1995/1996', '1995-07-01', '1996-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(21, '1996/1997', '1996-07-01', '1997-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(22, '1997/1998', '1997-07-01', '1998-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(23, '1998/1999', '1998-07-01', '1999-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(24, '1999/2000', '1999-07-01', '2000-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(25, '2000/2001', '2000-07-01', '2001-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(26, '2001/2002', '2001-07-01', '2002-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(27, '2002/2003', '2002-07-01', '2003-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(28, '2003/2004', '2003-07-01', '2004-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(29, '2004/2005', '2004-07-01', '2005-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(30, '2005/2006', '2005-07-01', '2006-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(31, '2006/2007', '2006-07-01', '2007-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(32, '2007/2008', '2007-07-01', '2008-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(33, '2008/2009', '2008-07-01', '2009-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(34, '2009/2010', '2009-07-01', '2010-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(35, '2010/2011', '2010-07-01', '2011-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(36, '2011/2012', '2011-07-01', '2012-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(37, '2012/2013', '2012-07-01', '2013-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(38, '2013/2014', '2013-07-01', '2014-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(39, '2014/2015', '2014-07-01', '2015-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(40, '2015/2016', '2015-07-01', '2016-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(41, '2016/2017', '2016-07-01', '2017-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(42, '2017/2018', '2017-07-01', '2018-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(43, '2018/2019', '2018-07-01', '2019-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(44, '2019/2020', '2019-07-01', '2020-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(45, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(46, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(47, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(48, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(49, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-09-23 03:45:34', '2026-09-23 03:45:34'),
(50, '2025/2026', '2025-07-01', '2026-06-30', 1, '2026-09-23 03:45:34', '2026-09-23 03:45:34');

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
(1, 'User 1', 'user1@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'admin', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
(2, 'User 2', 'user2@sekolah.sch.id', NULL, '$2y$12$6URGTD.jpbcfu8HOm1LnFeNzxS23FkLj9mTk.Q5vu3I8FJ6OveSRu', NULL, 'guru', '2026-09-23 03:43:52', '2026-09-23 03:43:52'),
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
(18, 50, 18, 18, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(19, 50, 19, 19, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(20, 50, 20, 20, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(21, 50, 21, 21, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(22, 50, 22, 22, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(23, 50, 23, 23, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(24, 50, 24, 24, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(25, 50, 25, 25, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(26, 50, 26, 26, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(27, 50, 27, 27, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(28, 50, 28, 28, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(29, 50, 29, 29, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(30, 50, 30, 30, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(31, 50, 31, 31, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(32, 50, 32, 32, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(33, 50, 33, 33, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(34, 50, 34, 34, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(35, 50, 35, 35, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(36, 50, 36, 36, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(37, 50, 37, 37, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(38, 50, 38, 38, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(39, 50, 39, 39, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(40, 50, 40, 40, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(41, 50, 41, 41, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(42, 50, 42, 42, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(43, 50, 43, 43, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(44, 50, 44, 44, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(45, 50, 45, 45, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(46, 50, 46, 46, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(47, 50, 47, 47, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(48, 50, 48, 48, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(49, 50, 49, 49, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40'),
(50, 50, 50, 50, '2026-07-01', NULL, 1, '2026-09-23 03:53:40', '2026-09-23 03:53:40');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
