-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 01:36 PM
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
-- Database: `attendancemsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_blocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `user_type`, `ip_address`, `login_time`) VALUES
(1, 1, 'Student', '::1', '2025-05-05 15:15:28'),
(2, 1, 'Student', '::1', '2025-05-05 15:16:05'),
(3, 1, 'Administrator', '::1', '2025-05-05 15:16:49'),
(4, 1, 'Administrator', '::1', '2025-05-05 15:17:04'),
(5, 1, 'Administrator', '::1', '2025-05-05 15:24:36');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `user_identifier` varchar(255) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_type`, `user_identifier`, `reset_token`, `expiry`, `used`, `created_at`) VALUES
(1, 'Student', 'AMS007', '760e5461ff494250bdaab715c65810eaff74b79c3ef4396d5378c6d255617d89', '2025-05-03 08:29:52', 0, '2025-05-02 06:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `rate_limit`
--

CREATE TABLE `rate_limit` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`) VALUES
(1, 'Admin', '', 'admin@mail.com', '$2y$10$33yhdWvw9NQZ/nmwSgqW8.oxZyrQKSOoni0M4sRqaIJcPni.3fXUW');

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance`
--

CREATE TABLE `tblattendance` (
  `Id` int(10) NOT NULL,
  `admissionNo` varchar(255) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `sessionTermId` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `dateTimeTaken` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblattendance`
--

INSERT INTO `tblattendance` (`Id`, `admissionNo`, `classId`, `classArmId`, `sessionTermId`, `status`, `dateTimeTaken`) VALUES
(162, 'ASDFLKJ', '1', '2', '1', '1', '2020-11-01'),
(163, 'HSKSDD', '1', '2', '1', '1', '2020-11-01'),
(164, 'JSLDKJ', '1', '2', '1', '1', '2020-11-01'),
(172, 'HSKDS9EE', '1', '4', '1', '1', '2020-11-01'),
(171, 'JKADA', '1', '4', '1', '0', '2020-11-01'),
(170, 'JSFSKDJ', '1', '4', '1', '1', '2020-11-01'),
(173, 'ASDFLKJ', '1', '2', '1', '1', '2020-11-19'),
(174, 'HSKSDD', '1', '2', '1', '1', '2020-11-19'),
(175, 'JSLDKJ', '1', '2', '1', '1', '2020-11-19'),
(176, 'JSFSKDJ', '1', '4', '1', '0', '2021-07-15'),
(177, 'JKADA', '1', '4', '1', '0', '2021-07-15'),
(178, 'HSKDS9EE', '1', '4', '1', '0', '2021-07-15'),
(179, 'ASDFLKJ', '1', '2', '1', '0', '2021-09-27'),
(180, 'HSKSDD', '1', '2', '1', '1', '2021-09-27'),
(181, 'JSLDKJ', '1', '2', '1', '1', '2021-09-27'),
(182, 'ASDFLKJ', '1', '2', '1', '0', '2021-10-06'),
(183, 'HSKSDD', '1', '2', '1', '0', '2021-10-06'),
(184, 'JSLDKJ', '1', '2', '1', '1', '2021-10-06'),
(185, 'ASDFLKJ', '1', '2', '1', '0', '2021-10-07'),
(186, 'HSKSDD', '1', '2', '1', '0', '2021-10-07'),
(187, 'JSLDKJ', '1', '2', '1', '0', '2021-10-07'),
(188, 'AMS110', '4', '6', '1', '1', '2021-10-07'),
(189, 'AMS133', '4', '6', '1', '1', '2021-10-07'),
(190, 'AMS135', '4', '6', '1', '1', '2021-10-07'),
(191, 'AMS144', '4', '6', '1', '1', '2021-10-07'),
(192, 'AMS148', '4', '6', '1', '1', '2021-10-07'),
(193, 'AMS151', '4', '6', '1', '1', '2021-10-07'),
(194, 'AMS159', '4', '6', '1', '1', '2021-10-07'),
(195, 'AMS161', '4', '6', '1', '1', '2021-10-07'),
(196, 'AMS110', '4', '6', '1', '1', '2022-06-06'),
(197, 'AMS133', '4', '6', '1', '1', '2022-06-06'),
(198, 'AMS135', '4', '6', '1', '1', '2022-06-06'),
(199, 'AMS144', '4', '6', '1', '1', '2022-06-06'),
(200, 'AMS148', '4', '6', '1', '1', '2022-06-06'),
(201, 'AMS151', '4', '6', '1', '1', '2022-06-06'),
(202, 'AMS159', '4', '6', '1', '1', '2022-06-06'),
(203, 'AMS161', '4', '6', '1', '1', '2022-06-06'),
(204, 'AMS110', '4', '6', '1', '1', '2025-04-20'),
(205, 'AMS133', '4', '6', '1', '1', '2025-04-20'),
(206, 'AMS135', '4', '6', '1', '1', '2025-04-20'),
(207, 'AMS144', '4', '6', '1', '1', '2025-04-20'),
(208, 'AMS148', '4', '6', '1', '1', '2025-04-20'),
(209, 'AMS151', '4', '6', '1', '1', '2025-04-20'),
(210, 'AMS159', '4', '6', '1', '1', '2025-04-20'),
(211, 'AMS161', '4', '6', '1', '0', '2025-04-20'),
(212, 'AMS110', '4', '6', '1', '1', '2025-05-02'),
(213, 'AMS133', '4', '6', '1', '1', '2025-05-02'),
(214, 'AMS135', '4', '6', '1', '1', '2025-05-02'),
(215, 'AMS144', '4', '6', '1', '1', '2025-05-02'),
(216, 'AMS148', '4', '6', '1', '1', '2025-05-02'),
(217, 'AMS151', '4', '6', '1', '1', '2025-05-02'),
(218, 'AMS159', '4', '6', '1', '1', '2025-05-02'),
(219, 'AMS161', '4', '6', '1', '0', '2025-05-02'),
(220, 'AMS110', '4', '6', '1', '1', '2025-05-08'),
(221, 'AMS133', '4', '6', '1', '1', '2025-05-08'),
(222, 'AMS135', '4', '6', '1', '1', '2025-05-08'),
(223, 'AMS144', '4', '6', '1', '1', '2025-05-08'),
(224, 'AMS148', '4', '6', '1', '1', '2025-05-08'),
(225, 'AMS151', '4', '6', '1', '1', '2025-05-08'),
(226, 'AMS159', '4', '6', '1', '1', '2025-05-08'),
(227, 'AMS161', '4', '6', '1', '0', '2025-05-08'),
(228, 'AMS110', '4', '6', '1', '1', '2025-05-10'),
(229, 'AMS133', '4', '6', '1', '1', '2025-05-10'),
(230, 'AMS135', '4', '6', '1', '1', '2025-05-10'),
(231, 'AMS144', '4', '6', '1', '1', '2025-05-10'),
(232, 'AMS148', '4', '6', '1', '1', '2025-05-10'),
(233, 'AMS151', '4', '6', '1', '0', '2025-05-10'),
(234, 'AMS159', '4', '6', '1', '0', '2025-05-10'),
(235, 'AMS161', '4', '6', '1', '0', '2025-05-10'),
(236, 'AMS012', '1', '4', '1', '0', '2025-05-10'),
(237, 'AMS015', '1', '4', '1', '1', '2025-05-10'),
(238, 'AMS017', '1', '4', '1', '1', '2025-05-10'),
(239, 'AMS005', '1', '2', '1', '1', '2025-05-10'),
(240, 'AMS007', '1', '2', '1', '1', '2025-05-10'),
(241, 'AMS011', '1', '2', '1', '0', '2025-05-10');

-- --------------------------------------------------------

--
-- Table structure for table `tblclass`
--

CREATE TABLE `tblclass` (
  `Id` int(10) NOT NULL,
  `className` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclass`
--

INSERT INTO `tblclass` (`Id`, `className`) VALUES
(1, 'Seven'),
(3, 'Eight'),
(4, 'Nine'),
(5, 'CLASS-TEN');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassarms`
--

CREATE TABLE `tblclassarms` (
  `Id` int(10) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmName` varchar(255) NOT NULL,
  `isAssigned` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassarms`
--

INSERT INTO `tblclassarms` (`Id`, `classId`, `classArmName`, `isAssigned`) VALUES
(2, '1', 'S1', '1'),
(4, '1', 'S2', '1'),
(5, '3', 'E1', '1'),
(6, '4', 'N1', '1'),
(7, '5', 'S3', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassteacher`
--

CREATE TABLE `tblclassteacher` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNo` varchar(50) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassteacher`
--

INSERT INTO `tblclassteacher` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`, `phoneNo`, `classId`, `classArmId`, `dateCreated`) VALUES
(1, 'ramkumar', 'k', 'teacher2@mail.com', '$2y$10$DurHxG1vuyYwiifuPHIJJOg3ecjy/dYn3ZD/uPM3Cw/E0fLDwHskK', '09089898999', '1', '2', '2022-10-31'),
(4, 'sharukhan', 'A', 'teacher3@gmail.com', '$2y$10$DurHxG1vuyYwiifuPHIJJOg3ecjy/dYn3ZD/uPM3Cw/E0fLDwHskK', '09672002882', '1', '4', '2022-11-01'),
(5, 'subuash', 'M', 'teacher4@mail.com', '$2y$10$DurHxG1vuyYwiifuPHIJJOg3ecjy/dYn3ZD/uPM3Cw/E0fLDwHskK', '7014560000', '3', '5', '2022-10-07'),
(6, 'subhiskhan', 'K', 'teacher@mail.com', '$2y$10$DurHxG1vuyYwiifuPHIJJOg3ecjy/dYn3ZD/uPM3Cw/E0fLDwHskK', '0100000030', '4', '6', '2022-10-07'),
(7, 'Naveen', 'KUMAR', 'janaanaveen4115@gmail.com', '32250170a0dca92d53ec9624f336ca24', '90087040231', '5', '7', '2025-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `tblsessionterm`
--

CREATE TABLE `tblsessionterm` (
  `Id` int(10) NOT NULL,
  `sessionName` varchar(50) NOT NULL,
  `termId` varchar(50) NOT NULL,
  `isActive` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsessionterm`
--

INSERT INTO `tblsessionterm` (`Id`, `sessionName`, `termId`, `isActive`, `dateCreated`) VALUES
(1, '2024/2025', '1', '1', '2024-10-31'),
(3, '2025/2026', '2', '0', '2024-10-31'),
(4, '2026/2027', '2', '0', '2025-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `otherName` varchar(255) NOT NULL,
  `admissionNumber` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`Id`, `firstName`, `lastName`, `otherName`, `admissionNumber`, `password`, `classId`, `classArmId`, `dateCreated`) VALUES
(1, 'Vetrivel', 'S', 'none', 'AMS005', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '1', '2', '2022-10-31'),
(3, 'Harish', 'k', 'none', 'AMS007', '$2y$10$hsbQa5Zwtd3I773x9adK9.7dbRRHAaMAAUX4WCQbYy45gKaYTocNC', '1', '2', '2022-10-31'),
(4, 'sudankumar', 'H', 'none', 'AMS011', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '1', '2', '2022-10-31'),
(5, 'priya', 'A', 'none', 'AMS012', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '1', '4', '2022-10-31'),
(6, 'Sandhiya', 'S', 'none', 'AMS015', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '1', '4', '2022-10-31'),
(7, 'Gopikrishan', 'M', 'Mack', 'AMS017', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '1', '4', '2022-10-31'),
(8, 'Ramya', 'K', 'none', 'AMS019', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '3', '5', '2022-10-31'),
(9, 'karthick', 'S', 'none', 'AMS021', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '3', '5', '2022-10-31'),
(10, 'vertriselvan', 'M', 'none', 'AMS110', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(11, 'sriharish', 'M', 'none', 'AMS133', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(12, 'Monish', 'B', 'none', 'AMS135', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(13, 'preethi', 'H', 'none', 'AMS144', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(14, 'Mogeshwaran', 'M', 'none', 'AMS148', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(15, 'Naresh', 'D', 'none', 'AMS151', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(16, 'senthil', 'A', 'none', 'AMS159', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07'),
(17, 'Sathivel', 'E', 'none', 'AMS161', '$2y$10$xuqbwLUjCS0EFIN6yHQxbOrbItqEGVdERJ9AED53Exu3b6iXvZ.Ym', '4', '6', '2022-10-07');

-- --------------------------------------------------------

--
-- Table structure for table `tblterm`
--

CREATE TABLE `tblterm` (
  `Id` int(10) NOT NULL,
  `termName` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblterm`
--

INSERT INTO `tblterm` (`Id`, `termName`) VALUES
(1, 'First'),
(2, 'Second'),
(3, 'Third');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_time` (`login_time`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rate_limit`
--
ALTER TABLE `rate_limit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_endpoint` (`ip_address`,`endpoint`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclass`
--
ALTER TABLE `tblclass`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblterm`
--
ALTER TABLE `tblterm`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rate_limit`
--
ALTER TABLE `rate_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `tblclass`
--
ALTER TABLE `tblclass`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblterm`
--
ALTER TABLE `tblterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
