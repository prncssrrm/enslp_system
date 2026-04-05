-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 05, 2026 at 03:37 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enslp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_transactions`
--

CREATE TABLE `accounting_transactions` (
  `id` int(11) NOT NULL,
  `txn_date` date NOT NULL,
  `type` enum('Income','Expense') NOT NULL,
  `category` varchar(80) NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `wo_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_method` varchar(40) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounting_transactions`
--

INSERT INTO `accounting_transactions` (`id`, `txn_date`, `type`, `category`, `reference_no`, `wo_id`, `description`, `payment_method`, `amount`, `created_at`) VALUES
(1, '2026-03-16', 'Income', 'Delivery', 'TEST-WO', 1, 'Test Delivery', 'Delivery', 1000.00, '2026-03-16 16:40:46'),
(2, '2026-03-16', 'Income', 'Delivery', 'WO-2026-0001', 5, 'Delivery WO-2026-0001 - FFC 20 Pin', 'Delivery', 50000.00, '2026-03-16 16:53:20'),
(7, '2026-03-18', 'Expense', 'Production', 'Protective Tape Roll', NULL, 'Used 50 roll - ', 'Inventory', 2250.00, '2026-03-18 08:46:54'),
(8, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0006', 6, 'Delivery WO-2026-0006 - FFC 30 Pin', 'Delivery', 3000.00, '2026-03-18 09:20:07'),
(9, '2026-03-18', 'Expense', 'Production', 'WO-2026-0006', 6, 'Cutting - WO-2026-0006 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 09:22:12'),
(10, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0007', 7, 'Delivery WO-2026-0007 - FFC 40 Pin', 'Delivery', 100000.00, '2026-03-18 12:34:15'),
(11, '2026-03-18', 'Expense', 'Production', 'WO-2026-0008', 8, 'Cutting - WO-2026-0008 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 13:35:25'),
(12, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0008', 8, 'Delivery WO-2026-0008 - FFC 50 Pin', 'Delivery', 10000.00, '2026-03-18 13:40:12'),
(13, '2026-03-18', 'Expense', 'Production', 'WO-2026-0009', 9, 'Cutting - WO-2026-0009 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 14:06:17'),
(14, '2026-03-19', 'Expense', 'Production', 'WO-2026-0009', 9, 'Cutting - WO-2026-0009 - Copper Foil Roll', 'Manufacturing', 70000.00, '2026-03-19 09:34:10'),
(15, '2026-03-19', 'Expense', 'Production', 'WO-2026-0011', 11, 'Cutting - WO-2026-0011 - Copper Foil Roll', 'Manufacturing', 17500.00, '2026-03-19 10:10:17'),
(16, '2026-03-19', 'Expense', 'Production', 'WO-2026-0012', 12, 'Cutting - WO-2026-0012 - Polymide Film Sheet', 'Manufacturing', 480.00, '2026-03-19 10:57:59'),
(17, '2026-03-19', 'Income', 'Delivery', 'WO-2026-0012', 12, 'Delivery WO-2026-0012 - FFC 90', 'Delivery', 600.00, '2026-03-19 11:02:53'),
(18, '2026-03-19', 'Expense', 'Production', 'WO-2026-0013', 13, 'Cutting - WO-2026-0013 - Adhesive Film', 'Manufacturing', 4000.00, '2026-03-19 11:19:21'),
(19, '2026-03-19', 'Income', 'Delivery', 'WO-2026-0013', 13, 'Delivery WO-2026-0013 - 100 PIN', 'Delivery', 2500.00, '2026-03-19 11:22:43'),
(20, '2026-03-19', 'Expense', 'Production', 'WO-2026-0014', 14, 'Cutting - WO-2026-0014 - Copper Foil Roll', 'Manufacturing', 3500.00, '2026-03-19 11:51:32'),
(21, '2026-03-19', 'Income', 'Delivery', 'WO-2026-0014', 14, 'Delivery WO-2026-0014 - 111', 'Delivery', 4.00, '2026-03-19 18:19:34'),
(22, '2026-03-15', 'Expense', 'Payroll', NULL, NULL, 'Salary - Employee ID: 6 (2026-03-01 to 2026-03-15)', NULL, 997.00, '2026-03-30 14:52:48'),
(23, '2026-03-15', 'Expense', 'Payroll', NULL, NULL, 'Salary - Employee ID: 7 (2026-03-01 to 2026-03-15)', NULL, 9990.00, '2026-03-30 14:55:41'),
(24, '2026-03-30', 'Expense', 'Payroll', NULL, NULL, 'Salary - Employee ID: 6 (2026-03-16 to 2026-03-30)', NULL, 2050.00, '2026-03-30 15:02:37');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `att_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Present','Absent','On Leave','Half Day') NOT NULL DEFAULT 'Present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cutting_jobs`
--

CREATE TABLE `cutting_jobs` (
  `id` int(11) NOT NULL,
  `work_order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity_cut` int(11) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  `date_cut` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cutting_jobs`
--

INSERT INTO `cutting_jobs` (`id`, `work_order_id`, `item_id`, `quantity_cut`, `operator`, `date_cut`) VALUES
(1, 6, 12, 20, 'meai', '2026-03-18'),
(2, 8, 12, 20, 'Airish', '2026-03-18'),
(3, 9, 12, 20, 'Airish', '2026-03-18'),
(4, 9, 10, 20, 'meai', '2026-03-19'),
(5, 11, 10, 5, 'meai', '2026-03-19'),
(6, 12, 11, 4, 'MM', '2026-03-19'),
(7, 13, 12, 5, 'NN', '2026-03-19'),
(8, 14, 10, 1, '1', '2026-03-19');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `dr_no` varchar(30) NOT NULL,
  `wo_id` int(11) NOT NULL,
  `delivered_to` varchar(150) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `delivered_date` date NOT NULL,
  `status` enum('Pending','Out for Delivery','Delivered','Cancelled') DEFAULT 'Pending',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `dr_no`, `wo_id`, `delivered_to`, `address`, `delivered_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(4, 'DR-2026-0001', 5, 'Samsung', 'San Rafae;', '2026-03-16', 'Delivered', 'Good', '2026-03-16 16:24:48', NULL),
(5, 'DR-2026-0002', 6, 'Brother Inc.', 'Ulango', '2026-03-18', 'Delivered', '', '2026-03-18 09:14:35', NULL),
(6, 'DR-2026-0003', 6, 'Brother Inc.', 'uu', '2026-03-18', 'Delivered', '', '2026-03-18 09:19:59', NULL),
(7, 'DR-2026-0004', 7, 'Mitsubishi', 'Sto.Tomas', '2026-03-18', 'Delivered', '', '2026-03-18 12:21:54', NULL),
(8, 'DR-2026-0005', 7, 'Mitsubishi', 'stb', '2026-03-18', 'Delivered', '', '2026-03-18 12:34:01', NULL),
(9, 'DR-2026-0006', 8, 'Epson', 'San Roque', '2026-03-18', 'Delivered', 'Ready to Deliver ', '2026-03-18 13:39:50', NULL),
(10, 'DR-2026-0007', 12, 'KUROMI', 'SS', '2026-03-19', 'Delivered', '', '2026-03-19 11:02:19', NULL),
(11, 'DR-2026-0008', 13, 'SAMSUNG', 'STB', '2026-03-19', 'Delivered', '', '2026-03-19 11:22:08', NULL),
(12, 'DR-2026-0009', 14, '111', 'stb', '2026-03-19', 'Delivered', '', '2026-03-19 18:10:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `salary_type` enum('Daily','Monthly') NOT NULL DEFAULT 'Daily',
  `salary_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `contact_no` varchar(20) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `monthly_salary` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `full_name`, `position`, `department`, `employment_status`, `date_hired`, `salary_type`, `salary_amount`, `contact_no`, `daily_rate`, `status`, `monthly_salary`) VALUES
(5, 'Marem Legazpi', 'Engineer', 'Engineering', 'Regular', '2026-03-18', 'Monthly', 700.00, '09154782744', NULL, 'Active', 0.00),
(6, 'Airra Esta', 'Accountant', 'Accounting', 'Regular', '2026-03-01', 'Monthly', 700.00, '09154782744', NULL, 'Active', 0.00),
(7, 'Airish Abliter', 'Engineer', 'Engineering', 'Regular', '2026-03-01', 'Monthly', 20000.00, '09154782744', NULL, 'Active', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `etching_jobs`
--

CREATE TABLE `etching_jobs` (
  `id` int(11) NOT NULL,
  `work_order_id` varchar(50) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `design` varchar(100) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  `date_etched` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etching_jobs`
--

INSERT INTO `etching_jobs` (`id`, `work_order_id`, `item_id`, `design`, `operator`, `date_etched`, `quantity`) VALUES
(7, '8', 11, 'Design A', 'Airish', '2026-03-17 16:00:00', 0),
(8, '11', 11, 'design a', 'meai', '2026-03-18 16:00:00', 0),
(9, '12', 12, 'A', 'MM', '2026-03-18 16:00:00', 0),
(10, '13', 11, 'A', 'NN', '2026-03-18 16:00:00', 0),
(11, '14', 12, '1', '1', '2026-03-18 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_qc`
--

CREATE TABLE `inspection_qc` (
  `id` int(11) NOT NULL,
  `work_order_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `inspector` varchar(100) DEFAULT NULL,
  `status` enum('Passed','Failed') NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_inspected` timestamp NOT NULL DEFAULT current_timestamp(),
  `passed_qty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inspection_qc`
--

INSERT INTO `inspection_qc` (`id`, `work_order_id`, `item_id`, `inspector`, `status`, `remarks`, `date_inspected`, `passed_qty`) VALUES
(3, '9', 18, 'meai', 'Passed', '', '2026-03-18 13:29:20', 20),
(4, '11', 20, 'meai', 'Passed', '', '2026-03-19 02:30:10', 50),
(5, '12', 21, 'MM', 'Passed', '', '2026-03-19 02:58:50', 4),
(6, '12', 21, 'MM', 'Passed', '', '2026-03-19 03:01:24', 4),
(7, '13', 22, 'NN', 'Passed', '', '2026-03-19 03:21:10', 5),
(8, '14', 23, '1', 'Passed', '', '2026-03-19 04:07:03', 2);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'pcs',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `reorder_level` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `cost` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'active',
  `value` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `item_code`, `item_name`, `category`, `unit`, `quantity`, `reorder_level`, `created_at`, `cost`, `selling_price`, `status`, `value`) VALUES
(10, NULL, 'Copper Foil Roll', 'Raw Material', 'roll', 4, 10, '2026-03-16 10:32:34', 3500.00, 0.00, 'active', 0.00),
(11, NULL, 'Polymide Film Sheet', 'Raw Material', 'sheet', 196, 50, '2026-03-16 10:33:13', 120.00, 0.00, 'active', 0.00),
(12, NULL, 'Adhesive Film', 'Raw Material', 'roll', 15, 5, '2026-03-16 10:35:38', 800.00, 0.00, 'active', 12000.00),
(13, NULL, 'FFC Connector 20 Pin', 'Component', 'pcs', 500, 100, '2026-03-16 10:36:11', 8.00, 0.00, 'active', 0.00),
(14, NULL, 'Contact Terminal', 'Component', 'pcs', 1000, 200, '2026-03-16 10:36:42', 1.00, 0.00, 'active', 0.00),
(15, NULL, 'Ferric Chloride Etching Solution ', 'Supply', 'liter', 20, 5, '2026-03-16 10:37:28', 450.00, 0.00, 'active', 0.00),
(16, NULL, 'Lamination Glue ', 'Supply', 'bottle', 40, 10, '2026-03-16 10:39:29', 150.00, 0.00, 'active', 0.00),
(17, NULL, 'Protective Tape Roll', 'Supply', 'roll', 40, 15, '2026-03-16 10:40:05', 45.00, 0.00, 'active', 0.00),
(18, NULL, 'FFC Cable 20 Pin ', 'Finished Good', 'pcs', 152, 20, '2026-03-16 10:40:43', 35.00, 120.00, 'active', 0.00),
(19, NULL, 'FFC Cable 30 Pin', 'Finished Good', 'pcs', 80, 20, '2026-03-16 10:41:29', 45.00, 150.00, 'active', 0.00),
(20, NULL, 'FFC 80 Pin', 'Finished Good', 'pcs', 100, 0, '2026-03-19 10:30:10', 0.00, 0.00, 'active', 0.00),
(21, NULL, 'FFC 90', 'Finished Good', 'pcs', 12, 0, '2026-03-19 10:58:50', 0.00, 0.00, 'active', 0.00),
(22, NULL, '100 PIN', 'Finished Good', 'pcs', 5, 0, '2026-03-19 11:21:10', 0.00, 0.00, 'active', 0.00),
(23, NULL, '111', 'Finished Good', 'pcs', -4, 0, '2026-03-19 12:07:03', 0.00, 2.00, 'active', 0.00);

--
-- Triggers `inventory_items`
--
DELIMITER $$
CREATE TRIGGER `update_value_before_update` BEFORE UPDATE ON `inventory_items` FOR EACH ROW SET NEW.value = NEW.cost * NEW.quantity
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lamination_jobs`
--

CREATE TABLE `lamination_jobs` (
  `id` int(11) NOT NULL,
  `work_order_id` varchar(50) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `adhesive_type` varchar(100) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  `date_laminated` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lamination_jobs`
--

INSERT INTO `lamination_jobs` (`id`, `work_order_id`, `item_id`, `adhesive_type`, `operator`, `date_laminated`, `quantity`) VALUES
(4, '8', 11, 'Type A', 'Airish', '2026-03-17 16:00:00', 0),
(5, '11', 11, 'b', 'meai', '2026-03-18 16:00:00', 0),
(6, '12', 11, 'MM', 'MM', '2026-03-18 16:00:00', 0),
(7, '13', 11, 'NN', 'NN', '2026-03-18 16:00:00', 0),
(8, '14', 10, 'A', 'AA', '2026-03-18 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packing_jobs`
--

CREATE TABLE `packing_jobs` (
  `id` int(11) NOT NULL,
  `work_order_id` varchar(50) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity_packed` int(11) NOT NULL,
  `packer` varchar(100) DEFAULT NULL,
  `date_packed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packing_jobs`
--

INSERT INTO `packing_jobs` (`id`, `work_order_id`, `item_id`, `quantity_packed`, `packer`, `date_packed`) VALUES
(2, '9', 18, 2, 'Meai', '2026-03-18 12:50:34'),
(3, '11', 20, 50, 'meai', '2026-03-19 02:45:20'),
(4, '12', 21, 4, 'MM', '2026-03-19 03:02:02'),
(5, '13', 22, 5, 'NN', '2026-03-19 03:21:32'),
(6, '14', 23, 2, 'meai', '2026-03-19 10:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `days_worked` decimal(5,2) NOT NULL DEFAULT 0.00,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `overtime_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pay_type` varchar(20) NOT NULL DEFAULT 'REGULAR',
  `basic_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overtime_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `philhealth` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pagibig` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_deductions` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `period_start`, `period_end`, `days_worked`, `overtime_hours`, `overtime_rate`, `deductions`, `gross_pay`, `net_pay`, `created_at`, `pay_type`, `basic_pay`, `overtime_pay`, `allowances`, `sss`, `philhealth`, `pagibig`, `other_deductions`) VALUES
(4, 5, '2026-03-27', '2026-03-27', 1.00, 0.00, 0.00, 0.00, 350.00, 287.00, '2026-03-27 23:27:30', 'REGULAR', 350.00, 0.00, 0.00, 31.50, 17.50, 14.00, 0.00),
(5, 6, '2026-03-01', '2026-03-15', 10.00, 3.00, 70.00, 0.00, 1060.00, 997.00, '2026-03-30 06:52:48', 'REGULAR', 350.00, 210.00, 500.00, 31.50, 17.50, 14.00, 0.00),
(6, 7, '2026-03-01', '2026-03-15', 13.00, 7.00, 70.00, 0.00, 11490.00, 9990.00, '2026-03-30 06:55:41', 'REGULAR', 10000.00, 490.00, 1000.00, 900.00, 500.00, 100.00, 0.00),
(7, 6, '2026-03-16', '2026-03-30', 10.00, 10.00, 70.00, 0.00, 2050.00, 2050.00, '2026-03-30 07:02:37', 'REGULAR', 350.00, 700.00, 1000.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `production_history`
--

CREATE TABLE `production_history` (
  `id` int(11) NOT NULL,
  `wo_id` int(11) DEFAULT NULL,
  `wo_no` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `client` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_started` date DEFAULT NULL,
  `date_finished` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_history`
--

INSERT INTO `production_history` (`id`, `wo_id`, `wo_no`, `product_name`, `client`, `qty`, `selling_price`, `status`, `remarks`, `date_started`, `date_finished`, `created_at`) VALUES
(1, 5, 'WO-2026-0001', 'FFC 20 Pin', 'Samsung', 200, 250.00, 'Completed', NULL, '2026-03-16', '2026-03-18', '2026-03-18 13:03:36'),
(2, 6, 'WO-2026-0006', 'FFC 30 Pin', 'Brother Inc.', 20, 150.00, 'Completed', NULL, '2026-03-18', '2026-03-18', '2026-03-18 13:03:36'),
(3, 7, 'WO-2026-0007', 'FFC 40 Pin', 'Mitsubishi', 200, 500.00, 'Completed', NULL, '2026-03-18', '2026-03-18', '2026-03-18 13:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `movement_type` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `movement_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `item_id`, `movement_type`, `quantity`, `reference`, `notes`, `created_at`, `movement_date`) VALUES
(6, 12, 'cutting', 20, '6', NULL, '2026-03-18 09:22:12', '2026-03-18 09:22:12'),
(7, 12, 'IN', 20, NULL, 'Manual restock', '2026-03-18 12:45:19', '2026-03-18 12:45:19'),
(8, 12, 'cutting', 20, '8', NULL, '2026-03-18 13:35:25', '2026-03-18 13:35:25'),
(9, 11, 'etching', 1, '8', NULL, '2026-03-18 13:37:12', '2026-03-18 13:37:12'),
(10, 11, 'lamination', 1, '8', NULL, '2026-03-18 13:38:05', '2026-03-18 13:38:05'),
(11, 12, 'cutting', 20, '9', NULL, '2026-03-18 14:06:17', '2026-03-18 14:06:17'),
(12, 15, 'inspection', 1, '9', NULL, '2026-03-18 20:49:55', '2026-03-18 20:49:55'),
(13, 18, 'packing', 2, '9', NULL, '2026-03-18 20:50:34', '2026-03-18 20:50:34'),
(14, 18, 'Stock In', 20, '9', 'QC Finished Product', '2026-03-18 21:29:20', '2026-03-18 21:29:20'),
(16, 10, 'cutting', 20, '9', NULL, '2026-03-19 09:34:10', '2026-03-19 09:34:10'),
(17, 10, 'cutting', 5, '11', NULL, '2026-03-19 10:10:17', '2026-03-19 10:10:17'),
(18, 11, 'etching', 1, '11', NULL, '2026-03-19 10:10:45', '2026-03-19 10:10:45'),
(19, 11, 'lamination', 1, '11', NULL, '2026-03-19 10:11:05', '2026-03-19 10:11:05'),
(20, 20, 'Stock In', 50, '11', 'QC STOCK IN', '2026-03-19 10:30:10', '2026-03-19 10:30:10'),
(21, 20, 'packing', 50, '11', NULL, '2026-03-19 10:45:20', '2026-03-19 10:45:20'),
(22, 11, 'cutting', 4, '12', NULL, '2026-03-19 10:57:59', '2026-03-19 10:57:59'),
(23, 12, 'etching', 1, '12', NULL, '2026-03-19 10:58:22', '2026-03-19 10:58:22'),
(24, 11, 'lamination', 1, '12', NULL, '2026-03-19 10:58:36', '2026-03-19 10:58:36'),
(25, 21, 'Stock In', 4, '12', 'QC STOCK IN', '2026-03-19 10:58:50', '2026-03-19 10:58:50'),
(26, 21, 'Stock In', 4, '12', 'QC STOCK IN', '2026-03-19 11:01:24', '2026-03-19 11:01:24'),
(27, 21, 'packing', 4, '12', NULL, '2026-03-19 11:02:02', '2026-03-19 11:02:02'),
(28, 12, 'cutting', 5, '13', NULL, '2026-03-19 11:19:21', '2026-03-19 11:19:21'),
(29, 11, 'etching', 1, '13', NULL, '2026-03-19 11:19:42', '2026-03-19 11:19:42'),
(30, 11, 'lamination', 1, '13', NULL, '2026-03-19 11:20:06', '2026-03-19 11:20:06'),
(31, 22, 'Stock In', 5, '13', 'QC STOCK IN', '2026-03-19 11:21:10', '2026-03-19 11:21:10'),
(32, 22, 'packing', 5, '13', NULL, '2026-03-19 11:21:32', '2026-03-19 11:21:32'),
(33, 10, 'cutting', 1, '14', NULL, '2026-03-19 11:51:32', '2026-03-19 11:51:32'),
(34, 12, 'etching', 1, '14', NULL, '2026-03-19 11:53:02', '2026-03-19 11:53:02'),
(35, 10, 'lamination', 1, '14', NULL, '2026-03-19 11:53:39', '2026-03-19 11:53:39'),
(36, 23, 'Stock In', 2, '14', 'QC STOCK IN', '2026-03-19 12:07:03', '2026-03-19 12:07:03'),
(37, 23, 'packing', 2, '14', NULL, '2026-03-19 18:10:04', '2026-03-19 18:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `thirteenth_month`
--

CREATE TABLE `thirteenth_month` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `total_basic_salary` decimal(12,2) DEFAULT 0.00,
  `thirteenth_amount` decimal(12,2) DEFAULT 0.00,
  `generated_by` int(11) DEFAULT NULL,
  `date_generated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'Admin',
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `password`, `role`, `status`) VALUES
(3, 'admin', 'System Administrator', '$2y$10$fW7TiiAWFOYO.PSwVGeA2uwZN37uo4Kr7ovWfJKglkxn.i7Ecba5G', 'Admin', 'Active'),
(5, 'meai', NULL, '$2y$10$f1w56eaLbftFJ.8UmbvpxOpn3ab46oVycvM3CNpCNeRNQZx6VH3eO', 'Production', 'active'),
(6, 'lovely', NULL, '$2y$10$tVEt7/a5Pdc7VCS6yzr8kO1EWKV17K2KIkl62WTy9rhrQiFWL3mZW', 'Accounting', 'active'),
(7, 'airish', NULL, '$2y$10$nl3kdqGaJT4dy7OtLGIEI.NoFHTE3r9ApS5t.I3m.ByZ0N4BP0ad.', 'Engineer', 'active'),
(9, 'may', NULL, '$2y$10$byKoHpqLdRX7l5DO3oPND.OYkbAGdwU02dJKTsF714kf/XG05UZ9.', 'Staff', 'active'),
(10, 'meyay', NULL, '$2y$10$S8Cz4iF53P73b.lxAGGGhOozVYwFb/k9rgO/fodJRki5PzFYEINCe', 'Production', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `work_orders`
--

CREATE TABLE `work_orders` (
  `id` int(11) NOT NULL,
  `wo_no` varchar(50) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `client` varchar(150) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT NULL,
  `date_started` date NOT NULL,
  `date_completed` date DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `selling_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_orders`
--

INSERT INTO `work_orders` (`id`, `wo_no`, `product_name`, `client`, `qty`, `status`, `date_started`, `date_completed`, `remarks`, `created_at`, `selling_price`) VALUES
(5, 'WO-2026-0001', 'FFC 20 Pin', 'Samsung', 200, 'Completed', '2026-03-16', '2026-03-18', NULL, '2026-03-16 08:13:40', 250.00),
(6, 'WO-2026-0006', 'FFC 30 Pin', 'Brother Inc.', 20, 'Completed', '2026-03-18', '2026-03-18', NULL, '2026-03-18 01:14:09', 150.00),
(7, 'WO-2026-0007', 'FFC 40 Pin', 'Mitsubishi', 200, 'Completed', '2026-03-18', '2026-03-18', NULL, '2026-03-18 04:21:33', 500.00),
(8, 'WO-2026-0008', 'FFC 50 Pin', 'Epson', 20, 'Completed', '2026-03-18', '2026-03-18', NULL, '2026-03-18 05:34:28', 500.00),
(9, 'WO-2026-0009', 'FFC 60 Pin', 'Panasonic', 200, 'Completed', '2026-03-18', NULL, NULL, '2026-03-18 06:05:48', 150.00),
(10, 'WO-2026-0010', 'FFC 70 Pin', 'IPhone', 50, 'Pending', '2026-03-19', NULL, NULL, '2026-03-19 02:07:17', 560.00),
(11, 'WO-2026-0011', 'FFC 80 Pin', 'Nintendo', 5, 'Completed', '2026-03-19', '2026-03-19', NULL, '2026-03-19 02:09:54', 500.00),
(12, 'WO-2026-0012', 'FFC 90', 'KUROMI', 4, 'Completed', '2026-03-19', '2026-03-19', NULL, '2026-03-19 02:57:40', 150.00),
(13, 'WO-2026-0013', '100 PIN', 'SAMSUNG', 5, 'Completed', '2026-03-19', '2026-03-19', NULL, '2026-03-19 03:18:30', 500.00),
(14, 'WO-2026-0014', '111', '111', 2, 'Packed', '2026-03-19', NULL, NULL, '2026-03-19 03:51:13', 2.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_transactions`
--
ALTER TABLE `accounting_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_emp_date` (`employee_id`,`att_date`);

--
-- Indexes for table `cutting_jobs`
--
ALTER TABLE `cutting_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dr_no` (`dr_no`),
  ADD KEY `wo_id` (`wo_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `etching_jobs`
--
ALTER TABLE `etching_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspection_qc`
--
ALTER TABLE `inspection_qc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lamination_jobs`
--
ALTER TABLE `lamination_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packing_jobs`
--
ALTER TABLE `packing_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payroll_employee` (`employee_id`);

--
-- Indexes for table `production_history`
--
ALTER TABLE `production_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thirteenth_month`
--
ALTER TABLE `thirteenth_month`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_emp_year` (`employee_id`,`year`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wo_no` (`wo_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_transactions`
--
ALTER TABLE `accounting_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutting_jobs`
--
ALTER TABLE `cutting_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `etching_jobs`
--
ALTER TABLE `etching_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_qc`
--
ALTER TABLE `inspection_qc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `lamination_jobs`
--
ALTER TABLE `lamination_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packing_jobs`
--
ALTER TABLE `packing_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `production_history`
--
ALTER TABLE `production_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `thirteenth_month`
--
ALTER TABLE `thirteenth_month`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`wo_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
