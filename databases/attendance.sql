-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 06:29 PM
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
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id_user`, `username`, `email`, `password`, `role`) VALUES
(10, 'admin_1', 'admin1@example.com', '$2y$10$5bgYB35c2Q00VUeHnvX3N.UnyZCjtBUBwX4L8J/2bAV7l7lhXbbg2', 'manager'),
(11, 'admin_2', 'admin2@example.com', '$2y$10$alb6UGXjiqrTDY05fbpVEeEdTpYGeScDr1NAIt/DLQf0hnEVJWitm', 'admin'),
(14, 'leader_1', 'leader1@example.com', '$2y$10$bMDjQ0DedblpqJ.YOmAxxOg8plIiSELzaWKXdJPaOKdOKkyveWYoy', 'leader'),
(15, 'leader_2', 'leader2@example.com', '$2y$10$dFYvdifj6/j.bwfhPEa3kun.8Dmuc6LCFVu3ZOWvgnXEEituoCsZS', 'leader'),
(16, 'manager_1', 'manager1@example.com', '$2y$10$OX/RrZcNmNzcsHwFWzPcmeK5LhOlpj3bhJ4.PHouzrC1N4c7D4w/C', 'manager'),
(17, 'manager_2', 'manager2@example.com', '$2y$10$YH64BaTtGuB57EvIeJZs7unu5IINuSSNBwxlmd8IWE5tFk0HaJlVe', 'manager'),
(18, 'testing_1', 'testing1@example.com', '$2y$10$OdWpeyNljFF/Zrca5s9LWewPC2Z16QXzD6uHdGBZ.6YKjgle8xMb2', 'admin'),
(19, 'testing_2', 'testing_2@example.com', '$2y$10$iuv88/c6SwSXHjLlLptHxeAQ/xEXhDG1hq.9kTfaQU0fxYpdnSKiq', 'leader'),
(20, 'admin_terakhir', 'admin_terakhir@example.com', '$2y$10$2IPouQV6bFTd2Cwq8MUBKeGn/l6Ocx8CAr9fRePgE9Mh3MCr0rR2q', 'manager'),
(21, 'admin_10', 'admin10@example.com', '$2y$10$Q0IlfrPLcr.SZU7CcvLXAeR7WZlIYFCby26XuwMctfjZUKwJ1aIKm', 'admin'),
(22, 'admin_11', 'admin11@example.com', '$2y$10$gZxEGkxuCZ12/zjdnM3xXulrPMQf9wCNO6iQiO0FqlXNPA0xBDhy.', 'admin'),
(23, 'admin_12', 'admin12@example.com', '$2y$10$CU5T.UlVl0vv5iC3NojgV.xBt.n4saKjRAMaSZGGNgVqVw52fGfZG', 'admin'),
(24, 'admin_13', 'admin13@example.com', '$2y$10$BrcwkfDpEMKgwuhlrt7H8u0f1hoygzmsUl/wVJxeoNX7eJuZVqkp.', 'admin'),
(25, 'admin_14', 'admin14@example.com', '$2y$10$BxD0/.sBPODK/EzKg08ZqeCU6Ikx6OUjAKxzGVBtN7ELb.P7JxigC', 'admin'),
(26, 'admin_15', 'admin15@example.com', '$2y$10$lvjQhoYDJTV2XEE58Ie5GurIGmOXzJl9nnjlDqXE.ZF1jm8.tGZ0m', 'admin'),
(27, 'admin_16', 'admin16@example.com', '$2y$10$4FPIEOTQ3c0RLE/lukPl3eVoIP4qsbetFf9z5UwlDxmm/c1VfF7CG', 'admin'),
(28, 'admin_17', 'admin17@example.com', '$2y$10$DSCqejI6pY4WkEnOmnvZ6e4fA0fCsFyYnq0IYjgH97wSQXs7YqOQK', 'admin');

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
  MODIFY `id_attendance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id_user` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
