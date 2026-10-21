-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 21, 2026 at 03:06 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dhestar_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `gambar` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `gambar`) VALUES
(3, 'Buku Tulis Tebal', 3000, 12, 'default.jpg'),
(4, 'Kokoro Bolpoin 5mm', 5000, 11, 'default.jpg'),
(5, 'Buku Gambar ', 4000, 9, 'default.jpg'),
(6, 'Rautan', 2000, 17, 'default.jpg'),
(7, 'Glue Stick', 3000, 10, 'default.jpg'),
(8, 'Sticky Notes', 5000, 18, 'default.jpg'),
(9, 'Papper Clip (pack)', 3000, 19, 'default.jpg'),
(10, 'Correction Tape', 5000, 8, 'default.jpg'),
(11, 'Stabilo Warna', 4000, 9, 'default.jpg'),
(12, 'Pensil 2B', 2000, 22, 'default.jpg'),
(13, 'Notebook', 7000, 21, 'default.jpg'),
(14, 'Spidol Warna Besar', 6000, 8, 'default.jpg'),
(15, 'Spidol Warna Kecil', 3000, 8, 'default.jpg'),
(16, 'Penggaris', 5000, 18, 'default.jpg'),
(17, 'Penghapus', 2000, 10, 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `no_nota` varchar(30) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `total_harga` int NOT NULL,
  `metode_pembayaran` varchar(20) NOT NULL,
  `jumlah_uang` int NOT NULL,
  `kembalian` int NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `no_nota`, `nama_pembeli`, `id_produk`, `jumlah`, `total_harga`, `metode_pembayaran`, `jumlah_uang`, `kembalian`, `tanggal`) VALUES
(1, 'NT-20261007025717', 'Cinta', 3, 1, 3000, 'Tunai', 20000, 5000, '2026-10-07 02:57:17'),
(2, 'NT-20261007025717', 'Cinta', 6, 1, 2000, 'Tunai', 20000, 5000, '2026-10-07 02:57:17'),
(3, 'NT-20261007025717', 'Cinta', 8, 1, 5000, 'Tunai', 20000, 5000, '2026-10-07 02:57:17'),
(4, 'NT-20261007025717', 'Cinta', 4, 1, 5000, 'Tunai', 20000, 5000, '2026-10-07 02:57:17'),
(5, 'NT-20261007030545', 'Berlian', 5, 1, 4000, 'QRIS', 40000, 0, '2026-10-07 03:05:45'),
(6, 'NT-20261007030545', 'Berlian', 9, 1, 3000, 'QRIS', 40000, 0, '2026-10-07 03:05:45'),
(7, 'NT-20261007030545', 'Berlian', 6, 2, 4000, 'QRIS', 40000, 0, '2026-10-07 03:05:45'),
(8, 'NT-20261007030545', 'Berlian', 12, 4, 8000, 'QRIS', 40000, 0, '2026-10-07 03:05:45'),
(9, 'NT-20261007030545', 'Berlian', 13, 3, 21000, 'QRIS', 40000, 0, '2026-10-07 03:05:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', 'admin123', 'Administrator Dhestar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
