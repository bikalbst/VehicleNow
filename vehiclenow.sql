-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2025 at 07:41 AM
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
-- Database: `vehiclenow`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_car_availability` (IN `car_id` INT, IN `start_date` DATE, IN `end_date` DATE, OUT `is_available` BOOLEAN)   BEGIN
    DECLARE booking_count INT;
    
    SELECT COUNT(*) INTO booking_count 
    FROM booking 
    WHERE CAR_ID = car_id 
      AND STATUS != 'Cancelled'
      AND ((FROM_DT <= end_date AND TO_DT >= start_date)
           OR (FROM_DT <= start_date AND TO_DT >= start_date)
           OR (FROM_DT <= end_date AND TO_DT >= end_date));
    
    IF booking_count > 0 OR 
       (SELECT AVAILABLE FROM cars WHERE CAR_ID = car_id) = 'N' THEN
        SET is_available = FALSE;
    ELSE
        SET is_available = TRUE;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_car_availability` ()   BEGIN
    -- Mark cars as unavailable if they have active bookings for current date
    UPDATE cars c
    SET c.AVAILABLE = 'N'
    WHERE EXISTS (
        SELECT 1
        FROM booking b
        WHERE b.CAR_ID = c.CAR_ID
        AND b.STATUS IN ('Confirmed', 'Pending')
        AND CURDATE() BETWEEN b.FROM_DT AND b.TO_DT
    );
    
    -- Mark cars as available if they don't have active bookings for current date
    UPDATE cars c
    SET c.AVAILABLE = 'Y'
    WHERE NOT EXISTS (
        SELECT 1
        FROM booking b
        WHERE b.CAR_ID = c.CAR_ID
        AND b.STATUS IN ('Confirmed', 'Pending')
        AND CURDATE() BETWEEN b.FROM_DT AND b.TO_DT
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `BOOK_ID` int(11) NOT NULL,
  `CAR_ID` int(11) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `BOOK_PLACE` varchar(25) NOT NULL,
  `DURATION` int(10) NOT NULL,
  `DESTINATION` varchar(25) NOT NULL,
  `BOOK_DATE` date NOT NULL,
  `FROM_DT` date NOT NULL,
  `TO_DT` date NOT NULL,
  `AMOUNT` decimal(10,2) NOT NULL,
  `STATUS` varchar(20) DEFAULT 'Pending',
  `PAYMENT_METHOD` varchar(50) DEFAULT 'Cash',
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`BOOK_ID`, `CAR_ID`, `EMAIL`, `BOOK_PLACE`, `DURATION`, `DESTINATION`, `BOOK_DATE`, `FROM_DT`, `TO_DT`, `AMOUNT`, `STATUS`, `PAYMENT_METHOD`, `CREATED_AT`) VALUES
(2, 4, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-01', '2025-06-03', 18000.00, 'RETURNED', 'Card', '2025-06-01 12:58:51'),
(3, 8, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-25', '2025-06-27', 16500.00, 'RETURNED', 'Cash', '2025-06-01 13:10:30'),
(4, 7, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-02', '2025-06-03', 15000.00, 'RETURNED', 'Cash', '2025-06-01 13:20:38'),
(5, 6, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-02', '2025-06-03', 9600.00, 'RETURNED', 'Cash', '2025-06-01 13:22:44'),
(7, 3, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-02', '2025-06-06', 40000.00, 'RETURNED', 'Cash', '2025-06-01 13:44:30'),
(8, 10, 'bst@bst.com', '', 0, '', '2025-06-01', '2025-06-02', '2025-06-05', 60000.00, 'RETURNED', 'UPI', '2025-06-01 21:33:35'),
(9, 3, 'bst@bst.com', '', 0, '', '2025-06-06', '2025-06-13', '2025-06-18', 48000.00, 'APPROVED', 'Cash', '2025-06-06 08:44:30'),
(10, 4, 'bst2@bst.com', '', 0, '', '2025-06-06', '2025-06-06', '2025-06-19', 84000.00, 'APPROVED', 'Cash', '2025-06-06 09:06:35'),
(11, 10, 'bst2@bst.com', 'Charali', 2, 'RH', '2025-06-06', '2025-06-12', '2025-06-14', 45000.00, 'APPROVED', 'Cash', '2025-06-06 09:21:06'),
(12, 9, 'bst2@bst.com', '', 0, '', '2025-06-06', '2025-06-07', '2025-06-08', 20000.00, 'Pending', 'Cash', '2025-06-06 11:10:12'),
(13, 9, 'bst2@bst.com', '', 0, '', '2025-06-06', '2025-06-07', '2025-06-08', 20000.00, 'Pending', 'Card', '2025-06-06 11:10:42'),
(14, 9, 'bst2@bst.com', '', 0, '', '2025-06-06', '2025-06-07', '2025-06-08', 20000.00, 'Pending', 'UPI', '2025-06-06 11:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `CAR_ID` int(11) NOT NULL,
  `CAR_NAME` varchar(100) NOT NULL,
  `CAR_IMG` varchar(255) NOT NULL,
  `FUEL_TYPE` varchar(50) NOT NULL,
  `CAPACITY` int(11) NOT NULL,
  `CAR_TYPE` varchar(11) NOT NULL,
  `TRANSMISSION` varchar(50) NOT NULL,
  `MODEL_YEAR` int(11) NOT NULL,
  `PRICE` decimal(10,2) NOT NULL,
  `AVAILABLE` char(1) DEFAULT 'Y',
  `ADDED_DATE` datetime DEFAULT current_timestamp(),
  `DESCRIPTION` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`CAR_ID`, `CAR_NAME`, `CAR_IMG`, `FUEL_TYPE`, `CAPACITY`, `CAR_TYPE`, `TRANSMISSION`, `MODEL_YEAR`, `PRICE`, `AVAILABLE`, `ADDED_DATE`, `DESCRIPTION`) VALUES
(2, 'Honda Civic', 'civic.jpg', 'Petrol', 5, '0', 'Manual', 2021, 4500.00, 'Y', '2025-06-01 11:51:10', 'Reliable and sporty compact car'),
(3, 'Ford Mustang', 'mustang.jpg', 'Petrol', 4, '0', 'Automatic', 2022, 8000.00, 'N', '2025-06-01 11:51:10', 'Iconic American muscle car with powerful engine'),
(4, 'Hyundai Tucson', 'tucson.jpg', 'Diesel', 5, '0', 'Automatic', 2021, 6000.00, 'N', '2025-06-01 11:51:10', 'Compact SUV with modern features and comfort'),
(6, 'Tata Nexon', 'nexon.jpg', 'Diesel', 5, '0', 'Manual', 2021, 4800.00, 'Y', '2025-06-01 11:51:10', 'Compact SUV with excellent safety features'),
(7, 'Mahindra Thar', 'thar.jpg', 'Diesel', 4, '0', 'Manual', 2021, 7500.00, 'Y', '2025-06-01 11:51:10', 'Off-road capable SUV with rugged design'),
(8, 'Kia Seltos', 'seltos.jpg', 'Petrol', 5, '0', 'Automatic', 2022, 5500.00, 'Y', '2025-06-01 11:51:10', 'Feature-rich compact SUV with premium feel'),
(9, 'Hyundai Venue	', 'IMG-683c3adef10189.95662091.jpg', 'Petrol', 5, '', '', 0, 10000.00, 'N', '2025-06-01 17:19:54', NULL),
(10, 'Tesla Model 3	', 'IMG-683c3d83350171.62683238.jpg', 'Electric', 5, 'Sedan', 'Automatic', 2024, 15000.00, 'N', '2025-06-01 17:31:11', NULL),
(11, 'Toyota Fortuner', 'IMG-68427137bcd919.67611002.jpg', 'Diesel', 7, 'SUV', 'Manual', 2022, 9500.00, 'Y', '2025-06-06 10:25:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dash`
--

CREATE TABLE `dash` (
  `total_users` int(10) NOT NULL,
  `total_vehicles` int(100) NOT NULL,
  `total_bookings` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int(11) NOT NULL,
  `FNAME` varchar(50) NOT NULL,
  `LNAME` varchar(50) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `PHONE` varchar(15) NOT NULL,
  `ADDRESS` varchar(30) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `LICENSE_NO` varchar(30) NOT NULL,
  `CREATION_DATE` datetime DEFAULT current_timestamp(),
  `IS_ADMIN` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `FNAME`, `LNAME`, `EMAIL`, `PHONE`, `ADDRESS`, `PASSWORD`, `LICENSE_NO`, `CREATION_DATE`, `IS_ADMIN`) VALUES
(1, 'Admin', 'User', 'admin@example.com', '1234567890', '', '0192023a7bbd73250516f069df18b500', '', '2025-06-01 11:51:10', 'Y'),
(2, 'bst', 'thakuri', 'bst@bst.com', '980569443', 'charali', '6527b7edd158a9d9568888a83a53c54e', 'bso9dk', '2025-06-01 12:06:39', 'N'),
(3, 'bst2', 'thakuri2', 'bst2@bst.com', '9809876787', 'charali', '6527b7edd158a9d9568888a83a53c54e', 'bhok89', '2025-06-06 09:06:05', 'N');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`BOOK_ID`),
  ADD KEY `CAR_ID` (`CAR_ID`),
  ADD KEY `EMAIL` (`EMAIL`),
  ADD KEY `idx_booking_status` (`STATUS`),
  ADD KEY `idx_booking_dates` (`FROM_DT`,`TO_DT`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`CAR_ID`),
  ADD KEY `idx_car_available` (`AVAILABLE`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `BOOK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `CAR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`CAR_ID`) REFERENCES `cars` (`CAR_ID`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`EMAIL`) REFERENCES `users` (`EMAIL`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_car_availability_daily` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 11:51:10' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL update_car_availability();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
