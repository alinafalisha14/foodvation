-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 10:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodvation`
--

-- --------------------------------------------------------

--
-- Table structure for table `daftar_menu`
--

CREATE TABLE `daftar_menu` (
  `id_menu` int(11) NOT NULL,
  `restoran_id` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `gambar_makanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_menu`
--

INSERT INTO `daftar_menu` (`id_menu`, `restoran_id`, `nama_menu`, `harga`, `gambar_makanan`) VALUES
(1, 1, 'Sate Ayam Spesial', 25000, 'sate_ayam.jpg'),
(2, 1, 'Es Teh Manis', 5000, 'es_teh.jpg'),
(3, 2, 'Paket Geprek Nasi', 18000, 'geprek_nasi.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan_reservasi`
--

CREATE TABLE `detail_pesanan_reservasi` (
  `id_detail` int(11) NOT NULL,
  `reservasi_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `jumlah_pesan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan_reservasi`
--

INSERT INTO `detail_pesanan_reservasi` (`id_detail`, `reservasi_id`, `menu_id`, `jumlah_pesan`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id_meja` int(11) NOT NULL,
  `restoran_id` int(11) NOT NULL,
  `no_meja` int(11) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `gambar_meja` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `restoran_id`, `no_meja`, `kapasitas`, `gambar_meja`) VALUES
(1, 1, 1, 4, ''),
(2, 1, 2, 2, ''),
(3, 2, 1, 4, ''),
(4, 2, 2, 6, '');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `no_reservasi` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restoran_id` int(11) NOT NULL,
  `meja_id` int(11) NOT NULL,
  `tanggal_reservasi` date NOT NULL,
  `waktu_reservasi` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `no_reservasi`, `user_id`, `restoran_id`, `meja_id`, `tanggal_reservasi`, `waktu_reservasi`) VALUES
(1, 'RSV-20260511-619', 3, 1, 2, '2026-05-13', '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `restoran`
--

CREATE TABLE `restoran` (
  `id_restoran` int(11) NOT NULL,
  `nama_restoran` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `gambar_restoran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restoran`
--

INSERT INTO `restoran` (`id_restoran`, `nama_restoran`, `alamat`, `gambar_restoran`) VALUES
(1, 'Warung Sate Bahagia', 'Jl. Bojongsoang No. 10', 'sate_bahagia.jpg'),
(2, 'Ayam Geprek Juara', 'Jl. Telekomunikasi No. 2', 'geprek_juara.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','admin') NOT NULL DEFAULT 'pelanggan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `role`) VALUES
(1, 'admin@foodvation.com', 'admin123', 'admin'),
(2, 'pelanggan@gmail.com', 'pelanggan123', 'pelanggan'),
(3, 'Hanlin@gmail.com', 'halin123pel', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daftar_menu`
--
ALTER TABLE `daftar_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `restoran_id` (`restoran_id`);

--
-- Indexes for table `detail_pesanan_reservasi`
--
ALTER TABLE `detail_pesanan_reservasi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `reservasi_id` (`reservasi_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`),
  ADD KEY `restoran_id` (`restoran_id`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD UNIQUE KEY `no_reservasi` (`no_reservasi`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restoran_id` (`restoran_id`),
  ADD KEY `meja_id` (`meja_id`);

--
-- Indexes for table `restoran`
--
ALTER TABLE `restoran`
  ADD PRIMARY KEY (`id_restoran`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daftar_menu`
--
ALTER TABLE `daftar_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detail_pesanan_reservasi`
--
ALTER TABLE `detail_pesanan_reservasi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restoran`
--
ALTER TABLE `restoran`
  MODIFY `id_restoran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daftar_menu`
--
ALTER TABLE `daftar_menu`
  ADD CONSTRAINT `daftar_menu_ibfk_1` FOREIGN KEY (`restoran_id`) REFERENCES `restoran` (`id_restoran`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_pesanan_reservasi`
--
ALTER TABLE `detail_pesanan_reservasi`
  ADD CONSTRAINT `detail_pesanan_reservasi_ibfk_1` FOREIGN KEY (`reservasi_id`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_reservasi_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `daftar_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meja`
--
ALTER TABLE `meja`
  ADD CONSTRAINT `meja_ibfk_1` FOREIGN KEY (`restoran_id`) REFERENCES `restoran` (`id_restoran`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`restoran_id`) REFERENCES `restoran` (`id_restoran`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_3` FOREIGN KEY (`meja_id`) REFERENCES `meja` (`id_meja`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
