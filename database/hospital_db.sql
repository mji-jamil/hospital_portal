-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 04:56 PM
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
-- Database: `hospital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(5, 2, 1, '2025-07-01', '15:46:00', 'Completed', '2025-07-01 09:45:09'),
(6, 2, 1, '2025-07-01', '15:49:00', 'Completed', '2025-07-01 09:47:22'),
(7, 2, 1, '2025-07-05', '11:53:00', 'Completed', '2025-07-01 09:50:13'),
(8, 2, 1, '2025-07-18', '16:13:00', 'Completed', '2025-07-01 10:10:21'),
(9, 3, 1, '2025-07-03', '18:12:00', 'Completed', '2025-07-01 10:12:19'),
(10, 2, 3, '2025-07-02', '21:28:00', 'Completed', '2025-07-01 15:27:32'),
(11, 4, 1, '2025-07-04', '14:45:00', 'Completed', '2025-07-02 17:45:54'),
(12, 2, 1, '2025-07-04', '02:49:00', 'Completed', '2025-07-02 18:49:59'),
(13, 2, 1, '2025-07-03', '00:56:00', 'Completed', '2025-07-02 18:53:58'),
(14, 2, 1, '2025-07-22', '00:01:00', 'Completed', '2025-07-02 18:58:17'),
(15, 2, 1, '2025-07-31', '00:03:00', 'Completed', '2025-07-02 18:59:42'),
(16, 2, 1, '2025-07-25', '01:04:00', 'Completed', '2025-07-02 19:01:33'),
(17, 2, 1, '2025-07-24', '01:12:00', 'Completed', '2025-07-02 19:09:34'),
(18, 2, 1, '2025-07-22', '01:37:00', 'Completed', '2025-07-02 19:33:00'),
(19, 2, 1, '2025-07-25', '05:37:00', 'Completed', '2025-07-02 19:37:21'),
(20, 4, 1, '2025-08-01', '01:49:00', 'Completed', '2025-07-02 19:44:18'),
(21, 5, 1, '2025-07-05', '20:15:00', 'Completed', '2025-07-03 18:16:03'),
(22, 5, 1, '2025-07-04', '01:22:00', 'Completed', '2025-07-03 18:22:38'),
(23, 2, 1, '2025-07-19', '16:15:00', 'Completed', '2025-07-04 06:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `appointment_id`, `patient_id`, `amount`, `status`, `notes`, `created_at`) VALUES
(1, 5, 2, 122332.00, 'Paid', 'pay now', '2025-07-02 18:06:53'),
(2, 6, 2, 6565.00, 'Paid', 'pay now', '2025-07-02 18:08:02'),
(3, 7, 2, 6669.00, 'Paid', 'Pay now', '2025-07-02 18:16:46'),
(4, 8, 2, 6565.00, 'Paid', 'Pay later', '2025-07-02 18:26:51'),
(5, 7, 2, 666.00, 'Paid', 'pay now', '2025-07-02 18:35:42'),
(6, 10, 2, 666666.00, 'Paid', '', '2025-07-02 18:42:58'),
(7, 12, 2, 6666.00, 'Paid', '', '2025-07-02 18:50:52'),
(8, 13, 2, 9999.00, 'Paid', 'pay with card', '2025-07-02 18:54:36'),
(9, 14, 2, 6666.00, 'Paid', '', '2025-07-02 18:58:34'),
(10, 15, 2, 99999.00, 'Paid', '', '2025-07-02 19:00:11'),
(11, 16, 2, 99999.00, 'Paid', '', '2025-07-02 19:01:48'),
(12, 17, 2, 999999.00, 'Paid', '', '2025-07-02 19:09:47'),
(13, 11, 4, 4444.00, 'Unpaid', 'pay with card', '2025-07-02 19:44:31'),
(14, 19, 2, 99999999.99, 'Unpaid', '', '2025-07-02 20:03:55'),
(15, 21, 5, 14250.00, 'Paid', 'Pay via bkash', '2025-07-03 18:39:40'),
(16, 18, 2, 69875.00, 'Paid', 'pay by cash', '2025-07-04 06:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `username`, `password`, `name`, `specialization`, `email`) VALUES
(1, 'drashraful', '$2y$10$DcJQZ.ROlWAiyNzvrbFIY.ROU3wrM1kMcwHI21UFUUiCIh6nNNn3q', 'Dr. Ashraful Alam', 'Cardiology', 'ashraful@example.com'),
(3, 'drayesha', '$2y$10$QHCFRJQp6m8LWbm4ZiZbD.fVMglWuDMvtpUJ9XJKcszOMdCc//Xoi', 'Ayesha', 'Medicine', NULL),
(4, 'shila', '$2y$10$9J8SunIk1nrgkzqtMGFCqeomnH5BlVk36uLN4v0uw/WRIzdKo3MLC', 'ms shila', 'Ortho', NULL),
(5, 'aalikhan', '$2y$10$/Cnh.Xvpl8QsJ9.SAQSpwOYzj.zyBiQniReTGxeV9/twVJa.Z2gia', 'Amzad Ali Khan', 'Medicine', NULL),
(6, 'drbashar', '$2y$10$7zh31B0MKLFAYkVhidPlU.J3Sw05AEfkFR/T.DQXhCcL8lt8YngKG', 'Bashar Al Asad', 'Densitry', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `username`, `password`, `name`, `email`) VALUES
(1, 'jsmith', '$2y$10$To9MsnMC1fMorkhwbGTeN.YVB7B/ocXzO0hA5Tzxjr2fTBLOl3SEi', 'John Smith', 'johnsmith@gmail.com'),
(2, 'kalam', '$2y$10$oAqhposX4MDVzCytwQIyterR3ivAW68POpBnJ/3dcJJNjhds0qmzq', 'mr kalam', 'kalam@gmail.com'),
(3, 'jamalali', '$2y$10$YYf..mFlP0EsIfsYTZT0kOJucMwmWuKRTrEtxGY.E4qQYIw2amV8C', 'jamal ali', 'jamalali@gmail.com'),
(4, 'mrali', '$2y$10$Zmi.yaUEdfhjAsaaCjWRLeVZzlz60HkVECELf4g01ZFE0fMP6gpBK', 'Mr Ali', 'ali@gmail.com'),
(5, 'raja', '$2y$10$bTAj/vKaX9HObglQBSMUou8dylP6/ku6w4akCR7xxcnvaVsSBvP0i', 'Raja Kha', 'raja@gmail.com'),
(6, 'bappy', '$2y$10$HnKzjD1HcyyDYDISe8QfqOd2CYRhOCxugPWB/ktXVcb82cJow4jb.', 'Bappy', 'bappy@gmail.com'),
(7, 'hridoya', '$2y$10$IE69RkSCAh6F9GX8e9nU/.SExSJvKy9Vk3D5zvjRKyAMrctLenOwO', 'hridoya', 'hridoya@email.com');

-- --------------------------------------------------------

--
-- Table structure for table `patient_profiles`
--

CREATE TABLE `patient_profiles` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_profiles`
--

