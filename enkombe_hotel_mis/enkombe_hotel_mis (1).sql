-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2026 at 04:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enkombe_hotel_mis`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodation_units`
--

CREATE TABLE `accommodation_units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(100) DEFAULT NULL,
  `unit_type` varchar(100) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodation_units`
--

INSERT INTO `accommodation_units` (`unit_id`, `unit_name`, `unit_type`, `bedrooms`, `status`) VALUES
(1, 'A01', 'Luxury Suite', NULL, 'Ready'),
(2, 'A02', 'Luxury Suite', NULL, 'Occupied'),
(3, 'A03', 'Luxury Suite', NULL, 'Ready'),
(4, 'A04', 'Luxury Suite', NULL, 'Ready'),
(5, 'A05', 'Luxury Suite', NULL, 'Occupied'),
(6, 'B01', 'Executive Apartment', NULL, 'Ready'),
(7, 'B02', 'Executive Apartment', NULL, 'Maintenance'),
(8, 'B03', 'Executive Apartment', NULL, 'Ready'),
(9, 'B04', 'Executive Apartment', NULL, 'Occupied'),
(10, 'B05', 'Executive Apartment', NULL, 'Needs Cleaning'),
(11, 'C01', 'Family Apartment', NULL, 'Ready'),
(12, 'C02', 'Family Apartment', NULL, 'Ready'),
(13, 'C03', 'Family Apartment', NULL, 'Ready'),
(14, 'C04', 'Family Apartment', NULL, 'Ready'),
(15, 'C05', 'Family Apartment', NULL, 'Maintenance'),
(16, 'A01', 'Luxury Suite', 1, 'Ready'),
(17, 'A02', 'Luxury Suite', 1, 'Needs Cleaning'),
(18, 'A03', 'Luxury Suite', 1, 'Needs Cleaning'),
(19, 'A04', 'Luxury Suite', 1, 'Ready'),
(20, 'A05', 'Luxury Suite', 1, 'Maintenance'),
(21, 'A06', 'Luxury Suite', 1, 'Ready'),
(22, 'A07', 'Luxury Suite', 1, 'Occupied'),
(23, 'A08', 'Luxury Suite', 1, 'Ready'),
(24, 'A09', 'Luxury Suite', 1, 'Cleaning'),
(25, 'A10', 'Luxury Suite', 1, 'Ready'),
(26, 'B01', 'Executive Apartment', 2, 'Ready'),
(27, 'B02', 'Executive Apartment', 2, 'Occupied'),
(28, 'B03', 'Executive Apartment', 2, 'Ready'),
(29, 'B04', 'Executive Apartment', 2, 'Maintenance'),
(30, 'B05', 'Executive Apartment', 2, 'Ready'),
(31, 'B06', 'Executive Apartment', 2, 'Cleaning'),
(32, 'B07', 'Executive Apartment', 2, 'Occupied'),
(33, 'B08', 'Executive Apartment', 2, 'Ready'),
(34, 'B09', 'Executive Apartment', 2, 'Ready'),
(35, 'B10', 'Executive Apartment', 2, 'Ready'),
(36, 'C01', 'Family Apartment', 3, 'Ready'),
(37, 'C02', 'Family Apartment', 3, 'Occupied'),
(38, 'C03', 'Family Apartment', 3, 'Ready'),
(39, 'C04', 'Family Apartment', 3, 'Ready'),
(40, 'C05', 'Family Apartment', 3, 'Maintenance'),
(41, 'C06', 'Family Apartment', 3, 'Cleaning'),
(42, 'C07', 'Family Apartment', 3, 'Ready'),
(43, 'C08', 'Family Apartment', 3, 'Occupied'),
(44, 'C09', 'Family Apartment', 3, 'Ready'),
(45, 'C10', 'Family Apartment', 3, 'Ready'),
(46, 'C01', 'Family Apartment', 3, 'Ready'),
(47, 'Master', 'Suite', NULL, 'Out of service');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action_type`, `details`, `created_at`) VALUES
(1, 1, 'LOGOUT', 'User logged out.', '2026-11-17 17:01:43'),
(2, 1, 'LOGOUT', 'User logged out.', '2026-11-17 17:02:28'),
(3, 1, 'LOGOUT', 'User logged out.', '2026-11-19 14:54:41'),
(4, 1, 'LOGOUT', 'User logged out.', '2026-11-19 15:00:09'),
(5, 11, 'LOGOUT', 'User logged out.', '2026-11-19 15:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `housekeeping_tasks`
--

CREATE TABLE `housekeeping_tasks` (
  `task_id` int(11) NOT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `assigned_staff` int(11) DEFAULT NULL,
  `task_status` varchar(50) DEFAULT NULL,
  `date_assigned` date DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `inspection_status` varchar(30) DEFAULT 'Not Inspected'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `housekeeping_tasks`
--

INSERT INTO `housekeeping_tasks` (`task_id`, `room_number`, `assigned_staff`, `task_status`, `date_assigned`, `completion_notes`, `inspection_status`) VALUES
(1, 'A03', 4, 'Completed', '2026-11-10', NULL, 'Not Inspected'),
(2, 'B05', 5, 'In Progress', '2026-11-10', NULL, 'Not Inspected'),
(3, 'C02', 8, 'Completed', '2026-11-09', NULL, 'Not Inspected'),
(4, 'A01', 4, 'Completed', '2026-11-09', NULL, 'Not Inspected'),
(5, 'B03', 5, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(6, 'C04', 8, 'In Progress', '2026-11-10', NULL, 'Not Inspected'),
(7, 'A03', 4, 'Completed', '2026-11-10', 'mopping and dusting complete', 'Not Inspected'),
(8, 'A09', 5, 'In Progress', '2026-11-10', NULL, 'Not Inspected'),
(9, 'B06', 8, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(10, 'C06', 4, 'In Progress', '2026-11-10', NULL, 'Not Inspected'),
(11, 'A02', 5, 'Completed', '2026-11-08', NULL, 'Not Inspected'),
(12, 'B02', 4, 'Completed', '2026-11-08', NULL, 'Not Inspected'),
(13, 'C02', 8, 'Completed', '2026-11-08', NULL, 'Not Inspected'),
(14, 'A05', 4, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(15, 'B04', 5, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(16, 'C05', 8, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(17, 'A01', 4, 'Completed', '2026-11-07', NULL, 'Not Inspected'),
(18, 'A07', 5, 'Completed', '2026-11-07', NULL, 'Not Inspected'),
(19, 'B01', 8, 'Completed', '2026-11-07', NULL, 'Not Inspected'),
(20, 'B03', 4, 'Completed', '2026-11-07', NULL, 'Not Inspected'),
(21, 'C03', 5, 'Completed', '2026-11-07', NULL, 'Not Inspected'),
(22, 'A04', 10, 'Pending', '2026-11-10', NULL, 'Not Inspected'),
(23, 'A03', 17, 'Pending', '2026-11-13', NULL, 'Not Inspected'),
(24, 'A02', 17, 'Pending', '2026-11-13', NULL, 'Not Inspected'),
(25, 'A03', 4, 'Completed', '2026-11-19', NULL, 'Not Inspected');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `location_name` varchar(100) DEFAULT NULL,
  `issue_description` text DEFAULT NULL,
  `priority_level` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `assigned_staff` int(11) DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `inspection_status` varchar(30) DEFAULT 'Not Inspected'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`request_id`, `location_name`, `issue_description`, `priority_level`, `status`, `report_date`, `assigned_staff`, `completion_notes`, `inspection_status`) VALUES
(1, 'B02', 'Air conditioner not cooling', 'High', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected'),
(2, 'C05', 'Bathroom leak', 'Medium', 'In Progress', '2026-11-10', NULL, NULL, 'Not Inspected'),
(3, 'A02', 'TV not working', 'Low', 'Completed', '2026-11-10', NULL, NULL, 'Not Inspected'),
(4, 'B04', 'Faulty lighting', 'Medium', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected'),
(5, 'A05', 'Door lock damaged', 'High', 'Assigned', '2026-11-10', NULL, NULL, 'Not Inspected'),
(6, 'A05', 'Air conditioner not cooling', 'High', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected'),
(7, 'B04', 'Water heater malfunction', 'Medium', 'In Progress', '2026-11-10', NULL, NULL, 'Not Inspected'),
(8, 'C05', 'Bathroom leakage', 'High', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected'),
(9, 'A02', 'Television not working', 'Low', 'Completed', '2026-11-10', NULL, NULL, 'Not Inspected'),
(10, 'B02', 'Broken door handle', 'Medium', 'Completed', '2026-11-10', NULL, NULL, 'Not Inspected'),
(11, 'C02', 'WiFi connectivity issue', 'Low', 'Completed', '2026-11-10', NULL, NULL, 'Not Inspected'),
(12, 'A07', 'Electrical socket fault', 'High', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected'),
(13, 'B08', 'Window lock damaged', 'Medium', 'Assigned', '2026-11-10', NULL, NULL, 'Not Inspected'),
(14, 'C08', 'Ceiling light replacement', 'Low', 'Assigned', '2026-11-10', NULL, NULL, 'Not Inspected'),
(15, 'A09', 'Mini fridge not cooling', 'Medium', 'Open', '2026-11-10', NULL, NULL, 'Not Inspected');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `fullname`, `phone`, `department`, `role`, `email`) VALUES
(4, 'Grace Achieng', '0700444444', 'Room Attendant', NULL, NULL),
(5, 'Mercy Atim', '0700555555', 'Room Attendant', NULL, NULL),
(6, 'Paul Okello', '0700666666', 'Technician', NULL, NULL),
(7, 'Robert Mugisha', '0700777777', 'Electrician', NULL, NULL),
(8, 'Patricia Nansubuga', '0700888888', 'Room Attendant', NULL, NULL),
(9, 'Michael Kato', '0701001001', 'Management', 'General Manager', 'michael@enkombe.com'),
(10, 'Sarah Namusoke', '0701001002', 'Housekeeping', 'Supervisor', 'sarah@enkombe.com'),
(11, 'John Ssemanda', '0701001003', 'Maintenance', 'Supervisor', 'john@enkombe.com'),
(12, 'Grace Achieng', '0701001004', 'Housekeeping', 'Attendant', 'grace@enkombe.com'),
(13, 'Mercy Atim', '0701001005', 'Housekeeping', 'Attendant', 'mercy@enkombe.com'),
(14, 'Robert Mugisha', '0701001006', 'Maintenance', 'Technician', 'robert@enkombe.com'),
(15, 'Paul Okello', '0701001007', 'Maintenance', 'Electrician', 'paul@enkombe.com'),
(16, 'Patricia Nansubuga', '0701001008', 'Housekeeping', 'Attendant', 'patricia@enkombe.com'),
(17, 'Brian Kintu', '0701001009', 'Operations', 'Officer', 'brian@enkombe.com'),
(18, 'James Balikuddembe', '0701001010', 'Maintenance', 'Technician', 'james@enkombe.com'),
(19, 'Dukundane Angel', NULL, 'Management', 'secretary', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `staff_id`) VALUES
(1, 'admin', '$2y$10$49bYPOw8.8m6mpEokYMbl.XMfsQrkA72nE91zIkoDGyiKNp5o6FaW', 'Manager', NULL),
(2, 'housekeeping', 'house123', 'Housekeeping Supervisor', NULL),
(3, 'maintenance', 'maint123', 'Maintenance Supervisor', NULL),
(4, 'admin', 'admin123', 'Manager', NULL),
(5, 'housekeep1', 'house123', 'Housekeeping', NULL),
(6, 'maint1', 'maint123', 'Maintenance', NULL),
(7, 'manager1', 'manager123', 'Supervisor', NULL),
(8, 'operations1', 'ops123', 'Operations', NULL),
(9, 'JamesB', 'admin1234', 'Housekeeping Supervisor', NULL),
(10, 'james', 'james123', 'Maintenance Supervisor', NULL),
(11, 'grace_R', '$2y$10$SmozG4VCQOh2xr0bChSIBuPrAnsgoSN1FyY50Lwht1E5Bbc.7pScu', 'Room Attendant', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodation_units`
--
ALTER TABLE `accommodation_units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `assigned_staff` (`assigned_staff`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `fk_maintenance_staff` (`assigned_staff`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodation_units`
--
ALTER TABLE `accommodation_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD CONSTRAINT `housekeeping_tasks_ibfk_1` FOREIGN KEY (`assigned_staff`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `fk_maintenance_staff` FOREIGN KEY (`assigned_staff`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
