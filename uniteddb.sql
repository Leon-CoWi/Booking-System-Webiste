-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2026 at 05:01 AM
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
-- Database: `uniteddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `Username`, `Password`) VALUES
(1, 'admin1', '$2y$10$obeh5FOnFCuTnwQjOCChcu1Ebcxb4BeRKmId7GK4yniEdbp0EO566'),
(2, 'admin2', '$2y$10$hYRtY2uZObg2azQhWOn1h.kh69yN76fefwpq572CRJR2YwWaYYESe'),
(3, 'admin3', '$2y$10$C2kHDgTSc0bsvxn3PfwIweQEMMMI3k3N61ZE9I0oHMA07bxs2bojG');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `RoomID` varchar(50) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `PaymentID` int(11) DEFAULT NULL,
  `ReservationType` varchar(50) NOT NULL,
  `CheckInDate` date NOT NULL,
  `CheckOutDate` date NOT NULL,
  `GuestNumber` int(11) NOT NULL,
  `Nights` int(11) NOT NULL,
  `Request` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`BookingID`, `AdminID`, `CustomerID`, `RoomID`, `Location`, `PaymentID`, `ReservationType`, `CheckInDate`, `CheckOutDate`, `GuestNumber`, `Nights`, `Request`) VALUES
(1, NULL, 1, 'CF-3', 'Casa FAM Appartelle', 1, 'Family Room', '2026-05-15', '2026-05-16', 5, 1, ''),
(2, 1, 2, 'CF-1', 'Casa FAM Appartelle', 2, 'Active', '2026-05-15', '2026-05-16', 1, 1, NULL),
(3, NULL, 3, 'CF-2', 'Casa FAM Appartelle', 3, 'Deluxe Family Suite Room', '2026-05-15', '2026-05-16', 7, 1, ''),
(4, NULL, 4, 'CF-1', 'Casa FAM Appartelle', 4, 'Deluxe Family Suite Room', '2026-05-22', '2026-05-23', 1, 1, ''),
(5, NULL, 5, 'CF-2', 'Casa FAM Appartelle', 5, 'Deluxe Family Suite Room', '2026-05-22', '2026-05-23', 1, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `branch_images`
--

CREATE TABLE `branch_images` (
  `ImageID` int(11) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_images`
--

