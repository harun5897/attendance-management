-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 05:29 PM
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
-- Database: `attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_attendance`
--

CREATE TABLE `tb_attendance` (
  `id_attendance` int(11) NOT NULL,
  `code_employee` int(11) NOT NULL,
  `time` varchar(50) DEFAULT NULL,
  `overtime` varchar(50) DEFAULT NULL,
  `meal_box` varchar(50) DEFAULT NULL,
  `date_attendance` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_attendance`
--

INSERT INTO `tb_attendance` (`id_attendance`, `code_employee`, `time`, `overtime`, `meal_box`, `date_attendance`) VALUES
(33, 210213, '07:56', '1', 'siang', '2024-12-02'),
(34, 202414, '07:41', '1', 'siang_malam', '2024-12-02'),
(35, 241003, '07:47', '1', 'malam', '2024-12-02');

-- --------------------------------------------------------

--
-- Table structure for table `tb_employee`
--

CREATE TABLE `tb_employee` (
  `code_employee` int(9) NOT NULL,
  `name_employee` varchar(255) NOT NULL,
  `fingerprint` int(9) NOT NULL,
  `date_join` date NOT NULL,
  `departement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_employee`
--

INSERT INTO `tb_employee` (`code_employee`, `name_employee`, `fingerprint`, `date_join`, `departement`) VALUES
(202414, ' FELASTRI', 2178, '2024-07-10', 'TM'),
(210213, ' MOLLY PHANG', 1777, '2021-02-04', 'TM'),
(241003, ' VIRA YUNITA', 2209, '2024-10-21', 'TM');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id_user` int(9) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `departement` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id_user`, `username`, `email`, `password`, `role`, `departement`) VALUES
(29, 'leader_TM', 'leader_tm@example.com', '$2y$10$LzYJR/cflLSyoOXlEOEa3uq5u7x0GQomosK9HTV6fSOhYOAjfmjB.', 'LEADER', 'TM'),
(31, 'admin_1', 'admin_1@example.com', '$2y$10$caRQa24MxiZKnrd9ERr7v.3i2IXeDsMb6mhrcZwCQKhCV6jGTPc/2', 'ADMIN', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_attendance`
--
ALTER TABLE `tb_attendance`
  ADD PRIMARY KEY (`id_attendance`);

--
-- Indexes for table `tb_employee`
--
ALTER TABLE `tb_employee`
  ADD PRIMARY KEY (`code_employee`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_attendance`
--
ALTER TABLE `tb_attendance`
  MODIFY `id_attendance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id_user` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
