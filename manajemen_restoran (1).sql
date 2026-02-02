-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 01, 2025 at 05:11 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manajemen_restoran`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail_pesanan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `catatan_item` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_menu`, `jumlah`, `harga`, `catatan_item`) VALUES
(1, 1, 1, 31, 10000.00, NULL),
(2, 2, 2, 20, 14000.00, NULL),
(3, 3, 3, 13, 9000.00, NULL),
(4, 4, 4, 20, 13000.00, NULL),
(5, 5, 5, 13, 12000.00, NULL),
(6, 6, 6, 8, 16000.00, NULL),
(7, 7, 1, 31, 10000.00, NULL),
(8, 8, 2, 14, 14000.00, NULL),
(9, 9, 3, 12, 9000.00, NULL),
(10, 10, 4, 18, 13000.00, NULL),
(11, 11, 5, 14, 12000.00, NULL),
(12, 12, 6, 8, 16000.00, NULL),
(13, 13, 1, 31, 10000.00, NULL),
(14, 14, 2, 14, 14000.00, NULL),
(15, 15, 3, 13, 9000.00, NULL),
(16, 16, 4, 19, 13000.00, NULL),
(17, 17, 5, 10, 12000.00, NULL),
(18, 18, 6, 9, 16000.00, NULL),
(19, 19, 1, 28, 10000.00, NULL),
(20, 20, 2, 16, 14000.00, NULL),
(21, 21, 3, 12, 9000.00, NULL),
(22, 22, 4, 16, 13000.00, NULL),
(23, 23, 5, 13, 12000.00, NULL),
(24, 24, 6, 11, 16000.00, NULL),
(25, 25, 1, 34, 10000.00, NULL),
(26, 26, 2, 15, 14000.00, NULL),
(27, 27, 3, 15, 9000.00, NULL),
(28, 28, 4, 22, 13000.00, NULL),
(29, 29, 5, 12, 12000.00, NULL),
(30, 30, 6, 10, 16000.00, NULL),
(31, 31, 1, 30, 10000.00, NULL),
(32, 32, 2, 20, 14000.00, NULL),
(33, 33, 3, 15, 9000.00, NULL),
(34, 34, 4, 21, 13000.00, NULL),
(35, 35, 5, 14, 12000.00, NULL),
(36, 36, 6, 12, 16000.00, NULL),
(37, 37, 1, 34, 10000.00, NULL),
(38, 38, 2, 19, 14000.00, NULL),
(39, 39, 3, 17, 9000.00, NULL),
(40, 40, 4, 21, 13000.00, NULL),
(41, 41, 5, 12, 12000.00, NULL),
(42, 42, 6, 9, 16000.00, NULL),
(43, 43, 1, 29, 10000.00, NULL),
(44, 44, 2, 17, 14000.00, NULL),
(45, 45, 3, 14, 9000.00, NULL),
(46, 46, 4, 21, 13000.00, NULL),
(47, 47, 5, 13, 12000.00, NULL),
(48, 48, 6, 8, 16000.00, NULL),
(49, 49, 1, 38, 10000.00, NULL),
(50, 50, 2, 16, 14000.00, NULL),
(51, 51, 3, 17, 9000.00, NULL),
(52, 52, 4, 18, 13000.00, NULL),
(53, 53, 5, 14, 12000.00, NULL),
(54, 54, 6, 11, 16000.00, NULL),
(55, 55, 1, 29, 10000.00, NULL),
(56, 56, 2, 18, 14000.00, NULL),
(57, 57, 3, 17, 9000.00, NULL),
(58, 58, 4, 19, 13000.00, NULL),
(59, 59, 5, 10, 12000.00, NULL),
(60, 60, 6, 9, 16000.00, NULL),
(61, 61, 1, 35, 10000.00, NULL),
(62, 62, 2, 19, 14000.00, NULL),
(63, 63, 3, 14, 9000.00, NULL),
(64, 64, 4, 21, 13000.00, NULL),
(65, 65, 5, 13, 12000.00, NULL),
(66, 66, 6, 9, 16000.00, NULL),
(67, 67, 1, 35, 10000.00, NULL),
(68, 68, 2, 20, 14000.00, NULL),
(69, 69, 3, 13, 9000.00, NULL),
(70, 70, 4, 16, 13000.00, NULL),
(71, 71, 5, 12, 12000.00, NULL),
(72, 72, 6, 9, 16000.00, NULL),
(73, 73, 1, 38, 10000.00, NULL),
(74, 74, 2, 20, 14000.00, NULL),
(75, 75, 3, 12, 9000.00, NULL),
(76, 76, 4, 20, 13000.00, NULL),
(77, 77, 5, 14, 12000.00, NULL),
(78, 78, 6, 9, 16000.00, NULL),
(79, 79, 1, 40, 10000.00, NULL),
(80, 80, 2, 14, 14000.00, NULL),
(81, 81, 3, 16, 9000.00, NULL),
(82, 82, 4, 19, 13000.00, NULL),
(83, 83, 5, 10, 12000.00, NULL),
(84, 84, 6, 10, 16000.00, NULL),
(85, 85, 1, 34, 10000.00, NULL),
(86, 86, 2, 18, 14000.00, NULL),
(87, 87, 3, 12, 9000.00, NULL),
(88, 88, 4, 22, 13000.00, NULL),
(89, 89, 5, 12, 12000.00, NULL),
(90, 90, 6, 12, 16000.00, NULL),
(91, 91, 1, 36, 10000.00, NULL),
(92, 92, 2, 16, 14000.00, NULL),
(93, 93, 3, 18, 9000.00, NULL),
(94, 94, 4, 20, 13000.00, NULL),
(95, 95, 5, 11, 12000.00, NULL),
(96, 96, 6, 8, 16000.00, NULL),
(97, 97, 1, 28, 10000.00, NULL),
(98, 98, 2, 16, 14000.00, NULL),
(99, 99, 3, 12, 9000.00, NULL),
(100, 100, 4, 17, 13000.00, NULL),
(101, 101, 5, 10, 12000.00, NULL),
(102, 102, 6, 8, 16000.00, NULL),
(103, 103, 1, 38, 10000.00, NULL),
(104, 104, 2, 18, 14000.00, NULL),
(105, 105, 3, 13, 9000.00, NULL),
(106, 106, 4, 16, 13000.00, NULL),
(107, 107, 5, 10, 12000.00, NULL),
(108, 108, 6, 9, 16000.00, NULL),
(109, 109, 1, 30, 10000.00, NULL),
(110, 110, 2, 15, 14000.00, NULL),
(111, 111, 3, 16, 9000.00, NULL),
(112, 112, 4, 16, 13000.00, NULL),
(113, 113, 5, 14, 12000.00, NULL),
(114, 114, 6, 11, 16000.00, NULL),
(115, 115, 1, 35, 10000.00, NULL),
(116, 116, 2, 17, 14000.00, NULL),
(117, 117, 3, 16, 9000.00, NULL),
(118, 118, 4, 21, 13000.00, NULL),
(119, 119, 5, 12, 12000.00, NULL),
(120, 120, 6, 9, 16000.00, NULL),
(121, 121, 1, 31, 10000.00, NULL),
(122, 122, 2, 19, 14000.00, NULL),
(123, 123, 3, 14, 9000.00, NULL),
(124, 124, 4, 21, 13000.00, NULL),
(125, 125, 5, 13, 12000.00, NULL),
(126, 126, 6, 10, 16000.00, NULL),
(127, 127, 1, 35, 10000.00, NULL),
(128, 128, 2, 16, 14000.00, NULL),
(129, 129, 3, 15, 9000.00, NULL),
(130, 130, 4, 19, 13000.00, NULL),
(131, 131, 5, 13, 12000.00, NULL),
(132, 132, 6, 8, 16000.00, NULL),
(133, 133, 1, 39, 10000.00, NULL),
(134, 134, 2, 19, 14000.00, NULL),
(135, 135, 3, 18, 9000.00, NULL),
(136, 136, 4, 20, 13000.00, NULL),
(137, 137, 5, 10, 12000.00, NULL),
(138, 138, 6, 8, 16000.00, NULL),
(139, 139, 1, 36, 10000.00, NULL),
(140, 140, 2, 20, 14000.00, NULL),
(141, 141, 3, 17, 9000.00, NULL),
(142, 142, 4, 21, 13000.00, NULL),
(143, 143, 5, 14, 12000.00, NULL),
(144, 144, 6, 11, 16000.00, NULL),
(145, 145, 1, 38, 10000.00, NULL),
(146, 146, 2, 15, 14000.00, NULL),
(147, 147, 3, 13, 9000.00, NULL),
(148, 148, 4, 18, 13000.00, NULL),
(149, 149, 5, 14, 12000.00, NULL),
(150, 150, 6, 9, 16000.00, NULL),
(151, 151, 1, 28, 10000.00, NULL),
(152, 152, 2, 19, 14000.00, NULL),
(153, 153, 3, 17, 9000.00, NULL),
(154, 154, 4, 19, 13000.00, NULL),
(155, 155, 5, 14, 12000.00, NULL),
(156, 156, 6, 9, 16000.00, NULL),
(157, 157, 1, 39, 10000.00, NULL),
(158, 158, 2, 14, 14000.00, NULL),
(159, 159, 3, 13, 9000.00, NULL),
(160, 160, 4, 17, 13000.00, NULL),
(161, 161, 5, 12, 12000.00, NULL),
(162, 162, 6, 12, 16000.00, NULL),
(163, 163, 1, 41, 10000.00, NULL),
(164, 164, 2, 15, 14000.00, NULL),
(165, 165, 3, 15, 9000.00, NULL),
(166, 166, 4, 22, 13000.00, NULL),
(167, 167, 5, 10, 12000.00, NULL),
(168, 168, 6, 11, 16000.00, NULL),
(169, 169, 1, 28, 10000.00, NULL),
(170, 170, 2, 20, 14000.00, NULL),
(171, 171, 3, 16, 9000.00, NULL),
(172, 172, 4, 20, 13000.00, NULL),
(173, 173, 5, 11, 12000.00, NULL),
(174, 174, 6, 8, 16000.00, NULL),
(175, 175, 1, 30, 10000.00, NULL),
(176, 176, 2, 19, 14000.00, NULL),
(177, 177, 3, 14, 9000.00, NULL),
(178, 178, 4, 18, 13000.00, NULL),
(179, 179, 5, 10, 12000.00, NULL),
(180, 180, 6, 12, 16000.00, NULL),
(181, 181, 2, 3, 14000.00, NULL),
(182, 181, 3, 2, 9000.00, NULL),
(183, 181, 12, 3, 4000.00, NULL),
(184, 182, 3, 3, 9000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori` enum('geprek','crispy','gangnam','minuman','tambahan') NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama`, `deskripsi`, `harga`, `kategori`, `gambar`, `dibuat_pada`) VALUES
(1, 'Geprek Hemat', 'Ayam geprek pedas dengan nasi, porsi hemat cocok untuk sehari-hari.', 10000.00, 'geprek', 'ayamgeprek.jpg', '2025-11-02 01:19:54'),
(2, 'Geprek Jumbo', 'Porsi besar ayam geprek dengan rasa mantap, bikin kenyang lebih lama.', 14000.00, 'geprek', 'geprekjumbo.jpg', '2025-11-02 01:21:11'),
(3, 'Crispy Hemat', 'Ayam crispy renyah dengan nasi, pas buat kantong pelajar.', 9000.00, 'crispy', 'crispyhemat.webp', '2025-11-02 01:21:35'),
(4, 'Crispy Jumbo', 'Ayam crispy ukuran jumbo, garing di luar lembut di dalam.', 13000.00, 'crispy', 'crispyjumbo.webp', '2025-11-02 01:21:59'),
(5, 'Gangnam Hemat', 'Ayam saus Korea rasa gurih manis pedas, paket hemat.', 12000.00, 'gangnam', 'gangnamhemat.webp', '2025-11-02 01:22:31'),
(6, 'Gangnam Jumbo', 'Ayam saus Korea porsi besar, cocok untuk yang doyan banget.', 16000.00, 'gangnam', 'gangnamjumbo.webp', '2025-11-02 01:23:02'),
(7, 'Gangnam Chicken Double', 'Dua potong ayam saus Korea lezat, puas buat sharing berdua.', 20000.00, 'gangnam', 'gangnamdouble.webp', '2025-11-02 01:32:59'),
(8, 'Paha Bawah', 'Potongan paha bawah ayam goreng gurih, nikmat disantap kapan saja.', 9000.00, 'tambahan', 'pahabawah.webp', '2025-11-02 01:29:34'),
(9, 'Sayap', 'Sayap ayam goreng renyah, cocok buat camilan atau tambahan lauk.', 9000.00, 'tambahan', 'sayap.webp', '2025-11-02 01:29:59'),
(10, 'Paha Atas', 'Paha atas ayam goreng empuk dengan cita rasa gurih.', 12000.00, 'tambahan', 'pahaatas.webp', '2025-11-02 01:30:27'),
(11, 'Dada', 'Daging dada ayam goreng lezat, potongan besar bikin puas.', 12000.00, 'tambahan', 'dada.webp', '2025-11-02 01:30:48'),
(12, 'Le Minerale 600ml', 'Air mineral segar untuk menemani makanmu.', 4000.00, 'minuman', 'leminerale.webp', '2025-11-02 01:31:24'),
(13, 'Teh Pucuk 350ml', 'Teh manis menyegarkan, pas banget buat makan siang.', 4000.00, 'minuman', 'tehpucuk.webp', '2025-11-02 01:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed','canceled','refunded') NOT NULL DEFAULT 'pending',
  `transaction_time` timestamp NULL DEFAULT NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `provider_order_id` varchar(100) DEFAULT NULL,
  `provider_transaction_id` varchar(100) DEFAULT NULL,
  `raw_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `provider`, `metode`, `gross_amount`, `status`, `transaction_time`, `settlement_time`, `provider_order_id`, `provider_transaction_id`, `raw_response`, `created_at`, `updated_at`) VALUES
(1, 1, 'midtrans', 'qris', 310000.00, 'paid', '2025-10-30 03:04:00', '2025-10-30 03:05:00', 'INV-20251030-0001', 'MID-000001', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"310000.00\"}', '2025-10-30 03:04:00', '2025-10-30 03:05:00'),
(2, 2, 'midtrans', 'bni_va', 280000.00, 'paid', '2025-10-30 04:04:00', '2025-10-30 04:05:00', 'INV-20251030-0002', 'MID-000002', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"280000.00\"}', '2025-10-30 04:04:00', '2025-10-30 04:05:00'),
(3, 3, 'midtrans', 'qris', 117000.00, 'paid', '2025-10-30 05:04:00', '2025-10-30 05:05:00', 'INV-20251030-0003', 'MID-000003', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"117000.00\"}', '2025-10-30 05:04:00', '2025-10-30 05:05:00'),
(4, 4, 'midtrans', 'bca_va', 260000.00, 'paid', '2025-10-30 06:04:00', '2025-10-30 06:05:00', 'INV-20251030-0004', 'MID-000004', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"260000.00\"}', '2025-10-30 06:04:00', '2025-10-30 06:05:00'),
(5, 5, 'midtrans', 'bri_va', 156000.00, 'paid', '2025-10-30 07:04:00', '2025-10-30 07:05:00', 'INV-20251030-0005', 'MID-000005', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"156000.00\"}', '2025-10-30 07:04:00', '2025-10-30 07:05:00'),
(6, 6, 'midtrans', 'bca_va', 128000.00, 'paid', '2025-10-30 08:04:00', '2025-10-30 08:05:00', 'INV-20251030-0006', 'MID-000006', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"128000.00\"}', '2025-10-30 08:04:00', '2025-10-30 08:05:00'),
(7, 7, 'midtrans', 'bri_va', 310000.00, 'paid', '2025-10-31 03:04:00', '2025-10-31 03:05:00', 'INV-20251031-0007', 'MID-000007', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"310000.00\"}', '2025-10-31 03:04:00', '2025-10-31 03:05:00'),
(8, 8, 'midtrans', 'bca_va', 196000.00, 'paid', '2025-10-31 04:04:00', '2025-10-31 04:05:00', 'INV-20251031-0008', 'MID-000008', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"196000.00\"}', '2025-10-31 04:04:00', '2025-10-31 04:05:00'),
(9, 9, 'midtrans', 'bri_va', 108000.00, 'paid', '2025-10-31 05:04:00', '2025-10-31 05:05:00', 'INV-20251031-0009', 'MID-000009', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"108000.00\"}', '2025-10-31 05:04:00', '2025-10-31 05:05:00'),
(10, 10, 'midtrans', 'gopay', 234000.00, 'paid', '2025-10-31 06:04:00', '2025-10-31 06:05:00', 'INV-20251031-0010', 'MID-000010', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"234000.00\"}', '2025-10-31 06:04:00', '2025-10-31 06:05:00'),
(11, 11, 'midtrans', 'gopay', 168000.00, 'paid', '2025-10-31 07:04:00', '2025-10-31 07:05:00', 'INV-20251031-0011', 'MID-000011', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"168000.00\"}', '2025-10-31 07:04:00', '2025-10-31 07:05:00'),
(12, 12, 'midtrans', 'gopay', 128000.00, 'paid', '2025-10-31 08:04:00', '2025-10-31 08:05:00', 'INV-20251031-0012', 'MID-000012', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"128000.00\"}', '2025-10-31 08:04:00', '2025-10-31 08:05:00'),
(13, 13, 'midtrans', 'bni_va', 310000.00, 'paid', '2025-11-01 03:04:00', '2025-11-01 03:05:00', 'INV-20251101-0013', 'MID-000013', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"310000.00\"}', '2025-11-01 03:04:00', '2025-11-01 03:05:00'),
(14, 14, 'midtrans', 'bni_va', 196000.00, 'paid', '2025-11-01 04:04:00', '2025-11-01 04:05:00', 'INV-20251101-0014', 'MID-000014', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"196000.00\"}', '2025-11-01 04:04:00', '2025-11-01 04:05:00'),
(15, 15, 'midtrans', 'shopeepay', 117000.00, 'paid', '2025-11-01 05:04:00', '2025-11-01 05:05:00', 'INV-20251101-0015', 'MID-000015', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"117000.00\"}', '2025-11-01 05:04:00', '2025-11-01 05:05:00'),
(16, 16, 'midtrans', 'bca_va', 247000.00, 'paid', '2025-11-01 06:04:00', '2025-11-01 06:05:00', 'INV-20251101-0016', 'MID-000016', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"247000.00\"}', '2025-11-01 06:04:00', '2025-11-01 06:05:00'),
(17, 17, 'midtrans', 'gopay', 120000.00, 'paid', '2025-11-01 07:04:00', '2025-11-01 07:05:00', 'INV-20251101-0017', 'MID-000017', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"120000.00\"}', '2025-11-01 07:04:00', '2025-11-01 07:05:00'),
(18, 18, 'midtrans', 'bca_va', 144000.00, 'paid', '2025-11-01 08:04:00', '2025-11-01 08:05:00', 'INV-20251101-0018', 'MID-000018', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"144000.00\"}', '2025-11-01 08:04:00', '2025-11-01 08:05:00'),
(19, 19, 'midtrans', 'shopeepay', 280000.00, 'paid', '2025-11-02 03:04:00', '2025-11-02 03:05:00', 'INV-20251102-0019', 'MID-000019', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"280000.00\"}', '2025-11-02 03:04:00', '2025-11-02 03:05:00'),
(20, 20, 'midtrans', 'bni_va', 224000.00, 'paid', '2025-11-02 04:04:00', '2025-11-02 04:05:00', 'INV-20251102-0020', 'MID-000020', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"224000.00\"}', '2025-11-02 04:04:00', '2025-11-02 04:05:00'),
(21, 21, 'midtrans', 'shopeepay', 108000.00, 'paid', '2025-11-02 05:04:00', '2025-11-02 05:05:00', 'INV-20251102-0021', 'MID-000021', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"108000.00\"}', '2025-11-02 05:04:00', '2025-11-02 05:05:00'),
(22, 22, 'midtrans', 'bni_va', 208000.00, 'paid', '2025-11-02 06:04:00', '2025-11-02 06:05:00', 'INV-20251102-0022', 'MID-000022', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"208000.00\"}', '2025-11-02 06:04:00', '2025-11-02 06:05:00'),
(23, 23, 'midtrans', 'bri_va', 156000.00, 'paid', '2025-11-02 07:04:00', '2025-11-02 07:05:00', 'INV-20251102-0023', 'MID-000023', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"156000.00\"}', '2025-11-02 07:04:00', '2025-11-02 07:05:00'),
(24, 24, 'midtrans', 'bni_va', 176000.00, 'paid', '2025-11-02 08:04:00', '2025-11-02 08:05:00', 'INV-20251102-0024', 'MID-000024', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"176000.00\"}', '2025-11-02 08:04:00', '2025-11-02 08:05:00'),
(25, 25, 'midtrans', 'bri_va', 340000.00, 'paid', '2025-11-03 03:04:00', '2025-11-03 03:05:00', 'INV-20251103-0025', 'MID-000025', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"340000.00\"}', '2025-11-03 03:04:00', '2025-11-03 03:05:00'),
(26, 26, 'midtrans', 'bni_va', 210000.00, 'paid', '2025-11-03 04:04:00', '2025-11-03 04:05:00', 'INV-20251103-0026', 'MID-000026', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"210000.00\"}', '2025-11-03 04:04:00', '2025-11-03 04:05:00'),
(27, 27, 'midtrans', 'bni_va', 135000.00, 'paid', '2025-11-03 05:04:00', '2025-11-03 05:05:00', 'INV-20251103-0027', 'MID-000027', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"135000.00\"}', '2025-11-03 05:04:00', '2025-11-03 05:05:00'),
(28, 28, 'midtrans', 'bca_va', 286000.00, 'paid', '2025-11-03 06:04:00', '2025-11-03 06:05:00', 'INV-20251103-0028', 'MID-000028', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"286000.00\"}', '2025-11-03 06:04:00', '2025-11-03 06:05:00'),
(29, 29, 'midtrans', 'qris', 144000.00, 'paid', '2025-11-03 07:04:00', '2025-11-03 07:05:00', 'INV-20251103-0029', 'MID-000029', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"144000.00\"}', '2025-11-03 07:04:00', '2025-11-03 07:05:00'),
(30, 30, 'midtrans', 'bni_va', 160000.00, 'paid', '2025-11-03 08:04:00', '2025-11-03 08:05:00', 'INV-20251103-0030', 'MID-000030', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"160000.00\"}', '2025-11-03 08:04:00', '2025-11-03 08:05:00'),
(31, 31, 'midtrans', 'qris', 300000.00, 'paid', '2025-11-04 03:04:00', '2025-11-04 03:05:00', 'INV-20251104-0031', 'MID-000031', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"300000.00\"}', '2025-11-04 03:04:00', '2025-11-04 03:05:00'),
(32, 32, 'midtrans', 'qris', 280000.00, 'paid', '2025-11-04 04:04:00', '2025-11-04 04:05:00', 'INV-20251104-0032', 'MID-000032', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"280000.00\"}', '2025-11-04 04:04:00', '2025-11-04 04:05:00'),
(33, 33, 'midtrans', 'qris', 135000.00, 'paid', '2025-11-04 05:04:00', '2025-11-04 05:05:00', 'INV-20251104-0033', 'MID-000033', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"135000.00\"}', '2025-11-04 05:04:00', '2025-11-04 05:05:00'),
(34, 34, 'midtrans', 'gopay', 273000.00, 'paid', '2025-11-04 06:04:00', '2025-11-04 06:05:00', 'INV-20251104-0034', 'MID-000034', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"273000.00\"}', '2025-11-04 06:04:00', '2025-11-04 06:05:00'),
(35, 35, 'midtrans', 'bca_va', 168000.00, 'paid', '2025-11-04 07:04:00', '2025-11-04 07:05:00', 'INV-20251104-0035', 'MID-000035', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"168000.00\"}', '2025-11-04 07:04:00', '2025-11-04 07:05:00'),
(36, 36, 'midtrans', 'bni_va', 192000.00, 'paid', '2025-11-04 08:04:00', '2025-11-04 08:05:00', 'INV-20251104-0036', 'MID-000036', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"192000.00\"}', '2025-11-04 08:04:00', '2025-11-04 08:05:00'),
(37, 37, 'midtrans', 'bni_va', 340000.00, 'paid', '2025-11-05 03:04:00', '2025-11-05 03:05:00', 'INV-20251105-0037', 'MID-000037', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"340000.00\"}', '2025-11-05 03:04:00', '2025-11-05 03:05:00'),
(38, 38, 'midtrans', 'shopeepay', 266000.00, 'paid', '2025-11-05 04:04:00', '2025-11-05 04:05:00', 'INV-20251105-0038', 'MID-000038', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"266000.00\"}', '2025-11-05 04:04:00', '2025-11-05 04:05:00'),
(39, 39, 'midtrans', 'gopay', 153000.00, 'paid', '2025-11-05 05:04:00', '2025-11-05 05:05:00', 'INV-20251105-0039', 'MID-000039', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"153000.00\"}', '2025-11-05 05:04:00', '2025-11-05 05:05:00'),
(40, 40, 'midtrans', 'bca_va', 273000.00, 'paid', '2025-11-05 06:04:00', '2025-11-05 06:05:00', 'INV-20251105-0040', 'MID-000040', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"273000.00\"}', '2025-11-05 06:04:00', '2025-11-05 06:05:00'),
(41, 41, 'midtrans', 'gopay', 144000.00, 'paid', '2025-11-05 07:04:00', '2025-11-05 07:05:00', 'INV-20251105-0041', 'MID-000041', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"144000.00\"}', '2025-11-05 07:04:00', '2025-11-05 07:05:00'),
(42, 42, 'midtrans', 'shopeepay', 144000.00, 'paid', '2025-11-05 08:04:00', '2025-11-05 08:05:00', 'INV-20251105-0042', 'MID-000042', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"144000.00\"}', '2025-11-05 08:04:00', '2025-11-05 08:05:00'),
(43, 43, 'midtrans', 'shopeepay', 290000.00, 'paid', '2025-11-06 03:04:00', '2025-11-06 03:05:00', 'INV-20251106-0043', 'MID-000043', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"290000.00\"}', '2025-11-06 03:04:00', '2025-11-06 03:05:00'),
(44, 44, 'midtrans', 'qris', 238000.00, 'paid', '2025-11-06 04:04:00', '2025-11-06 04:05:00', 'INV-20251106-0044', 'MID-000044', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"238000.00\"}', '2025-11-06 04:04:00', '2025-11-06 04:05:00'),
(45, 45, 'midtrans', 'bni_va', 126000.00, 'paid', '2025-11-06 05:04:00', '2025-11-06 05:05:00', 'INV-20251106-0045', 'MID-000045', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"126000.00\"}', '2025-11-06 05:04:00', '2025-11-06 05:05:00'),
(46, 46, 'midtrans', 'bca_va', 273000.00, 'paid', '2025-11-06 06:04:00', '2025-11-06 06:05:00', 'INV-20251106-0046', 'MID-000046', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"273000.00\"}', '2025-11-06 06:04:00', '2025-11-06 06:05:00'),
(47, 47, 'midtrans', 'bni_va', 156000.00, 'paid', '2025-11-06 07:04:00', '2025-11-06 07:05:00', 'INV-20251106-0047', 'MID-000047', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"156000.00\"}', '2025-11-06 07:04:00', '2025-11-06 07:05:00'),
(48, 48, 'midtrans', 'bri_va', 128000.00, 'paid', '2025-11-06 08:04:00', '2025-11-06 08:05:00', 'INV-20251106-0048', 'MID-000048', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"128000.00\"}', '2025-11-06 08:04:00', '2025-11-06 08:05:00'),
(49, 49, 'midtrans', 'shopeepay', 380000.00, 'paid', '2025-11-07 03:04:00', '2025-11-07 03:05:00', 'INV-20251107-0049', 'MID-000049', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"380000.00\"}', '2025-11-07 03:04:00', '2025-11-07 03:05:00'),
(50, 50, 'midtrans', 'shopeepay', 224000.00, 'paid', '2025-11-07 04:04:00', '2025-11-07 04:05:00', 'INV-20251107-0050', 'MID-000050', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"224000.00\"}', '2025-11-07 04:04:00', '2025-11-07 04:05:00'),
(51, 51, 'midtrans', 'shopeepay', 153000.00, 'paid', '2025-11-07 05:04:00', '2025-11-07 05:05:00', 'INV-20251107-0051', 'MID-000051', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"153000.00\"}', '2025-11-07 05:04:00', '2025-11-07 05:05:00'),
(52, 52, 'midtrans', 'bni_va', 234000.00, 'paid', '2025-11-07 06:04:00', '2025-11-07 06:05:00', 'INV-20251107-0052', 'MID-000052', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"234000.00\"}', '2025-11-07 06:04:00', '2025-11-07 06:05:00'),
(53, 53, 'midtrans', 'bni_va', 168000.00, 'paid', '2025-11-07 07:04:00', '2025-11-07 07:05:00', 'INV-20251107-0053', 'MID-000053', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"168000.00\"}', '2025-11-07 07:04:00', '2025-11-07 07:05:00'),
(54, 54, 'midtrans', 'bni_va', 176000.00, 'paid', '2025-11-07 08:04:00', '2025-11-07 08:05:00', 'INV-20251107-0054', 'MID-000054', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"176000.00\"}', '2025-11-07 08:04:00', '2025-11-07 08:05:00'),
(55, 55, 'midtrans', 'bca_va', 290000.00, 'paid', '2025-11-08 03:04:00', '2025-11-08 03:05:00', 'INV-20251108-0055', 'MID-000055', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"290000.00\"}', '2025-11-08 03:04:00', '2025-11-08 03:05:00'),
(56, 56, 'midtrans', 'qris', 252000.00, 'paid', '2025-11-08 04:04:00', '2025-11-08 04:05:00', 'INV-20251108-0056', 'MID-000056', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"252000.00\"}', '2025-11-08 04:04:00', '2025-11-08 04:05:00'),
(57, 57, 'midtrans', 'bca_va', 153000.00, 'paid', '2025-11-08 05:04:00', '2025-11-08 05:05:00', 'INV-20251108-0057', 'MID-000057', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"153000.00\"}', '2025-11-08 05:04:00', '2025-11-08 05:05:00'),
(58, 58, 'midtrans', 'qris', 247000.00, 'paid', '2025-11-08 06:04:00', '2025-11-08 06:05:00', 'INV-20251108-0058', 'MID-000058', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"247000.00\"}', '2025-11-08 06:04:00', '2025-11-08 06:05:00'),
(59, 59, 'midtrans', 'gopay', 120000.00, 'paid', '2025-11-08 07:04:00', '2025-11-08 07:05:00', 'INV-20251108-0059', 'MID-000059', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"120000.00\"}', '2025-11-08 07:04:00', '2025-11-08 07:05:00'),
(60, 60, 'midtrans', 'gopay', 144000.00, 'paid', '2025-11-08 08:04:00', '2025-11-08 08:05:00', 'INV-20251108-0060', 'MID-000060', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"144000.00\"}', '2025-11-08 08:04:00', '2025-11-08 08:05:00'),
(61, 61, 'midtrans', 'qris', 350000.00, 'paid', '2025-11-09 03:04:00', '2025-11-09 03:05:00', 'INV-20251109-0061', 'MID-000061', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"350000.00\"}', '2025-11-09 03:04:00', '2025-11-09 03:05:00'),
(62, 62, 'midtrans', 'bni_va', 266000.00, 'paid', '2025-11-09 04:04:00', '2025-11-09 04:05:00', 'INV-20251109-0062', 'MID-000062', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"266000.00\"}', '2025-11-09 04:04:00', '2025-11-09 04:05:00'),
(63, 63, 'midtrans', 'qris', 126000.00, 'paid', '2025-11-09 05:04:00', '2025-11-09 05:05:00', 'INV-20251109-0063', 'MID-000063', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"126000.00\"}', '2025-11-09 05:04:00', '2025-11-09 05:05:00'),
(64, 64, 'midtrans', 'gopay', 273000.00, 'paid', '2025-11-09 06:04:00', '2025-11-09 06:05:00', 'INV-20251109-0064', 'MID-000064', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"273000.00\"}', '2025-11-09 06:04:00', '2025-11-09 06:05:00'),
(65, 65, 'midtrans', 'bri_va', 156000.00, 'paid', '2025-11-09 07:04:00', '2025-11-09 07:05:00', 'INV-20251109-0065', 'MID-000065', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"156000.00\"}', '2025-11-09 07:04:00', '2025-11-09 07:05:00'),
(66, 66, 'midtrans', 'qris', 144000.00, 'paid', '2025-11-09 08:04:00', '2025-11-09 08:05:00', 'INV-20251109-0066', 'MID-000066', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"144000.00\"}', '2025-11-09 08:04:00', '2025-11-09 08:05:00'),
(67, 67, 'midtrans', 'shopeepay', 350000.00, 'paid', '2025-11-10 03:04:00', '2025-11-10 03:05:00', 'INV-20251110-0067', 'MID-000067', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"350000.00\"}', '2025-11-10 03:04:00', '2025-11-10 03:05:00'),
(68, 68, 'midtrans', 'bca_va', 280000.00, 'paid', '2025-11-10 04:04:00', '2025-11-10 04:05:00', 'INV-20251110-0068', 'MID-000068', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"280000.00\"}', '2025-11-10 04:04:00', '2025-11-10 04:05:00'),
(69, 69, 'midtrans', 'shopeepay', 117000.00, 'paid', '2025-11-10 05:04:00', '2025-11-10 05:05:00', 'INV-20251110-0069', 'MID-000069', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"117000.00\"}', '2025-11-10 05:04:00', '2025-11-10 05:05:00'),
(70, 70, 'midtrans', 'bca_va', 208000.00, 'paid', '2025-11-10 06:04:00', '2025-11-10 06:05:00', 'INV-20251110-0070', 'MID-000070', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"208000.00\"}', '2025-11-10 06:04:00', '2025-11-10 06:05:00'),
(71, 71, 'midtrans', 'shopeepay', 144000.00, 'paid', '2025-11-10 07:04:00', '2025-11-10 07:05:00', 'INV-20251110-0071', 'MID-000071', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"144000.00\"}', '2025-11-10 07:04:00', '2025-11-10 07:05:00'),
(72, 72, 'midtrans', 'bri_va', 144000.00, 'paid', '2025-11-10 08:04:00', '2025-11-10 08:05:00', 'INV-20251110-0072', 'MID-000072', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"144000.00\"}', '2025-11-10 08:04:00', '2025-11-10 08:05:00'),
(73, 73, 'midtrans', 'bri_va', 380000.00, 'paid', '2025-11-11 03:04:00', '2025-11-11 03:05:00', 'INV-20251111-0073', 'MID-000073', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"380000.00\"}', '2025-11-11 03:04:00', '2025-11-11 03:05:00'),
(74, 74, 'midtrans', 'qris', 280000.00, 'paid', '2025-11-11 04:04:00', '2025-11-11 04:05:00', 'INV-20251111-0074', 'MID-000074', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"280000.00\"}', '2025-11-11 04:04:00', '2025-11-11 04:05:00'),
(75, 75, 'midtrans', 'bni_va', 108000.00, 'paid', '2025-11-11 05:04:00', '2025-11-11 05:05:00', 'INV-20251111-0075', 'MID-000075', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"108000.00\"}', '2025-11-11 05:04:00', '2025-11-11 05:05:00'),
(76, 76, 'midtrans', 'bni_va', 260000.00, 'paid', '2025-11-11 06:04:00', '2025-11-11 06:05:00', 'INV-20251111-0076', 'MID-000076', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"260000.00\"}', '2025-11-11 06:04:00', '2025-11-11 06:05:00'),
(77, 77, 'midtrans', 'bca_va', 168000.00, 'paid', '2025-11-11 07:04:00', '2025-11-11 07:05:00', 'INV-20251111-0077', 'MID-000077', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"168000.00\"}', '2025-11-11 07:04:00', '2025-11-11 07:05:00'),
(78, 78, 'midtrans', 'bri_va', 144000.00, 'paid', '2025-11-11 08:04:00', '2025-11-11 08:05:00', 'INV-20251111-0078', 'MID-000078', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"144000.00\"}', '2025-11-11 08:04:00', '2025-11-11 08:05:00'),
(79, 79, 'midtrans', 'gopay', 400000.00, 'paid', '2025-11-12 03:04:00', '2025-11-12 03:05:00', 'INV-20251112-0079', 'MID-000079', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"400000.00\"}', '2025-11-12 03:04:00', '2025-11-12 03:05:00'),
(80, 80, 'midtrans', 'shopeepay', 196000.00, 'paid', '2025-11-12 04:04:00', '2025-11-12 04:05:00', 'INV-20251112-0080', 'MID-000080', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"196000.00\"}', '2025-11-12 04:04:00', '2025-11-12 04:05:00'),
(81, 81, 'midtrans', 'bni_va', 144000.00, 'paid', '2025-11-12 05:04:00', '2025-11-12 05:05:00', 'INV-20251112-0081', 'MID-000081', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"144000.00\"}', '2025-11-12 05:04:00', '2025-11-12 05:05:00'),
(82, 82, 'midtrans', 'gopay', 247000.00, 'paid', '2025-11-12 06:04:00', '2025-11-12 06:05:00', 'INV-20251112-0082', 'MID-000082', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"247000.00\"}', '2025-11-12 06:04:00', '2025-11-12 06:05:00'),
(83, 83, 'midtrans', 'bni_va', 120000.00, 'paid', '2025-11-12 07:04:00', '2025-11-12 07:05:00', 'INV-20251112-0083', 'MID-000083', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"120000.00\"}', '2025-11-12 07:04:00', '2025-11-12 07:05:00'),
(84, 84, 'midtrans', 'qris', 160000.00, 'paid', '2025-11-12 08:04:00', '2025-11-12 08:05:00', 'INV-20251112-0084', 'MID-000084', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"160000.00\"}', '2025-11-12 08:04:00', '2025-11-12 08:05:00'),
(85, 85, 'midtrans', 'shopeepay', 340000.00, 'paid', '2025-11-13 03:04:00', '2025-11-13 03:05:00', 'INV-20251113-0085', 'MID-000085', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"340000.00\"}', '2025-11-13 03:04:00', '2025-11-13 03:05:00'),
(86, 86, 'midtrans', 'bca_va', 252000.00, 'paid', '2025-11-13 04:04:00', '2025-11-13 04:05:00', 'INV-20251113-0086', 'MID-000086', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"252000.00\"}', '2025-11-13 04:04:00', '2025-11-13 04:05:00'),
(87, 87, 'midtrans', 'bca_va', 108000.00, 'paid', '2025-11-13 05:04:00', '2025-11-13 05:05:00', 'INV-20251113-0087', 'MID-000087', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"108000.00\"}', '2025-11-13 05:04:00', '2025-11-13 05:05:00'),
(88, 88, 'midtrans', 'bni_va', 286000.00, 'paid', '2025-11-13 06:04:00', '2025-11-13 06:05:00', 'INV-20251113-0088', 'MID-000088', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"286000.00\"}', '2025-11-13 06:04:00', '2025-11-13 06:05:00'),
(89, 89, 'midtrans', 'gopay', 144000.00, 'paid', '2025-11-13 07:04:00', '2025-11-13 07:05:00', 'INV-20251113-0089', 'MID-000089', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"144000.00\"}', '2025-11-13 07:04:00', '2025-11-13 07:05:00'),
(90, 90, 'midtrans', 'bni_va', 192000.00, 'paid', '2025-11-13 08:04:00', '2025-11-13 08:05:00', 'INV-20251113-0090', 'MID-000090', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"192000.00\"}', '2025-11-13 08:04:00', '2025-11-13 08:05:00'),
(91, 91, 'midtrans', 'bca_va', 360000.00, 'paid', '2025-11-14 03:04:00', '2025-11-14 03:05:00', 'INV-20251114-0091', 'MID-000091', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"360000.00\"}', '2025-11-14 03:04:00', '2025-11-14 03:05:00'),
(92, 92, 'midtrans', 'bri_va', 224000.00, 'paid', '2025-11-14 04:04:00', '2025-11-14 04:05:00', 'INV-20251114-0092', 'MID-000092', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"224000.00\"}', '2025-11-14 04:04:00', '2025-11-14 04:05:00'),
(93, 93, 'midtrans', 'gopay', 162000.00, 'paid', '2025-11-14 05:04:00', '2025-11-14 05:05:00', 'INV-20251114-0093', 'MID-000093', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"162000.00\"}', '2025-11-14 05:04:00', '2025-11-14 05:05:00'),
(94, 94, 'midtrans', 'bri_va', 260000.00, 'paid', '2025-11-14 06:04:00', '2025-11-14 06:05:00', 'INV-20251114-0094', 'MID-000094', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"260000.00\"}', '2025-11-14 06:04:00', '2025-11-14 06:05:00'),
(95, 95, 'midtrans', 'gopay', 132000.00, 'paid', '2025-11-14 07:04:00', '2025-11-14 07:05:00', 'INV-20251114-0095', 'MID-000095', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"132000.00\"}', '2025-11-14 07:04:00', '2025-11-14 07:05:00'),
(96, 96, 'midtrans', 'bni_va', 128000.00, 'paid', '2025-11-14 08:04:00', '2025-11-14 08:05:00', 'INV-20251114-0096', 'MID-000096', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"128000.00\"}', '2025-11-14 08:04:00', '2025-11-14 08:05:00'),
(97, 97, 'midtrans', 'gopay', 280000.00, 'paid', '2025-11-15 03:04:00', '2025-11-15 03:05:00', 'INV-20251115-0097', 'MID-000097', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"280000.00\"}', '2025-11-15 03:04:00', '2025-11-15 03:05:00'),
(98, 98, 'midtrans', 'bni_va', 224000.00, 'paid', '2025-11-15 04:04:00', '2025-11-15 04:05:00', 'INV-20251115-0098', 'MID-000098', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"224000.00\"}', '2025-11-15 04:04:00', '2025-11-15 04:05:00'),
(99, 99, 'midtrans', 'gopay', 108000.00, 'paid', '2025-11-15 05:04:00', '2025-11-15 05:05:00', 'INV-20251115-0099', 'MID-000099', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"108000.00\"}', '2025-11-15 05:04:00', '2025-11-15 05:05:00'),
(100, 100, 'midtrans', 'gopay', 221000.00, 'paid', '2025-11-15 06:04:00', '2025-11-15 06:05:00', 'INV-20251115-0100', 'MID-000100', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"221000.00\"}', '2025-11-15 06:04:00', '2025-11-15 06:05:00'),
(101, 101, 'midtrans', 'bni_va', 120000.00, 'paid', '2025-11-15 07:04:00', '2025-11-15 07:05:00', 'INV-20251115-0101', 'MID-000101', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"120000.00\"}', '2025-11-15 07:04:00', '2025-11-15 07:05:00'),
(102, 102, 'midtrans', 'bri_va', 128000.00, 'paid', '2025-11-15 08:04:00', '2025-11-15 08:05:00', 'INV-20251115-0102', 'MID-000102', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"128000.00\"}', '2025-11-15 08:04:00', '2025-11-15 08:05:00'),
(103, 103, 'midtrans', 'bri_va', 380000.00, 'paid', '2025-11-16 03:04:00', '2025-11-16 03:05:00', 'INV-20251116-0103', 'MID-000103', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"380000.00\"}', '2025-11-16 03:04:00', '2025-11-16 03:05:00'),
(104, 104, 'midtrans', 'gopay', 252000.00, 'paid', '2025-11-16 04:04:00', '2025-11-16 04:05:00', 'INV-20251116-0104', 'MID-000104', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"252000.00\"}', '2025-11-16 04:04:00', '2025-11-16 04:05:00'),
(105, 105, 'midtrans', 'bni_va', 117000.00, 'paid', '2025-11-16 05:04:00', '2025-11-16 05:05:00', 'INV-20251116-0105', 'MID-000105', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"117000.00\"}', '2025-11-16 05:04:00', '2025-11-16 05:05:00'),
(106, 106, 'midtrans', 'shopeepay', 208000.00, 'paid', '2025-11-16 06:04:00', '2025-11-16 06:05:00', 'INV-20251116-0106', 'MID-000106', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"208000.00\"}', '2025-11-16 06:04:00', '2025-11-16 06:05:00'),
(107, 107, 'midtrans', 'bri_va', 120000.00, 'paid', '2025-11-16 07:04:00', '2025-11-16 07:05:00', 'INV-20251116-0107', 'MID-000107', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"120000.00\"}', '2025-11-16 07:04:00', '2025-11-16 07:05:00'),
(108, 108, 'midtrans', 'bri_va', 144000.00, 'paid', '2025-11-16 08:04:00', '2025-11-16 08:05:00', 'INV-20251116-0108', 'MID-000108', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"144000.00\"}', '2025-11-16 08:04:00', '2025-11-16 08:05:00'),
(109, 109, 'midtrans', 'gopay', 300000.00, 'paid', '2025-11-17 03:04:00', '2025-11-17 03:05:00', 'INV-20251117-0109', 'MID-000109', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"300000.00\"}', '2025-11-17 03:04:00', '2025-11-17 03:05:00'),
(110, 110, 'midtrans', 'bni_va', 210000.00, 'paid', '2025-11-17 04:04:00', '2025-11-17 04:05:00', 'INV-20251117-0110', 'MID-000110', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"210000.00\"}', '2025-11-17 04:04:00', '2025-11-17 04:05:00'),
(111, 111, 'midtrans', 'qris', 144000.00, 'paid', '2025-11-17 05:04:00', '2025-11-17 05:05:00', 'INV-20251117-0111', 'MID-000111', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"144000.00\"}', '2025-11-17 05:04:00', '2025-11-17 05:05:00'),
(112, 112, 'midtrans', 'shopeepay', 208000.00, 'paid', '2025-11-17 06:04:00', '2025-11-17 06:05:00', 'INV-20251117-0112', 'MID-000112', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"208000.00\"}', '2025-11-17 06:04:00', '2025-11-17 06:05:00'),
(113, 113, 'midtrans', 'gopay', 168000.00, 'paid', '2025-11-17 07:04:00', '2025-11-17 07:05:00', 'INV-20251117-0113', 'MID-000113', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"168000.00\"}', '2025-11-17 07:04:00', '2025-11-17 07:05:00'),
(114, 114, 'midtrans', 'bca_va', 176000.00, 'paid', '2025-11-17 08:04:00', '2025-11-17 08:05:00', 'INV-20251117-0114', 'MID-000114', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"176000.00\"}', '2025-11-17 08:04:00', '2025-11-17 08:05:00'),
(115, 115, 'midtrans', 'qris', 350000.00, 'paid', '2025-11-18 03:04:00', '2025-11-18 03:05:00', 'INV-20251118-0115', 'MID-000115', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"350000.00\"}', '2025-11-18 03:04:00', '2025-11-18 03:05:00'),
(116, 116, 'midtrans', 'qris', 238000.00, 'paid', '2025-11-18 04:04:00', '2025-11-18 04:05:00', 'INV-20251118-0116', 'MID-000116', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"238000.00\"}', '2025-11-18 04:04:00', '2025-11-18 04:05:00'),
(117, 117, 'midtrans', 'qris', 144000.00, 'paid', '2025-11-18 05:04:00', '2025-11-18 05:05:00', 'INV-20251118-0117', 'MID-000117', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"144000.00\"}', '2025-11-18 05:04:00', '2025-11-18 05:05:00'),
(118, 118, 'midtrans', 'qris', 273000.00, 'paid', '2025-11-18 06:04:00', '2025-11-18 06:05:00', 'INV-20251118-0118', 'MID-000118', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"273000.00\"}', '2025-11-18 06:04:00', '2025-11-18 06:05:00'),
(119, 119, 'midtrans', 'bri_va', 144000.00, 'paid', '2025-11-18 07:04:00', '2025-11-18 07:05:00', 'INV-20251118-0119', 'MID-000119', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"144000.00\"}', '2025-11-18 07:04:00', '2025-11-18 07:05:00'),
(120, 120, 'midtrans', 'bri_va', 144000.00, 'paid', '2025-11-18 08:04:00', '2025-11-18 08:05:00', 'INV-20251118-0120', 'MID-000120', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"144000.00\"}', '2025-11-18 08:04:00', '2025-11-18 08:05:00'),
(121, 121, 'midtrans', 'qris', 310000.00, 'paid', '2025-11-19 03:04:00', '2025-11-19 03:05:00', 'INV-20251119-0121', 'MID-000121', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"310000.00\"}', '2025-11-19 03:04:00', '2025-11-19 03:05:00'),
(122, 122, 'midtrans', 'bri_va', 266000.00, 'paid', '2025-11-19 04:04:00', '2025-11-19 04:05:00', 'INV-20251119-0122', 'MID-000122', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"266000.00\"}', '2025-11-19 04:04:00', '2025-11-19 04:05:00'),
(123, 123, 'midtrans', 'bca_va', 126000.00, 'paid', '2025-11-19 05:04:00', '2025-11-19 05:05:00', 'INV-20251119-0123', 'MID-000123', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"126000.00\"}', '2025-11-19 05:04:00', '2025-11-19 05:05:00'),
(124, 124, 'midtrans', 'bni_va', 273000.00, 'paid', '2025-11-19 06:04:00', '2025-11-19 06:05:00', 'INV-20251119-0124', 'MID-000124', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"273000.00\"}', '2025-11-19 06:04:00', '2025-11-19 06:05:00'),
(125, 125, 'midtrans', 'qris', 156000.00, 'paid', '2025-11-19 07:04:00', '2025-11-19 07:05:00', 'INV-20251119-0125', 'MID-000125', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"156000.00\"}', '2025-11-19 07:04:00', '2025-11-19 07:05:00'),
(126, 126, 'midtrans', 'shopeepay', 160000.00, 'paid', '2025-11-19 08:04:00', '2025-11-19 08:05:00', 'INV-20251119-0126', 'MID-000126', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"160000.00\"}', '2025-11-19 08:04:00', '2025-11-19 08:05:00'),
(127, 127, 'midtrans', 'bri_va', 350000.00, 'paid', '2025-11-20 03:04:00', '2025-11-20 03:05:00', 'INV-20251120-0127', 'MID-000127', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"350000.00\"}', '2025-11-20 03:04:00', '2025-11-20 03:05:00'),
(128, 128, 'midtrans', 'qris', 224000.00, 'paid', '2025-11-20 04:04:00', '2025-11-20 04:05:00', 'INV-20251120-0128', 'MID-000128', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"224000.00\"}', '2025-11-20 04:04:00', '2025-11-20 04:05:00'),
(129, 129, 'midtrans', 'bni_va', 135000.00, 'paid', '2025-11-20 05:04:00', '2025-11-20 05:05:00', 'INV-20251120-0129', 'MID-000129', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"135000.00\"}', '2025-11-20 05:04:00', '2025-11-20 05:05:00'),
(130, 130, 'midtrans', 'gopay', 247000.00, 'paid', '2025-11-20 06:04:00', '2025-11-20 06:05:00', 'INV-20251120-0130', 'MID-000130', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"247000.00\"}', '2025-11-20 06:04:00', '2025-11-20 06:05:00'),
(131, 131, 'midtrans', 'shopeepay', 156000.00, 'paid', '2025-11-20 07:04:00', '2025-11-20 07:05:00', 'INV-20251120-0131', 'MID-000131', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"156000.00\"}', '2025-11-20 07:04:00', '2025-11-20 07:05:00'),
(132, 132, 'midtrans', 'gopay', 128000.00, 'paid', '2025-11-20 08:04:00', '2025-11-20 08:05:00', 'INV-20251120-0132', 'MID-000132', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"128000.00\"}', '2025-11-20 08:04:00', '2025-11-20 08:05:00'),
(133, 133, 'midtrans', 'bri_va', 390000.00, 'paid', '2025-11-21 03:04:00', '2025-11-21 03:05:00', 'INV-20251121-0133', 'MID-000133', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"390000.00\"}', '2025-11-21 03:04:00', '2025-11-21 03:05:00'),
(134, 134, 'midtrans', 'bca_va', 266000.00, 'paid', '2025-11-21 04:04:00', '2025-11-21 04:05:00', 'INV-20251121-0134', 'MID-000134', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"266000.00\"}', '2025-11-21 04:04:00', '2025-11-21 04:05:00'),
(135, 135, 'midtrans', 'bni_va', 162000.00, 'paid', '2025-11-21 05:04:00', '2025-11-21 05:05:00', 'INV-20251121-0135', 'MID-000135', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"162000.00\"}', '2025-11-21 05:04:00', '2025-11-21 05:05:00'),
(136, 136, 'midtrans', 'bni_va', 260000.00, 'paid', '2025-11-21 06:04:00', '2025-11-21 06:05:00', 'INV-20251121-0136', 'MID-000136', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"260000.00\"}', '2025-11-21 06:04:00', '2025-11-21 06:05:00'),
(137, 137, 'midtrans', 'shopeepay', 120000.00, 'paid', '2025-11-21 07:04:00', '2025-11-21 07:05:00', 'INV-20251121-0137', 'MID-000137', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"120000.00\"}', '2025-11-21 07:04:00', '2025-11-21 07:05:00'),
(138, 138, 'midtrans', 'bca_va', 128000.00, 'paid', '2025-11-21 08:04:00', '2025-11-21 08:05:00', 'INV-20251121-0138', 'MID-000138', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"128000.00\"}', '2025-11-21 08:04:00', '2025-11-21 08:05:00'),
(139, 139, 'midtrans', 'bni_va', 360000.00, 'paid', '2025-11-22 03:04:00', '2025-11-22 03:05:00', 'INV-20251122-0139', 'MID-000139', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"360000.00\"}', '2025-11-22 03:04:00', '2025-11-22 03:05:00'),
(140, 140, 'midtrans', 'shopeepay', 280000.00, 'paid', '2025-11-22 04:04:00', '2025-11-22 04:05:00', 'INV-20251122-0140', 'MID-000140', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"280000.00\"}', '2025-11-22 04:04:00', '2025-11-22 04:05:00'),
(141, 141, 'midtrans', 'shopeepay', 153000.00, 'paid', '2025-11-22 05:04:00', '2025-11-22 05:05:00', 'INV-20251122-0141', 'MID-000141', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"153000.00\"}', '2025-11-22 05:04:00', '2025-11-22 05:05:00'),
(142, 142, 'midtrans', 'bri_va', 273000.00, 'paid', '2025-11-22 06:04:00', '2025-11-22 06:05:00', 'INV-20251122-0142', 'MID-000142', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"273000.00\"}', '2025-11-22 06:04:00', '2025-11-22 06:05:00'),
(143, 143, 'midtrans', 'qris', 168000.00, 'paid', '2025-11-22 07:04:00', '2025-11-22 07:05:00', 'INV-20251122-0143', 'MID-000143', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"168000.00\"}', '2025-11-22 07:04:00', '2025-11-22 07:05:00'),
(144, 144, 'midtrans', 'shopeepay', 176000.00, 'paid', '2025-11-22 08:04:00', '2025-11-22 08:05:00', 'INV-20251122-0144', 'MID-000144', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"176000.00\"}', '2025-11-22 08:04:00', '2025-11-22 08:05:00'),
(145, 145, 'midtrans', 'bca_va', 380000.00, 'paid', '2025-11-23 03:04:00', '2025-11-23 03:05:00', 'INV-20251123-0145', 'MID-000145', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"380000.00\"}', '2025-11-23 03:04:00', '2025-11-23 03:05:00'),
(146, 146, 'midtrans', 'bca_va', 210000.00, 'paid', '2025-11-23 04:04:00', '2025-11-23 04:05:00', 'INV-20251123-0146', 'MID-000146', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"210000.00\"}', '2025-11-23 04:04:00', '2025-11-23 04:05:00'),
(147, 147, 'midtrans', 'bri_va', 117000.00, 'paid', '2025-11-23 05:04:00', '2025-11-23 05:05:00', 'INV-20251123-0147', 'MID-000147', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"117000.00\"}', '2025-11-23 05:04:00', '2025-11-23 05:05:00'),
(148, 148, 'midtrans', 'bni_va', 234000.00, 'paid', '2025-11-23 06:04:00', '2025-11-23 06:05:00', 'INV-20251123-0148', 'MID-000148', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"234000.00\"}', '2025-11-23 06:04:00', '2025-11-23 06:05:00'),
(149, 149, 'midtrans', 'qris', 168000.00, 'paid', '2025-11-23 07:04:00', '2025-11-23 07:05:00', 'INV-20251123-0149', 'MID-000149', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"168000.00\"}', '2025-11-23 07:04:00', '2025-11-23 07:05:00'),
(150, 150, 'midtrans', 'gopay', 144000.00, 'paid', '2025-11-23 08:04:00', '2025-11-23 08:05:00', 'INV-20251123-0150', 'MID-000150', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"144000.00\"}', '2025-11-23 08:04:00', '2025-11-23 08:05:00'),
(151, 151, 'midtrans', 'bca_va', 280000.00, 'paid', '2025-11-24 03:04:00', '2025-11-24 03:05:00', 'INV-20251124-0151', 'MID-000151', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"280000.00\"}', '2025-11-24 03:04:00', '2025-11-24 03:05:00'),
(152, 152, 'midtrans', 'bri_va', 266000.00, 'paid', '2025-11-24 04:04:00', '2025-11-24 04:05:00', 'INV-20251124-0152', 'MID-000152', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"266000.00\"}', '2025-11-24 04:04:00', '2025-11-24 04:05:00'),
(153, 153, 'midtrans', 'bca_va', 153000.00, 'paid', '2025-11-24 05:04:00', '2025-11-24 05:05:00', 'INV-20251124-0153', 'MID-000153', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"153000.00\"}', '2025-11-24 05:04:00', '2025-11-24 05:05:00'),
(154, 154, 'midtrans', 'gopay', 247000.00, 'paid', '2025-11-24 06:04:00', '2025-11-24 06:05:00', 'INV-20251124-0154', 'MID-000154', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"247000.00\"}', '2025-11-24 06:04:00', '2025-11-24 06:05:00'),
(155, 155, 'midtrans', 'bri_va', 168000.00, 'paid', '2025-11-24 07:04:00', '2025-11-24 07:05:00', 'INV-20251124-0155', 'MID-000155', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"168000.00\"}', '2025-11-24 07:04:00', '2025-11-24 07:05:00'),
(156, 156, 'midtrans', 'bni_va', 144000.00, 'paid', '2025-11-24 08:04:00', '2025-11-24 08:05:00', 'INV-20251124-0156', 'MID-000156', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"144000.00\"}', '2025-11-24 08:04:00', '2025-11-24 08:05:00'),
(157, 157, 'midtrans', 'gopay', 390000.00, 'paid', '2025-11-25 03:04:00', '2025-11-25 03:05:00', 'INV-20251125-0157', 'MID-000157', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"390000.00\"}', '2025-11-25 03:04:00', '2025-11-25 03:05:00'),
(158, 158, 'midtrans', 'bni_va', 196000.00, 'paid', '2025-11-25 04:04:00', '2025-11-25 04:05:00', 'INV-20251125-0158', 'MID-000158', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"196000.00\"}', '2025-11-25 04:04:00', '2025-11-25 04:05:00'),
(159, 159, 'midtrans', 'bni_va', 117000.00, 'paid', '2025-11-25 05:04:00', '2025-11-25 05:05:00', 'INV-20251125-0159', 'MID-000159', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"117000.00\"}', '2025-11-25 05:04:00', '2025-11-25 05:05:00'),
(160, 160, 'midtrans', 'qris', 221000.00, 'paid', '2025-11-25 06:04:00', '2025-11-25 06:05:00', 'INV-20251125-0160', 'MID-000160', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"221000.00\"}', '2025-11-25 06:04:00', '2025-11-25 06:05:00'),
(161, 161, 'midtrans', 'gopay', 144000.00, 'paid', '2025-11-25 07:04:00', '2025-11-25 07:05:00', 'INV-20251125-0161', 'MID-000161', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"144000.00\"}', '2025-11-25 07:04:00', '2025-11-25 07:05:00'),
(162, 162, 'midtrans', 'shopeepay', 192000.00, 'paid', '2025-11-25 08:04:00', '2025-11-25 08:05:00', 'INV-20251125-0162', 'MID-000162', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"192000.00\"}', '2025-11-25 08:04:00', '2025-11-25 08:05:00'),
(163, 163, 'midtrans', 'gopay', 410000.00, 'paid', '2025-11-26 03:04:00', '2025-11-26 03:05:00', 'INV-20251126-0163', 'MID-000163', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"410000.00\"}', '2025-11-26 03:04:00', '2025-11-26 03:05:00'),
(164, 164, 'midtrans', 'bri_va', 210000.00, 'paid', '2025-11-26 04:04:00', '2025-11-26 04:05:00', 'INV-20251126-0164', 'MID-000164', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"210000.00\"}', '2025-11-26 04:04:00', '2025-11-26 04:05:00'),
(165, 165, 'midtrans', 'shopeepay', 135000.00, 'paid', '2025-11-26 05:04:00', '2025-11-26 05:05:00', 'INV-20251126-0165', 'MID-000165', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"135000.00\"}', '2025-11-26 05:04:00', '2025-11-26 05:05:00'),
(166, 166, 'midtrans', 'gopay', 286000.00, 'paid', '2025-11-26 06:04:00', '2025-11-26 06:05:00', 'INV-20251126-0166', 'MID-000166', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"286000.00\"}', '2025-11-26 06:04:00', '2025-11-26 06:05:00'),
(167, 167, 'midtrans', 'bni_va', 120000.00, 'paid', '2025-11-26 07:04:00', '2025-11-26 07:05:00', 'INV-20251126-0167', 'MID-000167', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"120000.00\"}', '2025-11-26 07:04:00', '2025-11-26 07:05:00'),
(168, 168, 'midtrans', 'gopay', 176000.00, 'paid', '2025-11-26 08:04:00', '2025-11-26 08:05:00', 'INV-20251126-0168', 'MID-000168', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"176000.00\"}', '2025-11-26 08:04:00', '2025-11-26 08:05:00'),
(169, 169, 'midtrans', 'gopay', 280000.00, 'paid', '2025-11-27 03:04:00', '2025-11-27 03:05:00', 'INV-20251127-0169', 'MID-000169', '{\"transaction_status\":\"settlement\",\"payment_type\":\"gopay\",\"gross_amount\":\"280000.00\"}', '2025-11-27 03:04:00', '2025-11-27 03:05:00'),
(170, 170, 'midtrans', 'bri_va', 280000.00, 'paid', '2025-11-27 04:04:00', '2025-11-27 04:05:00', 'INV-20251127-0170', 'MID-000170', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bri_va\",\"gross_amount\":\"280000.00\"}', '2025-11-27 04:04:00', '2025-11-27 04:05:00'),
(171, 171, 'midtrans', 'qris', 144000.00, 'paid', '2025-11-27 05:04:00', '2025-11-27 05:05:00', 'INV-20251127-0171', 'MID-000171', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"144000.00\"}', '2025-11-27 05:04:00', '2025-11-27 05:05:00'),
(172, 172, 'midtrans', 'shopeepay', 260000.00, 'paid', '2025-11-27 06:04:00', '2025-11-27 06:05:00', 'INV-20251127-0172', 'MID-000172', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"260000.00\"}', '2025-11-27 06:04:00', '2025-11-27 06:05:00'),
(173, 173, 'midtrans', 'shopeepay', 132000.00, 'paid', '2025-11-27 07:04:00', '2025-11-27 07:05:00', 'INV-20251127-0173', 'MID-000173', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"132000.00\"}', '2025-11-27 07:04:00', '2025-11-27 07:05:00'),
(174, 174, 'midtrans', 'qris', 128000.00, 'paid', '2025-11-27 08:04:00', '2025-11-27 08:05:00', 'INV-20251127-0174', 'MID-000174', '{\"transaction_status\":\"settlement\",\"payment_type\":\"qris\",\"gross_amount\":\"128000.00\"}', '2025-11-27 08:04:00', '2025-11-27 08:05:00'),
(175, 175, 'midtrans', 'bni_va', 300000.00, 'paid', '2025-11-28 03:04:00', '2025-11-28 03:05:00', 'INV-20251128-0175', 'MID-000175', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"300000.00\"}', '2025-11-28 03:04:00', '2025-11-28 03:05:00'),
(176, 176, 'midtrans', 'shopeepay', 266000.00, 'paid', '2025-11-28 04:04:00', '2025-11-28 04:05:00', 'INV-20251128-0176', 'MID-000176', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"266000.00\"}', '2025-11-28 04:04:00', '2025-11-28 04:05:00'),
(177, 177, 'midtrans', 'bni_va', 126000.00, 'paid', '2025-11-28 05:04:00', '2025-11-28 05:05:00', 'INV-20251128-0177', 'MID-000177', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bni_va\",\"gross_amount\":\"126000.00\"}', '2025-11-28 05:04:00', '2025-11-28 05:05:00'),
(178, 178, 'midtrans', 'shopeepay', 234000.00, 'paid', '2025-11-28 06:04:00', '2025-11-28 06:05:00', 'INV-20251128-0178', 'MID-000178', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"234000.00\"}', '2025-11-28 06:04:00', '2025-11-28 06:05:00');
INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `provider`, `metode`, `gross_amount`, `status`, `transaction_time`, `settlement_time`, `provider_order_id`, `provider_transaction_id`, `raw_response`, `created_at`, `updated_at`) VALUES
(179, 179, 'midtrans', 'bca_va', 120000.00, 'paid', '2025-11-28 07:04:00', '2025-11-28 07:05:00', 'INV-20251128-0179', 'MID-000179', '{\"transaction_status\":\"settlement\",\"payment_type\":\"bca_va\",\"gross_amount\":\"120000.00\"}', '2025-11-28 07:04:00', '2025-11-28 07:05:00'),
(180, 180, 'midtrans', 'shopeepay', 192000.00, 'paid', '2025-11-28 08:04:00', '2025-11-28 08:05:00', 'INV-20251128-0180', 'MID-000180', '{\"transaction_status\":\"settlement\",\"payment_type\":\"shopeepay\",\"gross_amount\":\"192000.00\"}', '2025-11-28 08:04:00', '2025-11-28 08:05:00'),
(181, 181, 'midtrans', 'qris', 72000.00, 'pending', '2025-11-30 08:58:13', NULL, 'ORD-20251130085812-15', 'febc60db-a919-4e54-813d-762e50c323d5', '{\"token\":\"febc60db-a919-4e54-813d-762e50c323d5\",\"redirect_url\":\"https:\\/\\/app.sandbox.midtrans.com\\/snap\\/v4\\/redirection\\/febc60db-a919-4e54-813d-762e50c323d5\"}', '2025-11-30 08:58:13', NULL),
(182, 181, 'midtrans', 'qris', 72000.00, 'pending', '2025-11-30 08:58:42', NULL, 'ORD-20251130085812-15-R1764493121', '567ff317-6171-4efb-b4c6-e781337ce55c', '{\"token\":\"567ff317-6171-4efb-b4c6-e781337ce55c\",\"redirect_url\":\"https:\\/\\/app.sandbox.midtrans.com\\/snap\\/v4\\/redirection\\/567ff317-6171-4efb-b4c6-e781337ce55c\"}', '2025-11-30 08:58:42', NULL),
(183, 182, 'midtrans', 'qris', 27000.00, 'pending', '2025-12-01 07:45:34', NULL, 'ORD-20251201074534-2', '4be770b2-3ea9-4d88-844d-1b4c5665abaa', '{\"token\":\"4be770b2-3ea9-4d88-844d-1b4c5665abaa\",\"redirect_url\":\"https:\\/\\/app.sandbox.midtrans.com\\/snap\\/v4\\/redirection\\/4be770b2-3ea9-4d88-844d-1b4c5665abaa\"}', '2025-12-01 07:45:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `kode_pesanan` varchar(30) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `payment_status` enum('unpaid','pending','paid','failed','expired','refunded') NOT NULL DEFAULT 'unpaid',
  `order_status` enum('created','processing','ready','completed','canceled') NOT NULL DEFAULT 'created',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `kode_pesanan`, `total_harga`, `payment_status`, `order_status`, `created_at`, `updated_at`, `paid_at`, `processed_at`, `ready_at`, `completed_at`, `canceled_at`) VALUES
(1, 22, 'ORD-20251030-0001', 310000.00, 'paid', 'completed', '2025-10-30 03:00:00', NULL, '2025-10-30 03:05:00', '2025-10-30 03:10:00', '2025-10-30 03:20:00', '2025-10-30 03:25:00', NULL),
(2, 23, 'ORD-20251030-0002', 280000.00, 'paid', 'completed', '2025-10-30 04:00:00', NULL, '2025-10-30 04:05:00', '2025-10-30 04:10:00', '2025-10-30 04:20:00', '2025-10-30 04:25:00', NULL),
(3, 7, 'ORD-20251030-0003', 117000.00, 'paid', 'completed', '2025-10-30 05:00:00', NULL, '2025-10-30 05:05:00', '2025-10-30 05:10:00', '2025-10-30 05:20:00', '2025-10-30 05:25:00', NULL),
(4, 25, 'ORD-20251030-0004', 260000.00, 'paid', 'completed', '2025-10-30 06:00:00', NULL, '2025-10-30 06:05:00', '2025-10-30 06:10:00', '2025-10-30 06:20:00', '2025-10-30 06:25:00', NULL),
(5, 17, 'ORD-20251030-0005', 156000.00, 'paid', 'completed', '2025-10-30 07:00:00', NULL, '2025-10-30 07:05:00', '2025-10-30 07:10:00', '2025-10-30 07:20:00', '2025-10-30 07:25:00', NULL),
(6, 5, 'ORD-20251030-0006', 128000.00, 'paid', 'completed', '2025-10-30 08:00:00', NULL, '2025-10-30 08:05:00', '2025-10-30 08:10:00', '2025-10-30 08:20:00', '2025-10-30 08:25:00', NULL),
(7, 24, 'ORD-20251031-0007', 310000.00, 'paid', 'completed', '2025-10-31 03:00:00', NULL, '2025-10-31 03:05:00', '2025-10-31 03:10:00', '2025-10-31 03:20:00', '2025-10-31 03:25:00', NULL),
(8, 15, 'ORD-20251031-0008', 196000.00, 'paid', 'completed', '2025-10-31 04:00:00', NULL, '2025-10-31 04:05:00', '2025-10-31 04:10:00', '2025-10-31 04:20:00', '2025-10-31 04:25:00', NULL),
(9, 15, 'ORD-20251031-0009', 108000.00, 'paid', 'completed', '2025-10-31 05:00:00', NULL, '2025-10-31 05:05:00', '2025-10-31 05:10:00', '2025-10-31 05:20:00', '2025-10-31 05:25:00', NULL),
(10, 10, 'ORD-20251031-0010', 234000.00, 'paid', 'completed', '2025-10-31 06:00:00', NULL, '2025-10-31 06:05:00', '2025-10-31 06:10:00', '2025-10-31 06:20:00', '2025-10-31 06:25:00', NULL),
(11, 25, 'ORD-20251031-0011', 168000.00, 'paid', 'completed', '2025-10-31 07:00:00', NULL, '2025-10-31 07:05:00', '2025-10-31 07:10:00', '2025-10-31 07:20:00', '2025-10-31 07:25:00', NULL),
(12, 21, 'ORD-20251031-0012', 128000.00, 'paid', 'completed', '2025-10-31 08:00:00', NULL, '2025-10-31 08:05:00', '2025-10-31 08:10:00', '2025-10-31 08:20:00', '2025-10-31 08:25:00', NULL),
(13, 9, 'ORD-20251101-0013', 310000.00, 'paid', 'completed', '2025-11-01 03:00:00', NULL, '2025-11-01 03:05:00', '2025-11-01 03:10:00', '2025-11-01 03:20:00', '2025-11-01 03:25:00', NULL),
(14, 13, 'ORD-20251101-0014', 196000.00, 'paid', 'completed', '2025-11-01 04:00:00', NULL, '2025-11-01 04:05:00', '2025-11-01 04:10:00', '2025-11-01 04:20:00', '2025-11-01 04:25:00', NULL),
(15, 7, 'ORD-20251101-0015', 117000.00, 'paid', 'completed', '2025-11-01 05:00:00', NULL, '2025-11-01 05:05:00', '2025-11-01 05:10:00', '2025-11-01 05:20:00', '2025-11-01 05:25:00', NULL),
(16, 6, 'ORD-20251101-0016', 247000.00, 'paid', 'completed', '2025-11-01 06:00:00', NULL, '2025-11-01 06:05:00', '2025-11-01 06:10:00', '2025-11-01 06:20:00', '2025-11-01 06:25:00', NULL),
(17, 19, 'ORD-20251101-0017', 120000.00, 'paid', 'completed', '2025-11-01 07:00:00', NULL, '2025-11-01 07:05:00', '2025-11-01 07:10:00', '2025-11-01 07:20:00', '2025-11-01 07:25:00', NULL),
(18, 23, 'ORD-20251101-0018', 144000.00, 'paid', 'completed', '2025-11-01 08:00:00', NULL, '2025-11-01 08:05:00', '2025-11-01 08:10:00', '2025-11-01 08:20:00', '2025-11-01 08:25:00', NULL),
(19, 16, 'ORD-20251102-0019', 280000.00, 'paid', 'completed', '2025-11-02 03:00:00', NULL, '2025-11-02 03:05:00', '2025-11-02 03:10:00', '2025-11-02 03:20:00', '2025-11-02 03:25:00', NULL),
(20, 7, 'ORD-20251102-0020', 224000.00, 'paid', 'completed', '2025-11-02 04:00:00', NULL, '2025-11-02 04:05:00', '2025-11-02 04:10:00', '2025-11-02 04:20:00', '2025-11-02 04:25:00', NULL),
(21, 25, 'ORD-20251102-0021', 108000.00, 'paid', 'completed', '2025-11-02 05:00:00', NULL, '2025-11-02 05:05:00', '2025-11-02 05:10:00', '2025-11-02 05:20:00', '2025-11-02 05:25:00', NULL),
(22, 20, 'ORD-20251102-0022', 208000.00, 'paid', 'completed', '2025-11-02 06:00:00', NULL, '2025-11-02 06:05:00', '2025-11-02 06:10:00', '2025-11-02 06:20:00', '2025-11-02 06:25:00', NULL),
(23, 11, 'ORD-20251102-0023', 156000.00, 'paid', 'completed', '2025-11-02 07:00:00', NULL, '2025-11-02 07:05:00', '2025-11-02 07:10:00', '2025-11-02 07:20:00', '2025-11-02 07:25:00', NULL),
(24, 19, 'ORD-20251102-0024', 176000.00, 'paid', 'completed', '2025-11-02 08:00:00', NULL, '2025-11-02 08:05:00', '2025-11-02 08:10:00', '2025-11-02 08:20:00', '2025-11-02 08:25:00', NULL),
(25, 7, 'ORD-20251103-0025', 340000.00, 'paid', 'completed', '2025-11-03 03:00:00', NULL, '2025-11-03 03:05:00', '2025-11-03 03:10:00', '2025-11-03 03:20:00', '2025-11-03 03:25:00', NULL),
(26, 16, 'ORD-20251103-0026', 210000.00, 'paid', 'completed', '2025-11-03 04:00:00', NULL, '2025-11-03 04:05:00', '2025-11-03 04:10:00', '2025-11-03 04:20:00', '2025-11-03 04:25:00', NULL),
(27, 9, 'ORD-20251103-0027', 135000.00, 'paid', 'completed', '2025-11-03 05:00:00', NULL, '2025-11-03 05:05:00', '2025-11-03 05:10:00', '2025-11-03 05:20:00', '2025-11-03 05:25:00', NULL),
(28, 19, 'ORD-20251103-0028', 286000.00, 'paid', 'completed', '2025-11-03 06:00:00', NULL, '2025-11-03 06:05:00', '2025-11-03 06:10:00', '2025-11-03 06:20:00', '2025-11-03 06:25:00', NULL),
(29, 2, 'ORD-20251103-0029', 144000.00, 'paid', 'completed', '2025-11-03 07:00:00', NULL, '2025-11-03 07:05:00', '2025-11-03 07:10:00', '2025-11-03 07:20:00', '2025-11-03 07:25:00', NULL),
(30, 20, 'ORD-20251103-0030', 160000.00, 'paid', 'completed', '2025-11-03 08:00:00', NULL, '2025-11-03 08:05:00', '2025-11-03 08:10:00', '2025-11-03 08:20:00', '2025-11-03 08:25:00', NULL),
(31, 2, 'ORD-20251104-0031', 300000.00, 'paid', 'completed', '2025-11-04 03:00:00', NULL, '2025-11-04 03:05:00', '2025-11-04 03:10:00', '2025-11-04 03:20:00', '2025-11-04 03:25:00', NULL),
(32, 21, 'ORD-20251104-0032', 280000.00, 'paid', 'completed', '2025-11-04 04:00:00', NULL, '2025-11-04 04:05:00', '2025-11-04 04:10:00', '2025-11-04 04:20:00', '2025-11-04 04:25:00', NULL),
(33, 23, 'ORD-20251104-0033', 135000.00, 'paid', 'completed', '2025-11-04 05:00:00', NULL, '2025-11-04 05:05:00', '2025-11-04 05:10:00', '2025-11-04 05:20:00', '2025-11-04 05:25:00', NULL),
(34, 13, 'ORD-20251104-0034', 273000.00, 'paid', 'completed', '2025-11-04 06:00:00', NULL, '2025-11-04 06:05:00', '2025-11-04 06:10:00', '2025-11-04 06:20:00', '2025-11-04 06:25:00', NULL),
(35, 18, 'ORD-20251104-0035', 168000.00, 'paid', 'completed', '2025-11-04 07:00:00', NULL, '2025-11-04 07:05:00', '2025-11-04 07:10:00', '2025-11-04 07:20:00', '2025-11-04 07:25:00', NULL),
(36, 15, 'ORD-20251104-0036', 192000.00, 'paid', 'completed', '2025-11-04 08:00:00', NULL, '2025-11-04 08:05:00', '2025-11-04 08:10:00', '2025-11-04 08:20:00', '2025-11-04 08:25:00', NULL),
(37, 23, 'ORD-20251105-0037', 340000.00, 'paid', 'completed', '2025-11-05 03:00:00', NULL, '2025-11-05 03:05:00', '2025-11-05 03:10:00', '2025-11-05 03:20:00', '2025-11-05 03:25:00', NULL),
(38, 12, 'ORD-20251105-0038', 266000.00, 'paid', 'completed', '2025-11-05 04:00:00', NULL, '2025-11-05 04:05:00', '2025-11-05 04:10:00', '2025-11-05 04:20:00', '2025-11-05 04:25:00', NULL),
(39, 23, 'ORD-20251105-0039', 153000.00, 'paid', 'completed', '2025-11-05 05:00:00', NULL, '2025-11-05 05:05:00', '2025-11-05 05:10:00', '2025-11-05 05:20:00', '2025-11-05 05:25:00', NULL),
(40, 3, 'ORD-20251105-0040', 273000.00, 'paid', 'completed', '2025-11-05 06:00:00', NULL, '2025-11-05 06:05:00', '2025-11-05 06:10:00', '2025-11-05 06:20:00', '2025-11-05 06:25:00', NULL),
(41, 22, 'ORD-20251105-0041', 144000.00, 'paid', 'completed', '2025-11-05 07:00:00', NULL, '2025-11-05 07:05:00', '2025-11-05 07:10:00', '2025-11-05 07:20:00', '2025-11-05 07:25:00', NULL),
(42, 10, 'ORD-20251105-0042', 144000.00, 'paid', 'completed', '2025-11-05 08:00:00', NULL, '2025-11-05 08:05:00', '2025-11-05 08:10:00', '2025-11-05 08:20:00', '2025-11-05 08:25:00', NULL),
(43, 12, 'ORD-20251106-0043', 290000.00, 'paid', 'completed', '2025-11-06 03:00:00', NULL, '2025-11-06 03:05:00', '2025-11-06 03:10:00', '2025-11-06 03:20:00', '2025-11-06 03:25:00', NULL),
(44, 24, 'ORD-20251106-0044', 238000.00, 'paid', 'completed', '2025-11-06 04:00:00', NULL, '2025-11-06 04:05:00', '2025-11-06 04:10:00', '2025-11-06 04:20:00', '2025-11-06 04:25:00', NULL),
(45, 23, 'ORD-20251106-0045', 126000.00, 'paid', 'completed', '2025-11-06 05:00:00', NULL, '2025-11-06 05:05:00', '2025-11-06 05:10:00', '2025-11-06 05:20:00', '2025-11-06 05:25:00', NULL),
(46, 4, 'ORD-20251106-0046', 273000.00, 'paid', 'completed', '2025-11-06 06:00:00', NULL, '2025-11-06 06:05:00', '2025-11-06 06:10:00', '2025-11-06 06:20:00', '2025-11-06 06:25:00', NULL),
(47, 13, 'ORD-20251106-0047', 156000.00, 'paid', 'completed', '2025-11-06 07:00:00', NULL, '2025-11-06 07:05:00', '2025-11-06 07:10:00', '2025-11-06 07:20:00', '2025-11-06 07:25:00', NULL),
(48, 21, 'ORD-20251106-0048', 128000.00, 'paid', 'completed', '2025-11-06 08:00:00', NULL, '2025-11-06 08:05:00', '2025-11-06 08:10:00', '2025-11-06 08:20:00', '2025-11-06 08:25:00', NULL),
(49, 5, 'ORD-20251107-0049', 380000.00, 'paid', 'completed', '2025-11-07 03:00:00', NULL, '2025-11-07 03:05:00', '2025-11-07 03:10:00', '2025-11-07 03:20:00', '2025-11-07 03:25:00', NULL),
(50, 9, 'ORD-20251107-0050', 224000.00, 'paid', 'completed', '2025-11-07 04:00:00', NULL, '2025-11-07 04:05:00', '2025-11-07 04:10:00', '2025-11-07 04:20:00', '2025-11-07 04:25:00', NULL),
(51, 10, 'ORD-20251107-0051', 153000.00, 'paid', 'completed', '2025-11-07 05:00:00', NULL, '2025-11-07 05:05:00', '2025-11-07 05:10:00', '2025-11-07 05:20:00', '2025-11-07 05:25:00', NULL),
(52, 13, 'ORD-20251107-0052', 234000.00, 'paid', 'completed', '2025-11-07 06:00:00', NULL, '2025-11-07 06:05:00', '2025-11-07 06:10:00', '2025-11-07 06:20:00', '2025-11-07 06:25:00', NULL),
(53, 7, 'ORD-20251107-0053', 168000.00, 'paid', 'completed', '2025-11-07 07:00:00', NULL, '2025-11-07 07:05:00', '2025-11-07 07:10:00', '2025-11-07 07:20:00', '2025-11-07 07:25:00', NULL),
(54, 11, 'ORD-20251107-0054', 176000.00, 'paid', 'completed', '2025-11-07 08:00:00', NULL, '2025-11-07 08:05:00', '2025-11-07 08:10:00', '2025-11-07 08:20:00', '2025-11-07 08:25:00', NULL),
(55, 10, 'ORD-20251108-0055', 290000.00, 'paid', 'completed', '2025-11-08 03:00:00', NULL, '2025-11-08 03:05:00', '2025-11-08 03:10:00', '2025-11-08 03:20:00', '2025-11-08 03:25:00', NULL),
(56, 21, 'ORD-20251108-0056', 252000.00, 'paid', 'completed', '2025-11-08 04:00:00', NULL, '2025-11-08 04:05:00', '2025-11-08 04:10:00', '2025-11-08 04:20:00', '2025-11-08 04:25:00', NULL),
(57, 8, 'ORD-20251108-0057', 153000.00, 'paid', 'completed', '2025-11-08 05:00:00', NULL, '2025-11-08 05:05:00', '2025-11-08 05:10:00', '2025-11-08 05:20:00', '2025-11-08 05:25:00', NULL),
(58, 20, 'ORD-20251108-0058', 247000.00, 'paid', 'completed', '2025-11-08 06:00:00', NULL, '2025-11-08 06:05:00', '2025-11-08 06:10:00', '2025-11-08 06:20:00', '2025-11-08 06:25:00', NULL),
(59, 25, 'ORD-20251108-0059', 120000.00, 'paid', 'completed', '2025-11-08 07:00:00', NULL, '2025-11-08 07:05:00', '2025-11-08 07:10:00', '2025-11-08 07:20:00', '2025-11-08 07:25:00', NULL),
(60, 24, 'ORD-20251108-0060', 144000.00, 'paid', 'completed', '2025-11-08 08:00:00', NULL, '2025-11-08 08:05:00', '2025-11-08 08:10:00', '2025-11-08 08:20:00', '2025-11-08 08:25:00', NULL),
(61, 4, 'ORD-20251109-0061', 350000.00, 'paid', 'completed', '2025-11-09 03:00:00', NULL, '2025-11-09 03:05:00', '2025-11-09 03:10:00', '2025-11-09 03:20:00', '2025-11-09 03:25:00', NULL),
(62, 23, 'ORD-20251109-0062', 266000.00, 'paid', 'completed', '2025-11-09 04:00:00', NULL, '2025-11-09 04:05:00', '2025-11-09 04:10:00', '2025-11-09 04:20:00', '2025-11-09 04:25:00', NULL),
(63, 2, 'ORD-20251109-0063', 126000.00, 'paid', 'completed', '2025-11-09 05:00:00', NULL, '2025-11-09 05:05:00', '2025-11-09 05:10:00', '2025-11-09 05:20:00', '2025-11-09 05:25:00', NULL),
(64, 17, 'ORD-20251109-0064', 273000.00, 'paid', 'completed', '2025-11-09 06:00:00', NULL, '2025-11-09 06:05:00', '2025-11-09 06:10:00', '2025-11-09 06:20:00', '2025-11-09 06:25:00', NULL),
(65, 21, 'ORD-20251109-0065', 156000.00, 'paid', 'completed', '2025-11-09 07:00:00', NULL, '2025-11-09 07:05:00', '2025-11-09 07:10:00', '2025-11-09 07:20:00', '2025-11-09 07:25:00', NULL),
(66, 14, 'ORD-20251109-0066', 144000.00, 'paid', 'completed', '2025-11-09 08:00:00', NULL, '2025-11-09 08:05:00', '2025-11-09 08:10:00', '2025-11-09 08:20:00', '2025-11-09 08:25:00', NULL),
(67, 13, 'ORD-20251110-0067', 350000.00, 'paid', 'completed', '2025-11-10 03:00:00', NULL, '2025-11-10 03:05:00', '2025-11-10 03:10:00', '2025-11-10 03:20:00', '2025-11-10 03:25:00', NULL),
(68, 4, 'ORD-20251110-0068', 280000.00, 'paid', 'completed', '2025-11-10 04:00:00', NULL, '2025-11-10 04:05:00', '2025-11-10 04:10:00', '2025-11-10 04:20:00', '2025-11-10 04:25:00', NULL),
(69, 14, 'ORD-20251110-0069', 117000.00, 'paid', 'completed', '2025-11-10 05:00:00', NULL, '2025-11-10 05:05:00', '2025-11-10 05:10:00', '2025-11-10 05:20:00', '2025-11-10 05:25:00', NULL),
(70, 18, 'ORD-20251110-0070', 208000.00, 'paid', 'completed', '2025-11-10 06:00:00', NULL, '2025-11-10 06:05:00', '2025-11-10 06:10:00', '2025-11-10 06:20:00', '2025-11-10 06:25:00', NULL),
(71, 9, 'ORD-20251110-0071', 144000.00, 'paid', 'completed', '2025-11-10 07:00:00', NULL, '2025-11-10 07:05:00', '2025-11-10 07:10:00', '2025-11-10 07:20:00', '2025-11-10 07:25:00', NULL),
(72, 16, 'ORD-20251110-0072', 144000.00, 'paid', 'completed', '2025-11-10 08:00:00', NULL, '2025-11-10 08:05:00', '2025-11-10 08:10:00', '2025-11-10 08:20:00', '2025-11-10 08:25:00', NULL),
(73, 11, 'ORD-20251111-0073', 380000.00, 'paid', 'completed', '2025-11-11 03:00:00', NULL, '2025-11-11 03:05:00', '2025-11-11 03:10:00', '2025-11-11 03:20:00', '2025-11-11 03:25:00', NULL),
(74, 15, 'ORD-20251111-0074', 280000.00, 'paid', 'completed', '2025-11-11 04:00:00', NULL, '2025-11-11 04:05:00', '2025-11-11 04:10:00', '2025-11-11 04:20:00', '2025-11-11 04:25:00', NULL),
(75, 9, 'ORD-20251111-0075', 108000.00, 'paid', 'completed', '2025-11-11 05:00:00', NULL, '2025-11-11 05:05:00', '2025-11-11 05:10:00', '2025-11-11 05:20:00', '2025-11-11 05:25:00', NULL),
(76, 25, 'ORD-20251111-0076', 260000.00, 'paid', 'completed', '2025-11-11 06:00:00', NULL, '2025-11-11 06:05:00', '2025-11-11 06:10:00', '2025-11-11 06:20:00', '2025-11-11 06:25:00', NULL),
(77, 3, 'ORD-20251111-0077', 168000.00, 'paid', 'completed', '2025-11-11 07:00:00', NULL, '2025-11-11 07:05:00', '2025-11-11 07:10:00', '2025-11-11 07:20:00', '2025-11-11 07:25:00', NULL),
(78, 16, 'ORD-20251111-0078', 144000.00, 'paid', 'completed', '2025-11-11 08:00:00', NULL, '2025-11-11 08:05:00', '2025-11-11 08:10:00', '2025-11-11 08:20:00', '2025-11-11 08:25:00', NULL),
(79, 24, 'ORD-20251112-0079', 400000.00, 'paid', 'completed', '2025-11-12 03:00:00', NULL, '2025-11-12 03:05:00', '2025-11-12 03:10:00', '2025-11-12 03:20:00', '2025-11-12 03:25:00', NULL),
(80, 15, 'ORD-20251112-0080', 196000.00, 'paid', 'completed', '2025-11-12 04:00:00', NULL, '2025-11-12 04:05:00', '2025-11-12 04:10:00', '2025-11-12 04:20:00', '2025-11-12 04:25:00', NULL),
(81, 16, 'ORD-20251112-0081', 144000.00, 'paid', 'completed', '2025-11-12 05:00:00', NULL, '2025-11-12 05:05:00', '2025-11-12 05:10:00', '2025-11-12 05:20:00', '2025-11-12 05:25:00', NULL),
(82, 18, 'ORD-20251112-0082', 247000.00, 'paid', 'completed', '2025-11-12 06:00:00', NULL, '2025-11-12 06:05:00', '2025-11-12 06:10:00', '2025-11-12 06:20:00', '2025-11-12 06:25:00', NULL),
(83, 9, 'ORD-20251112-0083', 120000.00, 'paid', 'completed', '2025-11-12 07:00:00', NULL, '2025-11-12 07:05:00', '2025-11-12 07:10:00', '2025-11-12 07:20:00', '2025-11-12 07:25:00', NULL),
(84, 4, 'ORD-20251112-0084', 160000.00, 'paid', 'completed', '2025-11-12 08:00:00', NULL, '2025-11-12 08:05:00', '2025-11-12 08:10:00', '2025-11-12 08:20:00', '2025-11-12 08:25:00', NULL),
(85, 21, 'ORD-20251113-0085', 340000.00, 'paid', 'completed', '2025-11-13 03:00:00', NULL, '2025-11-13 03:05:00', '2025-11-13 03:10:00', '2025-11-13 03:20:00', '2025-11-13 03:25:00', NULL),
(86, 15, 'ORD-20251113-0086', 252000.00, 'paid', 'completed', '2025-11-13 04:00:00', NULL, '2025-11-13 04:05:00', '2025-11-13 04:10:00', '2025-11-13 04:20:00', '2025-11-13 04:25:00', NULL),
(87, 13, 'ORD-20251113-0087', 108000.00, 'paid', 'completed', '2025-11-13 05:00:00', NULL, '2025-11-13 05:05:00', '2025-11-13 05:10:00', '2025-11-13 05:20:00', '2025-11-13 05:25:00', NULL),
(88, 8, 'ORD-20251113-0088', 286000.00, 'paid', 'completed', '2025-11-13 06:00:00', NULL, '2025-11-13 06:05:00', '2025-11-13 06:10:00', '2025-11-13 06:20:00', '2025-11-13 06:25:00', NULL),
(89, 8, 'ORD-20251113-0089', 144000.00, 'paid', 'completed', '2025-11-13 07:00:00', NULL, '2025-11-13 07:05:00', '2025-11-13 07:10:00', '2025-11-13 07:20:00', '2025-11-13 07:25:00', NULL),
(90, 24, 'ORD-20251113-0090', 192000.00, 'paid', 'completed', '2025-11-13 08:00:00', NULL, '2025-11-13 08:05:00', '2025-11-13 08:10:00', '2025-11-13 08:20:00', '2025-11-13 08:25:00', NULL),
(91, 12, 'ORD-20251114-0091', 360000.00, 'paid', 'completed', '2025-11-14 03:00:00', NULL, '2025-11-14 03:05:00', '2025-11-14 03:10:00', '2025-11-14 03:20:00', '2025-11-14 03:25:00', NULL),
(92, 19, 'ORD-20251114-0092', 224000.00, 'paid', 'completed', '2025-11-14 04:00:00', NULL, '2025-11-14 04:05:00', '2025-11-14 04:10:00', '2025-11-14 04:20:00', '2025-11-14 04:25:00', NULL),
(93, 3, 'ORD-20251114-0093', 162000.00, 'paid', 'completed', '2025-11-14 05:00:00', NULL, '2025-11-14 05:05:00', '2025-11-14 05:10:00', '2025-11-14 05:20:00', '2025-11-14 05:25:00', NULL),
(94, 23, 'ORD-20251114-0094', 260000.00, 'paid', 'completed', '2025-11-14 06:00:00', NULL, '2025-11-14 06:05:00', '2025-11-14 06:10:00', '2025-11-14 06:20:00', '2025-11-14 06:25:00', NULL),
(95, 11, 'ORD-20251114-0095', 132000.00, 'paid', 'completed', '2025-11-14 07:00:00', NULL, '2025-11-14 07:05:00', '2025-11-14 07:10:00', '2025-11-14 07:20:00', '2025-11-14 07:25:00', NULL),
(96, 16, 'ORD-20251114-0096', 128000.00, 'paid', 'completed', '2025-11-14 08:00:00', NULL, '2025-11-14 08:05:00', '2025-11-14 08:10:00', '2025-11-14 08:20:00', '2025-11-14 08:25:00', NULL),
(97, 5, 'ORD-20251115-0097', 280000.00, 'paid', 'completed', '2025-11-15 03:00:00', NULL, '2025-11-15 03:05:00', '2025-11-15 03:10:00', '2025-11-15 03:20:00', '2025-11-15 03:25:00', NULL),
(98, 12, 'ORD-20251115-0098', 224000.00, 'paid', 'completed', '2025-11-15 04:00:00', NULL, '2025-11-15 04:05:00', '2025-11-15 04:10:00', '2025-11-15 04:20:00', '2025-11-15 04:25:00', NULL),
(99, 12, 'ORD-20251115-0099', 108000.00, 'paid', 'completed', '2025-11-15 05:00:00', NULL, '2025-11-15 05:05:00', '2025-11-15 05:10:00', '2025-11-15 05:20:00', '2025-11-15 05:25:00', NULL),
(100, 20, 'ORD-20251115-0100', 221000.00, 'paid', 'completed', '2025-11-15 06:00:00', NULL, '2025-11-15 06:05:00', '2025-11-15 06:10:00', '2025-11-15 06:20:00', '2025-11-15 06:25:00', NULL),
(101, 5, 'ORD-20251115-0101', 120000.00, 'paid', 'completed', '2025-11-15 07:00:00', NULL, '2025-11-15 07:05:00', '2025-11-15 07:10:00', '2025-11-15 07:20:00', '2025-11-15 07:25:00', NULL),
(102, 5, 'ORD-20251115-0102', 128000.00, 'paid', 'completed', '2025-11-15 08:00:00', NULL, '2025-11-15 08:05:00', '2025-11-15 08:10:00', '2025-11-15 08:20:00', '2025-11-15 08:25:00', NULL),
(103, 16, 'ORD-20251116-0103', 380000.00, 'paid', 'completed', '2025-11-16 03:00:00', NULL, '2025-11-16 03:05:00', '2025-11-16 03:10:00', '2025-11-16 03:20:00', '2025-11-16 03:25:00', NULL),
(104, 4, 'ORD-20251116-0104', 252000.00, 'paid', 'completed', '2025-11-16 04:00:00', NULL, '2025-11-16 04:05:00', '2025-11-16 04:10:00', '2025-11-16 04:20:00', '2025-11-16 04:25:00', NULL),
(105, 8, 'ORD-20251116-0105', 117000.00, 'paid', 'completed', '2025-11-16 05:00:00', NULL, '2025-11-16 05:05:00', '2025-11-16 05:10:00', '2025-11-16 05:20:00', '2025-11-16 05:25:00', NULL),
(106, 19, 'ORD-20251116-0106', 208000.00, 'paid', 'completed', '2025-11-16 06:00:00', NULL, '2025-11-16 06:05:00', '2025-11-16 06:10:00', '2025-11-16 06:20:00', '2025-11-16 06:25:00', NULL),
(107, 4, 'ORD-20251116-0107', 120000.00, 'paid', 'completed', '2025-11-16 07:00:00', NULL, '2025-11-16 07:05:00', '2025-11-16 07:10:00', '2025-11-16 07:20:00', '2025-11-16 07:25:00', NULL),
(108, 16, 'ORD-20251116-0108', 144000.00, 'paid', 'completed', '2025-11-16 08:00:00', NULL, '2025-11-16 08:05:00', '2025-11-16 08:10:00', '2025-11-16 08:20:00', '2025-11-16 08:25:00', NULL),
(109, 12, 'ORD-20251117-0109', 300000.00, 'paid', 'completed', '2025-11-17 03:00:00', NULL, '2025-11-17 03:05:00', '2025-11-17 03:10:00', '2025-11-17 03:20:00', '2025-11-17 03:25:00', NULL),
(110, 16, 'ORD-20251117-0110', 210000.00, 'paid', 'completed', '2025-11-17 04:00:00', NULL, '2025-11-17 04:05:00', '2025-11-17 04:10:00', '2025-11-17 04:20:00', '2025-11-17 04:25:00', NULL),
(111, 9, 'ORD-20251117-0111', 144000.00, 'paid', 'completed', '2025-11-17 05:00:00', NULL, '2025-11-17 05:05:00', '2025-11-17 05:10:00', '2025-11-17 05:20:00', '2025-11-17 05:25:00', NULL),
(112, 4, 'ORD-20251117-0112', 208000.00, 'paid', 'completed', '2025-11-17 06:00:00', NULL, '2025-11-17 06:05:00', '2025-11-17 06:10:00', '2025-11-17 06:20:00', '2025-11-17 06:25:00', NULL),
(113, 18, 'ORD-20251117-0113', 168000.00, 'paid', 'completed', '2025-11-17 07:00:00', NULL, '2025-11-17 07:05:00', '2025-11-17 07:10:00', '2025-11-17 07:20:00', '2025-11-17 07:25:00', NULL),
(114, 11, 'ORD-20251117-0114', 176000.00, 'paid', 'completed', '2025-11-17 08:00:00', NULL, '2025-11-17 08:05:00', '2025-11-17 08:10:00', '2025-11-17 08:20:00', '2025-11-17 08:25:00', NULL),
(115, 17, 'ORD-20251118-0115', 350000.00, 'paid', 'completed', '2025-11-18 03:00:00', NULL, '2025-11-18 03:05:00', '2025-11-18 03:10:00', '2025-11-18 03:20:00', '2025-11-18 03:25:00', NULL),
(116, 22, 'ORD-20251118-0116', 238000.00, 'paid', 'completed', '2025-11-18 04:00:00', NULL, '2025-11-18 04:05:00', '2025-11-18 04:10:00', '2025-11-18 04:20:00', '2025-11-18 04:25:00', NULL),
(117, 14, 'ORD-20251118-0117', 144000.00, 'paid', 'completed', '2025-11-18 05:00:00', NULL, '2025-11-18 05:05:00', '2025-11-18 05:10:00', '2025-11-18 05:20:00', '2025-11-18 05:25:00', NULL),
(118, 9, 'ORD-20251118-0118', 273000.00, 'paid', 'completed', '2025-11-18 06:00:00', NULL, '2025-11-18 06:05:00', '2025-11-18 06:10:00', '2025-11-18 06:20:00', '2025-11-18 06:25:00', NULL),
(119, 6, 'ORD-20251118-0119', 144000.00, 'paid', 'completed', '2025-11-18 07:00:00', NULL, '2025-11-18 07:05:00', '2025-11-18 07:10:00', '2025-11-18 07:20:00', '2025-11-18 07:25:00', NULL),
(120, 11, 'ORD-20251118-0120', 144000.00, 'paid', 'completed', '2025-11-18 08:00:00', NULL, '2025-11-18 08:05:00', '2025-11-18 08:10:00', '2025-11-18 08:20:00', '2025-11-18 08:25:00', NULL),
(121, 11, 'ORD-20251119-0121', 310000.00, 'paid', 'completed', '2025-11-19 03:00:00', NULL, '2025-11-19 03:05:00', '2025-11-19 03:10:00', '2025-11-19 03:20:00', '2025-11-19 03:25:00', NULL),
(122, 18, 'ORD-20251119-0122', 266000.00, 'paid', 'completed', '2025-11-19 04:00:00', NULL, '2025-11-19 04:05:00', '2025-11-19 04:10:00', '2025-11-19 04:20:00', '2025-11-19 04:25:00', NULL),
(123, 10, 'ORD-20251119-0123', 126000.00, 'paid', 'completed', '2025-11-19 05:00:00', NULL, '2025-11-19 05:05:00', '2025-11-19 05:10:00', '2025-11-19 05:20:00', '2025-11-19 05:25:00', NULL),
(124, 5, 'ORD-20251119-0124', 273000.00, 'paid', 'completed', '2025-11-19 06:00:00', NULL, '2025-11-19 06:05:00', '2025-11-19 06:10:00', '2025-11-19 06:20:00', '2025-11-19 06:25:00', NULL),
(125, 7, 'ORD-20251119-0125', 156000.00, 'paid', 'completed', '2025-11-19 07:00:00', NULL, '2025-11-19 07:05:00', '2025-11-19 07:10:00', '2025-11-19 07:20:00', '2025-11-19 07:25:00', NULL),
(126, 12, 'ORD-20251119-0126', 160000.00, 'paid', 'completed', '2025-11-19 08:00:00', NULL, '2025-11-19 08:05:00', '2025-11-19 08:10:00', '2025-11-19 08:20:00', '2025-11-19 08:25:00', NULL),
(127, 3, 'ORD-20251120-0127', 350000.00, 'paid', 'completed', '2025-11-20 03:00:00', NULL, '2025-11-20 03:05:00', '2025-11-20 03:10:00', '2025-11-20 03:20:00', '2025-11-20 03:25:00', NULL),
(128, 9, 'ORD-20251120-0128', 224000.00, 'paid', 'completed', '2025-11-20 04:00:00', NULL, '2025-11-20 04:05:00', '2025-11-20 04:10:00', '2025-11-20 04:20:00', '2025-11-20 04:25:00', NULL),
(129, 11, 'ORD-20251120-0129', 135000.00, 'paid', 'completed', '2025-11-20 05:00:00', NULL, '2025-11-20 05:05:00', '2025-11-20 05:10:00', '2025-11-20 05:20:00', '2025-11-20 05:25:00', NULL),
(130, 5, 'ORD-20251120-0130', 247000.00, 'paid', 'completed', '2025-11-20 06:00:00', NULL, '2025-11-20 06:05:00', '2025-11-20 06:10:00', '2025-11-20 06:20:00', '2025-11-20 06:25:00', NULL),
(131, 13, 'ORD-20251120-0131', 156000.00, 'paid', 'completed', '2025-11-20 07:00:00', NULL, '2025-11-20 07:05:00', '2025-11-20 07:10:00', '2025-11-20 07:20:00', '2025-11-20 07:25:00', NULL),
(132, 23, 'ORD-20251120-0132', 128000.00, 'paid', 'completed', '2025-11-20 08:00:00', NULL, '2025-11-20 08:05:00', '2025-11-20 08:10:00', '2025-11-20 08:20:00', '2025-11-20 08:25:00', NULL),
(133, 21, 'ORD-20251121-0133', 390000.00, 'paid', 'completed', '2025-11-21 03:00:00', NULL, '2025-11-21 03:05:00', '2025-11-21 03:10:00', '2025-11-21 03:20:00', '2025-11-21 03:25:00', NULL),
(134, 16, 'ORD-20251121-0134', 266000.00, 'paid', 'completed', '2025-11-21 04:00:00', NULL, '2025-11-21 04:05:00', '2025-11-21 04:10:00', '2025-11-21 04:20:00', '2025-11-21 04:25:00', NULL),
(135, 12, 'ORD-20251121-0135', 162000.00, 'paid', 'completed', '2025-11-21 05:00:00', NULL, '2025-11-21 05:05:00', '2025-11-21 05:10:00', '2025-11-21 05:20:00', '2025-11-21 05:25:00', NULL),
(136, 7, 'ORD-20251121-0136', 260000.00, 'paid', 'completed', '2025-11-21 06:00:00', NULL, '2025-11-21 06:05:00', '2025-11-21 06:10:00', '2025-11-21 06:20:00', '2025-11-21 06:25:00', NULL),
(137, 20, 'ORD-20251121-0137', 120000.00, 'paid', 'completed', '2025-11-21 07:00:00', NULL, '2025-11-21 07:05:00', '2025-11-21 07:10:00', '2025-11-21 07:20:00', '2025-11-21 07:25:00', NULL),
(138, 5, 'ORD-20251121-0138', 128000.00, 'paid', 'completed', '2025-11-21 08:00:00', NULL, '2025-11-21 08:05:00', '2025-11-21 08:10:00', '2025-11-21 08:20:00', '2025-11-21 08:25:00', NULL),
(139, 16, 'ORD-20251122-0139', 360000.00, 'paid', 'completed', '2025-11-22 03:00:00', NULL, '2025-11-22 03:05:00', '2025-11-22 03:10:00', '2025-11-22 03:20:00', '2025-11-22 03:25:00', NULL),
(140, 19, 'ORD-20251122-0140', 280000.00, 'paid', 'completed', '2025-11-22 04:00:00', NULL, '2025-11-22 04:05:00', '2025-11-22 04:10:00', '2025-11-22 04:20:00', '2025-11-22 04:25:00', NULL),
(141, 20, 'ORD-20251122-0141', 153000.00, 'paid', 'completed', '2025-11-22 05:00:00', NULL, '2025-11-22 05:05:00', '2025-11-22 05:10:00', '2025-11-22 05:20:00', '2025-11-22 05:25:00', NULL),
(142, 5, 'ORD-20251122-0142', 273000.00, 'paid', 'completed', '2025-11-22 06:00:00', NULL, '2025-11-22 06:05:00', '2025-11-22 06:10:00', '2025-11-22 06:20:00', '2025-11-22 06:25:00', NULL),
(143, 14, 'ORD-20251122-0143', 168000.00, 'paid', 'completed', '2025-11-22 07:00:00', NULL, '2025-11-22 07:05:00', '2025-11-22 07:10:00', '2025-11-22 07:20:00', '2025-11-22 07:25:00', NULL),
(144, 16, 'ORD-20251122-0144', 176000.00, 'paid', 'completed', '2025-11-22 08:00:00', NULL, '2025-11-22 08:05:00', '2025-11-22 08:10:00', '2025-11-22 08:20:00', '2025-11-22 08:25:00', NULL),
(145, 17, 'ORD-20251123-0145', 380000.00, 'paid', 'completed', '2025-11-23 03:00:00', NULL, '2025-11-23 03:05:00', '2025-11-23 03:10:00', '2025-11-23 03:20:00', '2025-11-23 03:25:00', NULL),
(146, 8, 'ORD-20251123-0146', 210000.00, 'paid', 'completed', '2025-11-23 04:00:00', NULL, '2025-11-23 04:05:00', '2025-11-23 04:10:00', '2025-11-23 04:20:00', '2025-11-23 04:25:00', NULL),
(147, 4, 'ORD-20251123-0147', 117000.00, 'paid', 'completed', '2025-11-23 05:00:00', NULL, '2025-11-23 05:05:00', '2025-11-23 05:10:00', '2025-11-23 05:20:00', '2025-11-23 05:25:00', NULL),
(148, 23, 'ORD-20251123-0148', 234000.00, 'paid', 'completed', '2025-11-23 06:00:00', NULL, '2025-11-23 06:05:00', '2025-11-23 06:10:00', '2025-11-23 06:20:00', '2025-11-23 06:25:00', NULL),
(149, 15, 'ORD-20251123-0149', 168000.00, 'paid', 'completed', '2025-11-23 07:00:00', NULL, '2025-11-23 07:05:00', '2025-11-23 07:10:00', '2025-11-23 07:20:00', '2025-11-23 07:25:00', NULL),
(150, 13, 'ORD-20251123-0150', 144000.00, 'paid', 'completed', '2025-11-23 08:00:00', NULL, '2025-11-23 08:05:00', '2025-11-23 08:10:00', '2025-11-23 08:20:00', '2025-11-23 08:25:00', NULL),
(151, 25, 'ORD-20251124-0151', 280000.00, 'paid', 'completed', '2025-11-24 03:00:00', NULL, '2025-11-24 03:05:00', '2025-11-24 03:10:00', '2025-11-24 03:20:00', '2025-11-24 03:25:00', NULL),
(152, 8, 'ORD-20251124-0152', 266000.00, 'paid', 'completed', '2025-11-24 04:00:00', NULL, '2025-11-24 04:05:00', '2025-11-24 04:10:00', '2025-11-24 04:20:00', '2025-11-24 04:25:00', NULL),
(153, 5, 'ORD-20251124-0153', 153000.00, 'paid', 'completed', '2025-11-24 05:00:00', NULL, '2025-11-24 05:05:00', '2025-11-24 05:10:00', '2025-11-24 05:20:00', '2025-11-24 05:25:00', NULL),
(154, 5, 'ORD-20251124-0154', 247000.00, 'paid', 'completed', '2025-11-24 06:00:00', NULL, '2025-11-24 06:05:00', '2025-11-24 06:10:00', '2025-11-24 06:20:00', '2025-11-24 06:25:00', NULL),
(155, 25, 'ORD-20251124-0155', 168000.00, 'paid', 'completed', '2025-11-24 07:00:00', NULL, '2025-11-24 07:05:00', '2025-11-24 07:10:00', '2025-11-24 07:20:00', '2025-11-24 07:25:00', NULL),
(156, 13, 'ORD-20251124-0156', 144000.00, 'paid', 'completed', '2025-11-24 08:00:00', NULL, '2025-11-24 08:05:00', '2025-11-24 08:10:00', '2025-11-24 08:20:00', '2025-11-24 08:25:00', NULL),
(157, 17, 'ORD-20251125-0157', 390000.00, 'paid', 'completed', '2025-11-25 03:00:00', NULL, '2025-11-25 03:05:00', '2025-11-25 03:10:00', '2025-11-25 03:20:00', '2025-11-25 03:25:00', NULL),
(158, 11, 'ORD-20251125-0158', 196000.00, 'paid', 'completed', '2025-11-25 04:00:00', NULL, '2025-11-25 04:05:00', '2025-11-25 04:10:00', '2025-11-25 04:20:00', '2025-11-25 04:25:00', NULL),
(159, 19, 'ORD-20251125-0159', 117000.00, 'paid', 'completed', '2025-11-25 05:00:00', NULL, '2025-11-25 05:05:00', '2025-11-25 05:10:00', '2025-11-25 05:20:00', '2025-11-25 05:25:00', NULL),
(160, 13, 'ORD-20251125-0160', 221000.00, 'paid', 'completed', '2025-11-25 06:00:00', NULL, '2025-11-25 06:05:00', '2025-11-25 06:10:00', '2025-11-25 06:20:00', '2025-11-25 06:25:00', NULL),
(161, 23, 'ORD-20251125-0161', 144000.00, 'paid', 'completed', '2025-11-25 07:00:00', NULL, '2025-11-25 07:05:00', '2025-11-25 07:10:00', '2025-11-25 07:20:00', '2025-11-25 07:25:00', NULL),
(162, 18, 'ORD-20251125-0162', 192000.00, 'paid', 'completed', '2025-11-25 08:00:00', NULL, '2025-11-25 08:05:00', '2025-11-25 08:10:00', '2025-11-25 08:20:00', '2025-11-25 08:25:00', NULL),
(163, 6, 'ORD-20251126-0163', 410000.00, 'paid', 'completed', '2025-11-26 03:00:00', NULL, '2025-11-26 03:05:00', '2025-11-26 03:10:00', '2025-11-26 03:20:00', '2025-11-26 03:25:00', NULL),
(164, 12, 'ORD-20251126-0164', 210000.00, 'paid', 'completed', '2025-11-26 04:00:00', NULL, '2025-11-26 04:05:00', '2025-11-26 04:10:00', '2025-11-26 04:20:00', '2025-11-26 04:25:00', NULL),
(165, 22, 'ORD-20251126-0165', 135000.00, 'paid', 'completed', '2025-11-26 05:00:00', NULL, '2025-11-26 05:05:00', '2025-11-26 05:10:00', '2025-11-26 05:20:00', '2025-11-26 05:25:00', NULL),
(166, 15, 'ORD-20251126-0166', 286000.00, 'paid', 'completed', '2025-11-26 06:00:00', NULL, '2025-11-26 06:05:00', '2025-11-26 06:10:00', '2025-11-26 06:20:00', '2025-11-26 06:25:00', NULL),
(167, 17, 'ORD-20251126-0167', 120000.00, 'paid', 'completed', '2025-11-26 07:00:00', NULL, '2025-11-26 07:05:00', '2025-11-26 07:10:00', '2025-11-26 07:20:00', '2025-11-26 07:25:00', NULL),
(168, 4, 'ORD-20251126-0168', 176000.00, 'paid', 'completed', '2025-11-26 08:00:00', NULL, '2025-11-26 08:05:00', '2025-11-26 08:10:00', '2025-11-26 08:20:00', '2025-11-26 08:25:00', NULL),
(169, 16, 'ORD-20251127-0169', 280000.00, 'paid', 'completed', '2025-11-27 03:00:00', NULL, '2025-11-27 03:05:00', '2025-11-27 03:10:00', '2025-11-27 03:20:00', '2025-11-27 03:25:00', NULL),
(170, 8, 'ORD-20251127-0170', 280000.00, 'paid', 'completed', '2025-11-27 04:00:00', NULL, '2025-11-27 04:05:00', '2025-11-27 04:10:00', '2025-11-27 04:20:00', '2025-11-27 04:25:00', NULL),
(171, 4, 'ORD-20251127-0171', 144000.00, 'paid', 'completed', '2025-11-27 05:00:00', NULL, '2025-11-27 05:05:00', '2025-11-27 05:10:00', '2025-11-27 05:20:00', '2025-11-27 05:25:00', NULL),
(172, 20, 'ORD-20251127-0172', 260000.00, 'paid', 'completed', '2025-11-27 06:00:00', NULL, '2025-11-27 06:05:00', '2025-11-27 06:10:00', '2025-11-27 06:20:00', '2025-11-27 06:25:00', NULL),
(173, 6, 'ORD-20251127-0173', 132000.00, 'paid', 'completed', '2025-11-27 07:00:00', NULL, '2025-11-27 07:05:00', '2025-11-27 07:10:00', '2025-11-27 07:20:00', '2025-11-27 07:25:00', NULL),
(174, 23, 'ORD-20251127-0174', 128000.00, 'paid', 'completed', '2025-11-27 08:00:00', NULL, '2025-11-27 08:05:00', '2025-11-27 08:10:00', '2025-11-27 08:20:00', '2025-11-27 08:25:00', NULL),
(175, 18, 'ORD-20251128-0175', 300000.00, 'paid', 'completed', '2025-11-28 03:00:00', NULL, '2025-11-28 03:05:00', '2025-11-28 03:10:00', '2025-11-28 03:20:00', '2025-11-28 03:25:00', NULL),
(176, 18, 'ORD-20251128-0176', 266000.00, 'paid', 'completed', '2025-11-28 04:00:00', NULL, '2025-11-28 04:05:00', '2025-11-28 04:10:00', '2025-11-28 04:20:00', '2025-11-28 04:25:00', NULL),
(177, 9, 'ORD-20251128-0177', 126000.00, 'paid', 'completed', '2025-11-28 05:00:00', NULL, '2025-11-28 05:05:00', '2025-11-28 05:10:00', '2025-11-28 05:20:00', '2025-11-28 05:25:00', NULL),
(178, 17, 'ORD-20251128-0178', 234000.00, 'paid', 'completed', '2025-11-28 06:00:00', NULL, '2025-11-28 06:05:00', '2025-11-28 06:10:00', '2025-11-28 06:20:00', '2025-11-28 06:25:00', NULL),
(179, 12, 'ORD-20251128-0179', 120000.00, 'paid', 'completed', '2025-11-28 07:00:00', NULL, '2025-11-28 07:05:00', '2025-11-28 07:10:00', '2025-11-28 07:20:00', '2025-11-28 07:25:00', NULL),
(180, 25, 'ORD-20251128-0180', 192000.00, 'paid', 'completed', '2025-11-28 08:00:00', NULL, '2025-11-28 08:05:00', '2025-11-28 08:10:00', '2025-11-28 08:20:00', '2025-11-28 08:25:00', NULL),
(181, 15, 'ORD-20251130085812-15', 72000.00, 'pending', 'canceled', '2025-11-30 08:58:12', '2025-12-01 17:09:26', NULL, NULL, NULL, NULL, '2025-12-01 17:09:26'),
(182, 2, 'ORD-20251201074534-2', 27000.00, 'pending', 'created', '2025-12-01 07:45:34', '2025-12-01 07:45:34', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_status_pesanan`
--

