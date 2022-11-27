-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2022 at 02:26 PM
-- Server version: 5.7.21-20-beget-5.7.21-20-1-log
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+05:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `DBname`
--

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--
-- Creation: Nov 14, 2022 at 09:02 AM
--

CREATE TABLE `channels` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT 'Bizning Kanal',
  `object` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `channels`
--

INSERT INTO `channels` (`id`, `name`, `object`) VALUES
(1, 'status', 'on');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Nov 14, 2022 at 10:26 AM
-- Last update: Nov 27, 2022 at 11:23 AM
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fromid` varchar(70) NOT NULL,
  `name` varchar(120) NOT NULL DEFAULT '',
  `user` varchar(40) NOT NULL DEFAULT '',
  `chat_type` varchar(20) NOT NULL DEFAULT 'private',
  `lang` varchar(20) NOT NULL DEFAULT '',
  `del` varchar(5) NOT NULL DEFAULT '0',
  `created_at` varchar(80) NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;