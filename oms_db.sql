-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 03:52 PM
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
-- Database: `oms.db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(50) DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Name`, `Email`, `Password`, `Role`) VALUES
(1, 'Areeba Naz', 'areebanaz661@gmail.com', '$2y$10$AGX6ni4jXz1APUz7YhHiZOWVoWKl3OgkD3c00ExXNDRBv7kdjtfIa', 'Admin'),
(2, 'MUHAMMAD BILAL', 'bilalaamir661@gmail.com', '$2y$10$7WNQpFWaM4ABbvqpiDlIX.jEeu/CZ0yy77j5aebe/VoBl9hR.CSnW', 'Admin'),
(3, 'MUHAMMAD BILAL', 'bilalaamir@gmail.com', '$2y$10$J9.9eNK42LAdwCD5dFvV/OlB6esT4K9pZ1/L/10HSe5X2Ncgy93o6', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `Notification_ID` int(11) NOT NULL,
  `Passenger_ID` int(11) DEFAULT NULL,
  `Title` varchar(150) NOT NULL,
  `Message` text NOT NULL,
  `Type` enum('Arrival','Departure','Delay','General') DEFAULT 'General',
  `Is_Read` tinyint(1) DEFAULT 0,
  `Created_At` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`Notification_ID`, `Passenger_ID`, `Title`, `Message`, `Type`, `Is_Read`, `Created_At`) VALUES
(1, 4, 'Train Arriving Soon', 'Your train will arrive at the station in approximately 15 minutes. Please proceed to the platform.', 'Arrival', 1, '2026-07-07 07:46:05'),
(2, 5, 'Shalimar express', 'jhqwdnoiQW', 'Departure', 0, '2026-07-13 11:41:49'),
(3, 4, 'Train Delayed', 'Your train is delayed. We apologize for the inconvenience. Updated departure time will be announced shortly.', 'Delay', 1, '2026-07-13 23:49:21'),
(4, 5, 'Train Delayed', 'Your train is delayed. We apologize for the inconvenience. Updated departure time will be announced shortly.', 'Delay', 0, '2026-07-13 23:49:21'),
(5, 6, 'Train Delayed', 'Your train is delayed. We apologize for the inconvenience. Updated departure time will be announced shortly.', 'Delay', 0, '2026-07-13 23:49:21'),
(6, 4, 'DELAY', 'JB;J.N', 'Departure', 1, '2026-07-14 00:00:16'),
(7, 5, 'DELAY', 'JB;J.N', 'Departure', 0, '2026-07-14 00:00:16'),
(8, 6, 'DELAY', 'JB;J.N', 'Departure', 0, '2026-07-14 00:00:16'),
(10, 5, 'DELAY', 'JB;J.N', 'Departure', 0, '2026-07-14 00:01:12'),
(11, 6, 'DELAY', 'JB;J.N', 'Departure', 0, '2026-07-14 00:01:12'),
(12, 7, 'arrival', 'THE QUICK BROWN FOX JUMP OVER THELADY DOG', 'General', 0, '2026-07-14 06:50:48'),
(13, 4, 'DEPARTURE', 'THE QUICK BROWN FOX JUMP OVER THE LADY DOG', 'General', 0, '2026-07-14 06:51:19'),
(14, 5, 'DEPARTURE', 'THE QUICK BROWN FOX JUMP OVER THE LADY DOG', 'General', 0, '2026-07-14 06:51:19'),
(15, 6, 'DEPARTURE', 'THE QUICK BROWN FOX JUMP OVER THE LADY DOG', 'General', 0, '2026-07-14 06:51:19');

-- --------------------------------------------------------

--
-- Table structure for table `passenger`
--

