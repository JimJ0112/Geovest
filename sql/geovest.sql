-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2025 at 09:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geovest`
--

-- --------------------------------------------------------

--
-- Table structure for table `vests`
--

CREATE TABLE `vests` (
  `id` int(11) NOT NULL,
  `vest_number` varchar(255) NOT NULL,
  `in_use` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vests`
--

INSERT INTO `vests` (`id`, `vest_number`, `in_use`, `status`) VALUES
(1, 'vest_0001', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vest_locations`
--

CREATE TABLE `vest_locations` (
  `id` int(11) NOT NULL,
  `vest_id` int(11) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `location_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vest_locations`
--

INSERT INTO `vest_locations` (`id`, `vest_id`, `latitude`, `longitude`, `location_name`) VALUES
(1, 1, '0.000000', '0.000000', 'ph');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vests`
--
ALTER TABLE `vests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vest_locations`
--
ALTER TABLE `vest_locations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vests`
--
ALTER TABLE `vests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vest_locations`
--
ALTER TABLE `vest_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