INSERT INTO `patient_profiles` (`id`, `patient_id`, `age`, `gender`, `symptoms`, `medical_history`, `created_at`) VALUES
(1, 2, 95, 'Male', 'bp', 'high bp', '2025-07-02 17:27:10'),
(2, 4, 98, 'Male', 'High fever', 'Low BP', '2025-07-02 17:45:38'),
(3, 5, 99, 'Male', 'fever', '', '2025-07-03 18:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `diagnosis` text NOT NULL,
  `medications` text NOT NULL,
  `advice` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `patient_id`, `doctor_id`, `notes`, `file_path`, `created_at`, `diagnosis`, `medications`, `advice`) VALUES
(4, 9, 3, 1, NULL, NULL, '2025-07-03 01:42:57', 'sd', 'asd', 'asd'),
(5, 5, 2, 1, NULL, NULL, '2025-07-03 01:43:26', 'as', 'as', 'as'),
(6, 20, 4, 1, NULL, NULL, '2025-07-03 01:44:59', 'asd', 'asd', 'asd'),
(7, 6, 2, 1, NULL, NULL, '2025-07-03 02:03:35', 'asd', 'asd', 'asd'),
(8, 22, 5, 1, NULL, NULL, '2025-07-04 00:32:03', 'You have been diagnosed with:\r\n- Type 2 Diabetes Mellitus\r\n- Hypertension (Stage 1)\r\n', 'Metformin 500mg - 1 tablet - Twice daily', '- Maintain a low-sugar, low-sodium diet.\r\n- Engage in 30 minutes of brisk walking daily.\r\n- Monitor blood pressure and blood glucose at home regularly.\r\n- Schedule a follow-up appointment in 4 weeks.\r\n'),
(9, 13, 2, 1, NULL, NULL, '2025-07-04 12:55:32', 'Tooth problem', 'Some Medicine to cure tooth pain', 'Dont eat icecream');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bills_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD CONSTRAINT `patient_profiles_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