CREATE TABLE `passenger` (
  `Passenger_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `CNIC` varchar(20) NOT NULL,
  `Register_Date` datetime NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passenger`
--

INSERT INTO `passenger` (`Passenger_ID`, `Name`, `Email`, `Phone`, `Password`, `CNIC`, `Register_Date`, `reset_token`, `reset_token_expiry`) VALUES
(4, 'Areeba Naz', 'areebanaz661@gmail.com', '03227062942', '$2y$10$6HQB6HuYa8qwFcMwmdtcn.SSANVr/Bc6iWj4I2pfiCDWkdfGPzuqC', '33100-0115676-0', '2026-06-21 02:20:19', NULL, NULL),
(5, 'USER', 'USER@gmail.com', '4532543', '$2y$10$hqchWwldWCnbYhIg.51EVuX9sk3cGramj25MhckRUVVVa8FIgygv.', '33100-0117676-0', '2026-07-11 02:04:01', NULL, NULL),
(6, 'MUHAMMAD BILAL', 'bilalaamir@gmail.com', '03287006327', '$2y$10$fJV/8dx5ydXLVYkz2xzituu1xT7ngDuwnvHdub4EQGMEVYtFpzNJS', '33100-5721366-1', '2026-07-13 12:34:22', NULL, NULL),
(7, 'ali', 'ali11@gmail.com', '0333-6689563', '$2y$10$RhU2K/AQIdCPfzCzw5lyp.pC1hDtMYd5WD0jgvr.Lq5LTqH.4lrBO', '33104-5802249-5', '2026-07-14 04:31:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Ticket_ID` int(11) NOT NULL,
  `Method` varchar(50) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Payment_Date` datetime NOT NULL,
  `Transaction_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Ticket_ID`, `Method`, `Amount`, `Status`, `Payment_Date`, `Transaction_ID`) VALUES
(1, 1, 'Cash', 500.00, 'Completed', '2026-06-24 11:38:40', 'TXN-6A3C2430E996D'),
(3, 3, 'Cash', 1250.00, 'Completed', '2026-07-02 04:23:36', 'TXN-6A464A384BA6B'),
(5, 5, 'EasyPaisa', 900.00, 'Completed', '2026-07-13 12:45:12', 'TXN-6A55404844485'),
(6, 6, 'Cash', 1250.00, 'Completed', '2026-07-13 12:47:58', 'TXN-6A5540EE0ECFC'),
(7, 7, 'EasyPaisa', 500.00, 'Completed', '2026-07-14 02:51:43', 'TXN-6A5606AF2F1B5'),
(8, 8, 'Cash', 500.00, 'Completed', '2026-07-14 06:31:25', 'TXN-6A563A2DA2E04');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `Schedule_ID` int(11) NOT NULL,
  `Train_ID` int(11) NOT NULL,
  `Station_ID` int(11) NOT NULL,
  `Arrival_Time` time NOT NULL,
  `Departure_Time` time NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`Schedule_ID`, `Train_ID`, `Station_ID`, `Arrival_Time`, `Departure_Time`, `Date`) VALUES
(59, 17, 15, '16:42:00', '20:42:00', '2026-07-14'),
(60, 17, 15, '16:42:00', '20:42:00', '2026-07-14'),
(61, 1, 14, '03:46:00', '05:47:00', '2026-07-15');

-- --------------------------------------------------------

--
-- Table structure for table `station`
--

CREATE TABLE `station` (
  `Station_ID` int(11) NOT NULL,
  `Station_Name` varchar(100) NOT NULL,
  `City` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `station`
--

INSERT INTO `station` (`Station_ID`, `Station_Name`, `City`) VALUES
(5, 'Karachi City', ''),
(6, 'Hyderabad', ''),
(7, 'Rohri', ''),
(8, 'Bahawalpur', ''),
(9, 'Multan', ''),
(10, 'Lahore', ''),
(11, 'Rawalpindi', ''),
(12, 'Islamabad', ''),
(13, 'Peshawar', ''),
(14, 'Quetta', ''),
(15, 'Faisalabad', ''),
(16, 'Sialkot', ''),
(17, 'Sukkur', ''),
(18, 'Sahiwal', ''),
(19, 'Gujranwala', '');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `Ticket_ID` int(11) NOT NULL,
  `Passenger_ID` int(11) NOT NULL,
  `Train_ID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Seat_No` varchar(10) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`Ticket_ID`, `Passenger_ID`, `Train_ID`, `Date`, `Seat_No`, `Price`, `Status`) VALUES
(1, 4, 17, '2026-06-25', '1A', 500.00, 'Cancelled'),
(3, 4, 17, '2026-07-02', '1A', 1250.00, 'Booked'),
(5, 6, 4, '2026-07-16', '24A', 900.00, 'Booked'),
(6, 6, 4, '2026-07-16', '17A', 1250.00, 'Booked'),
(7, 4, 4, '2026-07-15', '24A', 500.00, 'Booked'),
(8, 4, 17, '2026-07-14', '17A', 500.00, 'Booked');

-- --------------------------------------------------------

--
-- Table structure for table `train`
--

CREATE TABLE `train` (
  `Train_ID` int(11) NOT NULL,
  `Train_Name` varchar(100) NOT NULL,
  `Route` varchar(100) NOT NULL,
  `TotalSeats` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `train`
--

INSERT INTO `train` (`Train_ID`, `Train_Name`, `Route`, `TotalSeats`, `Status`) VALUES
(1, 'Orange Line Express', 'Lahore to Orange Line Station', 200, 'active'),
(2, 'Green Line Metro', 'Islamabad to Rawalpindi', 150, 'Active'),
(3, 'Blue Line Express', 'Karachi to Malir', 180, 'Active'),
(4, 'Red Line Metro', 'Lahore to Multan', 250, 'Active'),
(5, 'Yellow Line', 'Islamabad to Peshawar', 200, 'Active'),
(17, 'Shalimar Express', 'Faisalabad - Lahore', 250, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`Notification_ID`),
  ADD KEY `Passenger_ID` (`Passenger_ID`);

--
-- Indexes for table `passenger`
--
ALTER TABLE `passenger`
  ADD PRIMARY KEY (`Passenger_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Ticket_ID` (`Ticket_ID`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`Schedule_ID`),
  ADD KEY `Train_ID` (`Train_ID`),
  ADD KEY `Station_ID` (`Station_ID`);

--
-- Indexes for table `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`Station_ID`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`Ticket_ID`),
  ADD KEY `Passenger_ID` (`Passenger_ID`),
  ADD KEY `Train_ID` (`Train_ID`);

--
-- Indexes for table `train`
--
ALTER TABLE `train`
  ADD PRIMARY KEY (`Train_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `passenger`
--
ALTER TABLE `passenger`
  MODIFY `Passenger_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `Schedule_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `station`
--
ALTER TABLE `station`
  MODIFY `Station_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `train`
--
ALTER TABLE `train`
  MODIFY `Train_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`Passenger_ID`) REFERENCES `passenger` (`Passenger_ID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Ticket_ID`) REFERENCES `ticket` (`Ticket_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`Train_ID`) REFERENCES `train` (`Train_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`Station_ID`) REFERENCES `station` (`Station_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`Passenger_ID`) REFERENCES `passenger` (`Passenger_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`Train_ID`) REFERENCES `train` (`Train_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