INSERT INTO `branch_images` (`ImageID`, `Location`, `ImagePath`, `SortOrder`) VALUES
(1, 'homepage', 'uploads/homepage/1777706966_cf1.jpg', 1),
(2, 'homepage', 'uploads/homepage/1777706990_IMG20260411104412.jpg', 2),
(3, 'logos_vf', 'uploads/logos/vf/1777780376_ChatGPT Image May 3, 2026, 10_35_30 AM.png', 1),
(4, 'logos_casa', 'uploads/logos/casa/1777785770_casa_fam_circle(2).png', 1),
(5, 'website_logo', 'uploads/logos/website/1777797776_681171349_939350335583143_7550896044686712276_n - Edited.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL,
  `CustomerName` varchar(100) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `PhoneNumber` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerID`, `CustomerName`, `Email`, `PhoneNumber`) VALUES
(1, 'Vince Riton', 'vince.alfredc@gmail.com', '09456001652'),
(2, 'Vince Riton', 'vince.alfredc@gmail.com', '09456001652'),
(3, 'Vince Riton', 'vince.alfredc@gmail.com', '09456001652'),
(4, 'Vince Riton', 'vince.alfredc@gmail.com', '09456001652'),
(5, 'Vince Riton', 'vince.alfredc@gmail.com', '09456001652');

-- --------------------------------------------------------

--
-- Table structure for table `location_images`
--

CREATE TABLE `location_images` (
  `ImageID` int(11) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_images`
--

INSERT INTO `location_images` (`ImageID`, `Location`, `ImagePath`, `SortOrder`) VALUES
(1, 'V.F Riton Appartelle', 'uploads/aboutus/V_F_Riton_Appartelle/1777703559_IMG20260411104936.jpg', 1),
(2, 'V.F Riton Appartelle', 'uploads/location/V_F_Riton_Appartelle/1777703884_IMG20260411104936.jpg', 2),
(3, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705154_cf1.jpg', 1),
(4, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705168_IMG20260411104412.jpg', 2),
(5, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705188_d2.jpg', 3),
(6, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705209_ca2.jpg', 4),
(7, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705539_cf5.jpg', 5),
(8, 'Signature Elements', 'uploads/signature/Signature_Elements/1777705931_4.jpg', 6),
(16, 'Casa FAM Appartelle', 'uploads/location/Casa_FAM_Appartelle/1778774627_cf7.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `Location` varchar(50) NOT NULL,
  `RoomID` varchar(50) NOT NULL,
  `PaymentDate` date DEFAULT NULL,
  `Charges` decimal(10,2) DEFAULT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `DiscountAmount` int(11) DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `PaidAmount` decimal(10,2) NOT NULL,
  `PaymentStatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `CustomerID`, `Location`, `RoomID`, `PaymentDate`, `Charges`, `PaymentMethod`, `DiscountAmount`, `TotalAmount`, `PaidAmount`, `PaymentStatus`) VALUES
(1, 1, 'Casa FAM Appartelle', 'CF-3', '2026-05-15', 2500.00, 'GCash', 0, 2500.00, 0.00, 'Cancelled'),
(2, 2, 'Casa FAM Appartelle', 'CF-1', '2026-05-15', 2000.00, 'Cash', 0, 2000.00, 2000.00, 'Paid'),
(3, 3, 'Casa FAM Appartelle', 'CF-2', '2026-05-15', 2000.00, 'GCash', 0, 2400.00, 2400.00, 'Paid'),
(4, 4, 'Casa FAM Appartelle', 'CF-1', '2026-05-15', 2000.00, 'GCash', 0, 2000.00, 0.00, 'Pending'),
(5, 5, 'Casa FAM Appartelle', 'CF-2', '2026-05-15', 2000.00, 'GCash', 0, 2000.00, 0.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `ImageID` int(11) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `ImagePath` varchar(500) NOT NULL,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`ImageID`, `Location`, `ImagePath`, `SortOrder`) VALUES
(1, 'GCash', 'uploads/payment_details/1778799409_HaWQSRoB.jpg', 0),
(2, 'Metrobank', 'uploads/payment_details/1778799415_Mn6Yx8nv.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipts`
--

CREATE TABLE `payment_receipts` (
  `ReceiptID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `PaymentID` int(11) DEFAULT NULL,
  `ReceiptPath` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_receipts`
--

INSERT INTO `payment_receipts` (`ReceiptID`, `CustomerID`, `PaymentID`, `ReceiptPath`, `CreatedAt`) VALUES
(1, 1, 1, 'uploads/receipts/1/receipt_6a067ea7a9dc94.14908652.jpg', '2026-05-15 02:02:15'),
(2, 3, 3, 'uploads/receipts/3/receipt_6a06810aba4e93.58584085.jpg', '2026-05-15 02:12:26'),
(3, 4, 4, 'uploads/receipts/4/receipt_6a0682cf30a5f3.00572204.jpg', '2026-05-15 02:19:59'),
(4, 5, 5, 'uploads/receipts/5/receipt_6a068b6f99c9f0.86495145.jpg', '2026-05-15 02:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` varchar(50) NOT NULL,
  `RoomNumber` varchar(50) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `RoomType` varchar(50) NOT NULL,
  `BaseRate` decimal(10,2) NOT NULL,
  `MaxOccupancies` int(11) DEFAULT NULL,
  `BedConfiguration` text DEFAULT NULL,
  `NumberBathrooms` int(11) DEFAULT NULL,
  `RoomFeatures` mediumtext DEFAULT NULL,
  `RoomAmenities` mediumtext DEFAULT NULL,
  `ExtraGuestRate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `RoomNumber`, `Location`, `RoomType`, `BaseRate`, `MaxOccupancies`, `BedConfiguration`, `NumberBathrooms`, `RoomFeatures`, `RoomAmenities`, `ExtraGuestRate`) VALUES
('CF-1', 'Room 1', 'Casa FAM Appartelle', 'Deluxe Family Suite Room', 2000.00, 6, '2  Double Bed, 2 Single Bed', 1, 'Enjoy a comfortable and spacious stay in our Deluxe Family Suite Room, designed to accommodate up to six guests with ease. The room features two double beds and two single beds, making it ideal for families or groups seeking both space and flexibility. It is fully air-conditioned and supported by an electric fan for added comfort, while cable television and free WiFi keep you entertained and connected throughout your stay. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, ensures convenience and relaxation. Essentials such as linen, towels, closets, and a table are provided for a hassle-free experience.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-10', 'Casa 3', 'Casa FAM Appartelle', 'Premium Double Room', 1500.00, 3, '1 Double Bed', 1, 'Experience a refined and comfortable stay in our Premium Double Room, thoughtfully designed for up to two guests. The room features a cozy queen-sized bed and is fully air-conditioned with a split-type unit, ensuring a quiet and relaxing atmosphere. Guests can enjoy modern conveniences such as cable television and free WiFi, making it suitable for both leisure and rest. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, adds to the overall comfort and functionality of the space. As a premium room, this accommodation offers added touches that enhance your stay, including extra furniture that varies in each Casa room, giving every space its own unique character. This non-smoking room also provides access to a shared dining area with an electric kettle and fridge, along with essentials such as linen, towels, and a table.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned (Split Type), Table, Linen and Towels Provided, Private bathroom, Fridge Access, Extra Furniture.', 400.00),
('CF-11', 'Casa 4', 'Casa FAM Appartelle', 'Premium Double Room', 1500.00, 3, '1 Queen Bed', 1, 'Experience a refined and comfortable stay in our Premium Double Room, thoughtfully designed for up to two guests. The room features a cozy queen-sized bed and is fully air-conditioned with a split-type unit, ensuring a quiet and relaxing atmosphere. Guests can enjoy modern conveniences such as cable television and free WiFi, making it suitable for both leisure and rest. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, adds to the overall comfort and functionality of the space. As a premium room, this accommodation offers added touches that enhance your stay, including extra furniture that varies in each Casa room, giving every space its own unique character. This non-smoking room also provides access to a shared dining area with an electric kettle and fridge, along with essentials such as linen, towels, and a table.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned (Split Type), Table, Linen and Towels Provided, Private bathroom, Fridge Access, Extra Furniture.', 400.00),
('CF-2', 'Room 2', 'Casa FAM Appartelle', 'Deluxe Family Suite Room', 2000.00, 6, '2  Double Bed, 2 Single Bed', 1, 'Enjoy a comfortable and spacious stay in our Deluxe Family Suite Room, designed to accommodate up to six guests with ease. The room features two double beds and two single beds, making it ideal for families or groups seeking both space and flexibility. It is fully air-conditioned and supported by an electric fan for added comfort, while cable television and free WiFi keep you entertained and connected throughout your stay. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, ensures convenience and relaxation. Essentials such as linen, towels, closets, and a table are provided for a hassle-free experience.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-3', 'Room 3', 'Casa FAM Appartelle', 'Family Room', 2500.00, 6, '3 Double Bed, 1 Single Bed', 1, 'Enjoy a spacious and budget-friendly stay in our Family Room, perfect for groups of up to six guests at an affordable rate of 2500 per night. The room is furnished with two double beds and two single beds, providing ample sleeping space for families or friends traveling together. It is fully air-conditioned and supported by an electric fan to keep the environment cool and comfortable, while cable television and free WiFi ensure entertainment and connectivity throughout your stay. A private bathroom with a shower heater and bidet, along with a dedicated vanity area, adds convenience for everyday use. Ideal for family bonding or group trips, this non-smoking room offers a practical set of amenities for a hassle-free experience. Guests can enjoy access to a shared dining area with an electric kettle and fridge, while essentials such as linen, towels, closets, and a table are readily available.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-4', 'Room 4', 'Casa FAM Appartelle', 'Standard Studio Room', 1200.00, 3, '1 Double Bed, 1 Single Bed', 1, 'Enjoy a cozy and functional stay in our Standard Studio Room, designed to comfortably accommodate up to three guests. The room features one double bed and one single bed, making it a great choice for small families or friends traveling together. It is fully air-conditioned and supported by an electric fan to maintain a comfortable atmosphere, while cable television and free WiFi keep you entertained and connected. A private bathroom with a shower heater and bidet, along with a dedicated vanity area, adds convenience to your daily routine. Guests can access a shared dining area with an electric kettle and fridge, while necessities such as linen, towels, closets, and a table are readily available.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-5', 'Room 5', 'Casa FAM Appartelle', 'Standard Studio Room', 1200.00, 2, '1 Double Bed', 1, 'Enjoy a cozy and functional stay in our Standard Studio Room, designed to comfortably accommodate up to two guests. The room features one double bed, making it a great choice for couples or friends traveling together. It is fully air-conditioned and supported by an electric fan to maintain a comfortable atmosphere, while cable television and free WiFi keep you entertained and connected. A private bathroom with a shower heater and bidet, along with a dedicated vanity area, adds convenience to your daily routine. Guests can access a shared dining area with an electric kettle and fridge, while necessities such as linen, towels, closets, and a table are readily available.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-6', 'Room 6', 'Casa FAM Appartelle', 'Standard Studio Room', 1200.00, 3, '1 Double Bed, 1 Single Bed', 1, 'Enjoy a cozy and functional stay in our Standard Studio Room, designed to comfortably accommodate up to three guests. The room features one double bed and one single bed, making it a great choice for small families or friends traveling together. It is fully air-conditioned and supported by an electric fan to maintain a comfortable atmosphere, while cable television and free WiFi keep you entertained and connected. A private bathroom with a shower heater and bidet, along with a dedicated vanity area, adds convenience to your daily routine. Guests can access a shared dining area with an electric kettle and fridge, while necessities such as linen, towels, closets, and a table are readily available.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-7', 'Room 7', 'Casa FAM Appartelle', 'Standard Studio Room', 1200.00, 3, '1 Double Bed, 1 Single Bed', 1, 'Enjoy a cozy and functional stay in our Standard Studio Room, designed to comfortably accommodate up to three guests. The room features one double bed and one single bed, making it a great choice for small families or friends traveling together. It is fully air-conditioned and supported by an electric fan to maintain a comfortable atmosphere, while cable television and free WiFi keep you entertained and connected. A private bathroom with a shower heater and bidet, along with a dedicated vanity area, adds convenience to your daily routine. Guests can access a shared dining area with an electric kettle and fridge, while necessities such as linen, towels, closets, and a table are readily available.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned, Table, Linen and Towels Provided, Private bathroom, Fridge Access.', 400.00),
('CF-8', 'Casa 1', 'Casa FAM Appartelle', 'Premium Double Room', 1500.00, 3, '1 Queen Bed', 1, 'Experience a refined and comfortable stay in our Premium Double Room, thoughtfully designed for up to two guests. The room features a cozy queen-sized bed and is fully air-conditioned with a split-type unit, ensuring a quiet and relaxing atmosphere. Guests can enjoy modern conveniences such as cable television and free WiFi, making it suitable for both leisure and rest. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, adds to the overall comfort and functionality of the space. As a premium room, this accommodation offers added touches that enhance your stay, including extra furniture that varies in each Casa room, giving every space its own unique character. This non-smoking room also provides access to a shared dining area with an electric kettle and fridge, along with essentials such as linen, towels, and a table.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned (Split Type), Table, Linen and Towels Provided, Private bathroom, Fridge Access, Extra Furniture. ', 400.00),
('CF-9', 'Casa 2', 'Casa FAM Appartelle', 'Premium Double Room', 1500.00, 3, '1 Double Bed', 1, 'Experience a refined and comfortable stay in our Premium Double Room, thoughtfully designed for up to two guests. The room features a cozy queen-sized bed and is fully air-conditioned with a split-type unit, ensuring a quiet and relaxing atmosphere. Guests can enjoy modern conveniences such as cable television and free WiFi, making it suitable for both leisure and rest. A private bathroom equipped with a shower heater and bidet, along with a dedicated vanity area, adds to the overall comfort and functionality of the space. As a premium room, this accommodation offers added touches that enhance your stay, including extra furniture that varies in each Casa room, giving every space its own unique character. This non-smoking room also provides access to a shared dining area with an electric kettle and fridge, along with essentials such as linen, towels, and a table.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Shared Dining Area, Free Wifi, Vanity Area, Electric Fan, Included Electric Kettle in Shared Dining Area, Air Conditioned (Split Type), Table, Linen and Towels Provided, Private bathroom, Fridge Access, Extra Furniture.', 400.00),
('VF-1', 'Room 1', 'V.F Riton Appartelle', 'Superior Family Room', 2000.00, 6, '3 Double Bed', 1, 'Enjoy a comfortable and spacious stay in our Superior Family Room, designed to accommodate up to six guests with ease. The room is furnished with three double beds, making it ideal for families or groups seeking both comfort and convenience. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 400.00),
('VF-2', 'Room 2', 'V.F Riton Appartelle', 'Superior Family Room', 2000.00, 6, '3 Double Bed', 1, 'Enjoy a comfortable and spacious stay in our Superior Family Room, designed to accommodate up to six guests with ease. The room is furnished with three double beds, making it ideal for families or groups seeking both comfort and convenience. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 400.00),
('VF-3', 'Room 3', 'V.F Riton Appartelle', 'Superior Family Room', 2000.00, 6, '3 Double Bed', 1, 'Enjoy a comfortable and spacious stay in our Superior Family Room, designed to accommodate up to six guests with ease. The room is furnished with three double beds, making it ideal for families or groups seeking both comfort and convenience. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 400.00),
('VF-4', 'Room 4', 'V.F Riton Appartelle', 'Deluxe Quadruple Room', 1600.00, 4, '1 Double Bed, 2 Single Bed', 1, 'Enjoy a comfortable and well-appointed stay in our Deluxe Quadruple Room, designed to accommodate up to four guests. The room is furnished with one double bed and two single beds, making it ideal for small families or groups seeking both comfort and practicality. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 400.00),
('VF-5', 'Room 5', 'V.F Riton Appartelle', 'Deluxe Twin Room', 1500.00, 3, '1 Double Bed, 1 Single Bed', 1, 'Enjoy a comfortable and well-appointed stay in our Deluxe Twin Room, designed to accommodate up to three guests. The room is furnished with one double bed and one single bed, making it ideal for small groups or families seeking both comfort and practicality. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 300.00),
('VF-6', 'Room 6', 'V.F Riton Appartelle', 'Deluxe Quadruple Room', 1600.00, 4, '2 Double Bed', 1, 'Enjoy a comfortable and well-appointed stay in our Deluxe Quadruple Room, designed to accommodate up to four guests. The room is furnished with one double bed and two single beds, making it ideal for small families or groups seeking both comfort and practicality. It is fully air-conditioned and supported by an electric fan for added comfort. A 32-inch HD TV with cable television is provided for your entertainment, along with free Wi-Fi to keep you connected throughout your stay. The room also features a private bathroom equipped with a shower heater and bidet, as well as a dedicated vanity area for added convenience. Essentials such as linen, towels, closets, and a table are provided to ensure a hassle-free and relaxing experience. A refrigerator is available upon request for an additional fee.', 'Smoke detectors, Shower with Heater, Bidet, Closets in room, 32 Inch HD TV with Cable, Dining Area, Free Wi-Fi, Vanity Area, Electric Fan, Electric Kettle, Air conditioned, Table, Linen and Towels Provided, Private bathroom, Refrigerator by Request (100 per day).', 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `ImageID` int(11) NOT NULL,
  `RoomID` varchar(50) NOT NULL,
  `ImagePath` varchar(255) DEFAULT NULL,
  `ImageName` varchar(255) DEFAULT NULL,
  `ImageOrder` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`ImageID`, `RoomID`, `ImagePath`, `ImageName`, `ImageOrder`) VALUES
(1, 'VF-1', 'uploads/rooms/VF-1/room_69f44e5085a936.13650356.jpg', '1.jpg', 1),
(2, 'VF-1', 'uploads/rooms/VF-1/room_69f44e50869043.23757946.jpg', '2.jpg', 2),
(3, 'VF-1', 'uploads/rooms/VF-1/room_69f44e5086e5d1.10761857.jpg', '3.jpg', 3),
(4, 'VF-1', 'uploads/rooms/VF-1/room_69f44e56e5dec0.76662447.jpg', '4.jpg', 4),
(5, 'VF-2', 'uploads/rooms/VF-2/room_69f44e5e880304.85576983.jpg', '1.jpg', 1),
(6, 'VF-2', 'uploads/rooms/VF-2/room_69f44e5e893257.91582817.jpg', '2.jpg', 2),
(7, 'VF-2', 'uploads/rooms/VF-2/room_69f44e5e89d785.72717014.jpg', '3.jpg', 3),
(8, 'VF-2', 'uploads/rooms/VF-2/room_69f44e65b87ac4.28576113.jpg', '4.jpg', 4),
(9, 'VF-3', 'uploads/rooms/VF-3/room_69f44e77d51b90.72648311.jpg', '1.jpg', 1),
(10, 'VF-3', 'uploads/rooms/VF-3/room_69f44e77d65b89.64001023.jpg', '2.jpg', 2),
(11, 'VF-3', 'uploads/rooms/VF-3/room_69f44e77d75187.24394653.jpg', '3.jpg', 3),
(12, 'VF-3', 'uploads/rooms/VF-3/room_69f44e7df41d23.45654949.jpg', '4.jpg', 4),
(13, 'VF-4', 'uploads/rooms/VF-4/room_69f44e8b1d3c28.40655153.jpg', '1.jpg', 1),
(14, 'VF-4', 'uploads/rooms/VF-4/room_69f44e8b1dea74.26256584.jpg', '2.jpg', 2),
(15, 'VF-4', 'uploads/rooms/VF-4/room_69f44e8b1e36e0.72684309.jpg', '3.jpg', 3),
(16, 'VF-4', 'uploads/rooms/VF-4/room_69f44e92917029.78865700.jpg', '4.jpg', 4),
(17, 'VF-5', 'uploads/rooms/VF-5/room_69f44ea220ad78.70352200.jpg', '3.jpg', 3),
(18, 'VF-5', 'uploads/rooms/VF-5/room_69f44ea2214bf2.03818159.jpg', '2.jpg', 2),
(19, 'VF-5', 'uploads/rooms/VF-5/room_69f44ea221b918.81274949.jpg', '1.jpg', 1),
(20, 'VF-5', 'uploads/rooms/VF-5/room_69f44eaa0ae1c5.88539002.jpg', '4.jpg', 4),
(21, 'VF-6', 'uploads/rooms/VF-6/room_69f44eb3ed5118.08084859.jpg', '1.jpg', 1),
(22, 'VF-6', 'uploads/rooms/VF-6/room_69f44eb3ee73b9.78419498.jpg', '3.jpg', 3),
(23, 'VF-6', 'uploads/rooms/VF-6/room_69f44eb3ef50d2.16587079.jpg', '2.jpg', 2),
(24, 'VF-6', 'uploads/rooms/VF-6/room_69f44eba6ef6c5.83194523.jpg', '4.jpg', 4),
(25, 'CF-1', 'uploads/rooms/CF-1/room_69f44f2ac086e1.28125885.jpg', '1.jpg', 1),
(26, 'CF-1', 'uploads/rooms/CF-1/room_69f44f2ac0fa15.21531418.jpg', '2.jpg', 2),
(27, 'CF-1', 'uploads/rooms/CF-1/room_69f44f2ac1fdc6.80725798.jpg', '3.jpg', 3),
(28, 'CF-1', 'uploads/rooms/CF-1/room_69f44f3b8c43e0.94124581.jpg', '4.jpg', 4),
(29, 'CF-2', 'uploads/rooms/CF-2/room_69f44f474dcb64.51693557.jpg', '1.jpg', 1),
(30, 'CF-2', 'uploads/rooms/CF-2/room_69f44f474e4163.09417709.jpg', '2.jpg', 2),
(31, 'CF-2', 'uploads/rooms/CF-2/room_69f44f474f35e0.64976181.jpg', '3.jpg', 3),
(32, 'CF-2', 'uploads/rooms/CF-2/room_69f44f4f5894b6.19276860.jpg', '4.jpg', 4),
(33, 'CF-3', 'uploads/rooms/CF-3/room_69f44f5d3417d1.67601317.jpg', '1.jpg', 1),
(34, 'CF-3', 'uploads/rooms/CF-3/room_69f44f5d3489a9.06174823.jpg', '2.jpg', 2),
(35, 'CF-3', 'uploads/rooms/CF-3/room_69f44f5d34d552.63723835.jpg', '3.jpg', 3),
(36, 'CF-3', 'uploads/rooms/CF-3/room_69f44f6a909771.18069155.jpg', '4.jpg', 4),
(37, 'CF-3', 'uploads/rooms/CF-3/room_69f44f6a9173b6.65982709.jpg', '5.jpg', 5),
(38, 'CF-3', 'uploads/rooms/CF-3/room_69f44f6a927909.91226016.jpg', '6.jpg', 6),
(39, 'CF-4', 'uploads/rooms/CF-4/room_69f44f808bd494.60345516.jpg', '3.jpg', 3),
(40, 'CF-4', 'uploads/rooms/CF-4/room_69f44f808c3ac6.54372586.jpg', '1.jpg', 1),
(41, 'CF-4', 'uploads/rooms/CF-4/room_69f44f808d0cd7.17787024.jpg', '2.jpg', 2),
(42, 'CF-4', 'uploads/rooms/CF-4/room_69f44fdf63a457.19589182.jpg', '4.jpg', 4),
(43, 'CF-5', 'uploads/rooms/CF-5/room_69f45028667e88.31742482.jpg', '3.jpg', 3),
(44, 'CF-5', 'uploads/rooms/CF-5/room_69f4502866d849.68581386.jpg', '2.jpg', 2),
(45, 'CF-5', 'uploads/rooms/CF-5/room_69f450286772e7.32602842.jpg', '1.jpg', 1),
(46, 'CF-5', 'uploads/rooms/CF-5/room_69f45032750e44.69757469.jpg', '4.jpg', 4),
(47, 'CF-6', 'uploads/rooms/CF-6/room_69f45041e60fb8.88676814.jpg', '3.jpg', 3),
(48, 'CF-6', 'uploads/rooms/CF-6/room_69f45041e66e49.06960401.jpg', '1.jpg', 1),
(49, 'CF-6', 'uploads/rooms/CF-6/room_69f45041e6b4e4.42405544.jpg', '2.jpg', 2),
(50, 'CF-6', 'uploads/rooms/CF-6/room_69f4504cc9ede6.49375382.jpg', '4.jpg', 4),
(51, 'CF-7', 'uploads/rooms/CF-7/room_69f45058643b77.38104368.jpg', '3.jpg', 3),
(52, 'CF-7', 'uploads/rooms/CF-7/room_69f4505865dc85.86601092.jpg', '1.jpg', 1),
(53, 'CF-7', 'uploads/rooms/CF-7/room_69f45058665a60.64663505.jpg', '2.jpg', 2),
(54, 'CF-7', 'uploads/rooms/CF-7/room_69f45060117e16.25256285.jpg', '4.jpg', 4),
(55, 'CF-8', 'uploads/rooms/CF-8/room_69f451b793dba1.86294109.jpg', '1.jpg', 1),
(56, 'CF-8', 'uploads/rooms/CF-8/room_69f451b794bce4.38603301.jpg', '3.jpg', 3),
(57, 'CF-8', 'uploads/rooms/CF-8/room_69f451b7952788.67989428.jpg', '2.jpg', 2),
(58, 'CF-8', 'uploads/rooms/CF-8/room_69f451c4ef10d5.12753424.jpg', '5.jpg', 5),
(59, 'CF-8', 'uploads/rooms/CF-8/room_69f451c4f00d23.63772427.jpg', '4.jpg', 4),
(60, 'CF-9', 'uploads/rooms/CF-9/room_69f451d75de778.62867455.jpg', '1.jpg', 1),
(61, 'CF-9', 'uploads/rooms/CF-9/room_69f451d75f1d00.68640945.jpg', '2.jpg', 2),
(62, 'CF-9', 'uploads/rooms/CF-9/room_69f451d75fb909.47479393.jpg', '3.jpg', 3),
(63, 'CF-9', 'uploads/rooms/CF-9/room_69f451dfd9ca23.93274841.jpg', '4.jpg', 4),
(64, 'CF-9', 'uploads/rooms/CF-9/room_69f451dfda5be6.28131371.jpg', '5.jpg', 5),
(65, 'CF-10', 'uploads/rooms/CF-10/room_69f451f3cd1072.27143578.jpg', '1.jpg', 1),
(66, 'CF-10', 'uploads/rooms/CF-10/room_69f451f3cd9e40.24398289.jpg', '2.jpg', 2),
(67, 'CF-10', 'uploads/rooms/CF-10/room_69f451f3ce0f23.23701444.jpg', '3.jpg', 3),
(68, 'CF-10', 'uploads/rooms/CF-10/room_69f451fdd22186.63120590.jpg', '4.jpg', 4),
(69, 'CF-10', 'uploads/rooms/CF-10/room_69f451fdd289a8.54403024.jpg', '5.jpg', 5),
(70, 'CF-11', 'uploads/rooms/CF-11/room_69f4520ca3ed65.25864791.jpg', '2.jpg', 2),
(71, 'CF-11', 'uploads/rooms/CF-11/room_69f4520ca483b1.60585340.jpg', '3.jpg', 3),
(72, 'CF-11', 'uploads/rooms/CF-11/room_69f4520ca53685.38357571.jpg', '1.jpg', 1),
(73, 'CF-11', 'uploads/rooms/CF-11/room_69f452173eb6e8.32408140.jpg', '4.jpg', 4),
(74, 'CF-11', 'uploads/rooms/CF-11/room_69f452173f4221.94016934.jpg', '5.jpg', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `RoomID` (`RoomID`),
  ADD KEY `PaymentID` (`PaymentID`);

--
-- Indexes for table `branch_images`
--
ALTER TABLE `branch_images`
  ADD PRIMARY KEY (`ImageID`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `location_images`
--
ALTER TABLE `location_images`
  ADD PRIMARY KEY (`ImageID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`ImageID`);

--
-- Indexes for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD PRIMARY KEY (`ReceiptID`),
  ADD KEY `fk_receipts_customer` (`CustomerID`),
  ADD KEY `fk_receipts_payment` (`PaymentID`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`RoomID`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`ImageID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branch_images`
--
ALTER TABLE `branch_images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `location_images`
--
ALTER TABLE `location_images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  MODIFY `ReceiptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `admins` (`AdminID`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`PaymentID`) REFERENCES `payments` (`PaymentID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`);

--
-- Constraints for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD CONSTRAINT `fk_receipts_customer` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `fk_receipts_payment` FOREIGN KEY (`PaymentID`) REFERENCES `payments` (`PaymentID`);

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