CREATE TABLE `riwayat_status_pesanan` (
  `id_riwayat` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `tipe` enum('payment','order') NOT NULL,
  `status_lama` varchar(50) DEFAULT NULL,
  `status_baru` varchar(50) NOT NULL,
  `diubah_oleh` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `riwayat_status_pesanan`
--

INSERT INTO `riwayat_status_pesanan` (`id_riwayat`, `id_pesanan`, `tipe`, `status_lama`, `status_baru`, `diubah_oleh`, `keterangan`, `dibuat_pada`) VALUES
(36, 181, 'order', NULL, 'created', 15, 'Pesanan dibuat oleh pelanggan.', '2025-11-30 08:58:12'),
(37, 181, 'payment', 'unpaid', 'pending', NULL, 'Membuat transaksi ke payment gateway.', '2025-11-30 08:58:13'),
(38, 181, 'payment', 'pending', 'pending', NULL, 'Membuat transaksi ke payment gateway.', '2025-11-30 08:58:42'),
(39, 182, 'order', NULL, 'created', 2, 'Pesanan dibuat oleh pelanggan.', '2025-12-01 07:45:34'),
(40, 182, 'payment', 'unpaid', 'pending', NULL, 'Membuat transaksi ke payment gateway.', '2025-12-01 07:45:34'),
(41, 181, 'order', 'created', 'canceled', 1, 'Perubahan status oleh admin.', '2025-12-01 17:09:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `pass_user` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_user`, `pass_user`, `role`, `dibuat_pada`) VALUES
(1, 'Samsul', '12345', 'admin', '2025-11-24 01:00:00'),
(2, 'Budi', '12345', 'pelanggan', '2025-11-24 01:05:00'),
(3, 'fufu', '$2y$10$96/z3Yv1iFrzk.JUfX.bpuA8bkOG4zCULVsk199h86NheGkuVrM7K', 'pelanggan', '2025-11-26 01:11:27'),
(4, 'Sumsal', '$2y$10$lB67S.RfLXLIFaSXyzu3oe4ad2tneBLJasPWWF6.zEkacH0Uv2TQK', 'pelanggan', '2025-11-26 09:12:26'),
(5, 'Yahu', '$2y$10$kQRvkO4.XKFu3GRv.9pt5uOBdMX0R31q9gBG/OfUWHzmMcoSHxYTW', 'pelanggan', '2025-11-27 12:51:47'),
(6, 'Andito', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(7, 'Rina Dwi', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(8, 'Dewi F', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(9, 'Agus Gosling', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(10, 'Siti Maryani', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(11, 'Rudi ', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(12, 'Lina', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(13, 'Hendra', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(14, 'Putri', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(15, 'Dimas ', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(16, 'Ninana', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(17, 'Tono Fun', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(18, 'Yulinya', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(19, 'Bayu Kopling', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(20, 'Tari', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(21, 'Andi', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(22, 'Farah', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(23, 'Joko Tingkir', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(24, 'Mira Din', '12345', 'pelanggan', '2025-10-01 01:00:00'),
(25, 'Bambang', '12345', 'pelanggan', '2025-10-01 01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail_pesanan`),
  ADD KEY `idx_detail_pesanan` (`id_pesanan`),
  ADD KEY `idx_detail_menu` (`id_menu`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD UNIQUE KEY `uniq_keranjang_user_menu` (`id_user`,`id_menu`),
  ADD KEY `idx_keranjang_user` (`id_user`),
  ADD KEY `idx_keranjang_menu` (`id_menu`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `idx_pembayaran_pesanan` (`id_pesanan`),
  ADD KEY `idx_pembayaran_status` (`status`,`settlement_time`),
  ADD KEY `idx_pembayaran_provider_order` (`provider_order_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `uniq_kode_pesanan` (`kode_pesanan`),
  ADD KEY `idx_pesanan_user` (`id_user`),
  ADD KEY `idx_pesanan_payment` (`payment_status`,`paid_at`),
  ADD KEY `idx_pesanan_order` (`order_status`,`created_at`);

--
-- Indexes for table `riwayat_status_pesanan`
--
ALTER TABLE `riwayat_status_pesanan`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `idx_riwayat_pesanan` (`id_pesanan`,`dibuat_pada`),
  ADD KEY `fk_riwayat_user` (`diubah_oleh`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `riwayat_status_pesanan`
--
ALTER TABLE `riwayat_status_pesanan`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_pesanan_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `fk_detail_pesanan_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `fk_keranjang_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_status_pesanan`
--
ALTER TABLE `riwayat_status_pesanan`
  ADD CONSTRAINT `fk_riwayat_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_riwayat_user` FOREIGN KEY (`diubah_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
