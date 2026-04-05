-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 18, 2026 at 11:33 AM
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
-- Database: `enslp_db_backup`
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
  `created_at` datetime DEFAULT current_timestamp(),
  `delivery_id` int(11) DEFAULT NULL,
  `work_order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounting_transactions`
--

INSERT INTO `accounting_transactions` (`id`, `txn_date`, `type`, `category`, `reference_no`, `wo_id`, `description`, `payment_method`, `amount`, `created_at`, `delivery_id`, `work_order_id`) VALUES
(1, '2026-03-16', 'Income', 'Delivery', 'TEST-WO', 1, 'Test Delivery', 'Delivery', 1000.00, '2026-03-16 16:40:46', NULL, NULL),
(2, '2026-03-16', 'Income', 'Delivery', 'WO-2026-0001', 5, 'Delivery WO-2026-0001 - FFC 20 Pin', 'Delivery', 50000.00, '2026-03-16 16:53:20', NULL, NULL),
(7, '2026-03-18', 'Expense', 'Production', 'Protective Tape Roll', NULL, 'Used 50 roll - ', 'Inventory', 2250.00, '2026-03-18 08:46:54', NULL, NULL),
(8, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0006', 6, 'Delivery WO-2026-0006 - FFC 30 Pin', 'Delivery', 3000.00, '2026-03-18 09:20:07', NULL, NULL),
(9, '2026-03-18', 'Expense', 'Production', 'WO-2026-0006', 6, 'Cutting - WO-2026-0006 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 09:22:12', NULL, NULL),
(10, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0007', 7, 'Delivery WO-2026-0007 - FFC 40 Pin', 'Delivery', 100000.00, '2026-03-18 12:34:15', NULL, NULL),
(11, '2026-03-18', 'Expense', 'Production', 'WO-2026-0008', 8, 'Cutting - WO-2026-0008 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 13:35:25', NULL, NULL),
(12, '2026-03-18', 'Income', 'Delivery', 'WO-2026-0008', 8, 'Delivery WO-2026-0008 - FFC 50 Pin', 'Delivery', 10000.00, '2026-03-18 13:40:12', NULL, NULL),
(13, '2026-03-18', 'Expense', 'Production', 'WO-2026-0009', 9, 'Cutting - WO-2026-0009 - Adhesive Film', 'Manufacturing', 16000.00, '2026-03-18 14:06:17', NULL, NULL);

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
(3, 9, 12, 20, 'Airish', '2026-03-18');

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
  `updated_at` datetime DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `dr_no`, `wo_id`, `delivered_to`, `address`, `delivered_date`, `status`, `remarks`, `created_at`, `updated_at`, `item_id`, `quantity`) VALUES
(4, 'DR-2026-0001', 5, 'Samsung', 'San Rafae;', '2026-03-16', 'Delivered', 'Good', '2026-03-16 16:24:48', NULL, NULL, 1),
(5, 'DR-2026-0002', 6, 'Brother Inc.', 'Ulango', '2026-03-18', 'Delivered', '', '2026-03-18 09:14:35', NULL, NULL, 1),
(6, 'DR-2026-0003', 6, 'Brother Inc.', 'uu', '2026-03-18', 'Delivered', '', '2026-03-18 09:19:59', NULL, NULL, 1),
(7, 'DR-2026-0004', 7, 'Mitsubishi', 'Sto.Tomas', '2026-03-18', 'Delivered', '', '2026-03-18 12:21:54', NULL, NULL, 1),
(8, 'DR-2026-0005', 7, 'Mitsubishi', 'stb', '2026-03-18', 'Delivered', '', '2026-03-18 12:34:01', NULL, NULL, 1),
(9, 'DR-2026-0006', 8, 'Epson', 'San Roque', '2026-03-18', 'Delivered', 'Ready to Deliver ', '2026-03-18 13:39:50', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_packing`
--

CREATE TABLE `delivery_packing` (
  `id` int(11) NOT NULL,
  `delivery_id` int(11) DEFAULT NULL,
  `packing_job_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `date_etched` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etching_jobs`
--

INSERT INTO `etching_jobs` (`id`, `work_order_id`, `item_id`, `design`, `operator`, `date_etched`) VALUES
(7, '8', 11, 'Design A', 'Airish', '2026-03-17 16:00:00');

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
  `lamination_job_id` int(11) DEFAULT NULL,
  `cutting_job_id` int(11) DEFAULT NULL,
  `etching_job_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `item_code`, `item_name`, `category`, `unit`, `quantity`, `reorder_level`, `created_at`, `cost`, `selling_price`, `status`) VALUES
(10, NULL, 'Copper Foil Roll', 'Raw Material', 'roll', 30, 10, '2026-03-16 10:32:34', 3500.00, 0.00, 'active'),
(11, NULL, 'Polymide Film Sheet', 'Raw Material', 'sheet', 200, 50, '2026-03-16 10:33:13', 120.00, 0.00, 'active'),
(12, NULL, 'Adhesive Film', 'Raw Material', 'roll', 10, 5, '2026-03-16 10:35:38', 800.00, 0.00, 'active'),
(13, NULL, 'FFC Connector 20 Pin', 'Component', 'pcs', 500, 100, '2026-03-16 10:36:11', 8.00, 0.00, 'active'),
(14, NULL, 'Contact Terminal', 'Component', 'pcs', 1000, 200, '2026-03-16 10:36:42', 1.00, 0.00, 'active'),
(15, NULL, 'Ferric Chloride Etching Solution ', 'Supply', 'liter', 20, 5, '2026-03-16 10:37:28', 450.00, 0.00, 'active'),
(16, NULL, 'Lamination Glue ', 'Supply', 'bottle', 40, 10, '2026-03-16 10:39:29', 150.00, 0.00, 'active'),
(17, NULL, 'Protective Tape Roll', 'Supply', 'roll', 40, 15, '2026-03-16 10:40:05', 45.00, 0.00, 'active'),
(18, NULL, 'FFC Cable 20 Pin ', 'Finished Good', 'pcs', 130, 20, '2026-03-16 10:40:43', 35.00, 120.00, 'active'),
(19, NULL, 'FFC Cable 30 Pin', 'Finished Good', 'pcs', 80, 20, '2026-03-16 10:41:29', 45.00, 150.00, 'active');

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
  `date_laminated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lamination_jobs`
--

INSERT INTO `lamination_jobs` (`id`, `work_order_id`, `item_id`, `adhesive_type`, `operator`, `date_laminated`) VALUES
(4, '8', 11, 'Type A', 'Airish', '2026-03-17 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lamination_materials`
--

CREATE TABLE `lamination_materials` (
  `id` int(11) NOT NULL,
  `lamination_job_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(11, 12, 'cutting', 20, '9', NULL, '2026-03-18 14:06:17', '2026-03-18 14:06:17');

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
(5, 'meai', NULL, '$2y$10$f1w56eaLbftFJ.8UmbvpxOpn3ab46oVycvM3CNpCNeRNQZx6VH3eO', 'Production', 'active');

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
  `status` enum('Pending','In Progress','QC','Completed') NOT NULL DEFAULT 'Pending',
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
(9, 'WO-2026-0009', 'FFC 60 Pin', 'Panasonic', 200, 'Pending', '2026-03-18', NULL, NULL, '2026-03-18 06:05:48', 150.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_transactions`
--
ALTER TABLE `accounting_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_accounting_delivery` (`delivery_id`),
  ADD KEY `fk_accounting_workorder` (`work_order_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cutting_work_order` (`work_order_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dr_no` (`dr_no`),
  ADD KEY `wo_id` (`wo_id`),
  ADD KEY `fk_deliveries_inventory` (`item_id`);

--
-- Indexes for table `delivery_packing`
--
ALTER TABLE `delivery_packing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `packing_job_id` (`packing_job_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_qc_lamination` (`lamination_job_id`),
  ADD KEY `fk_qc_cutting` (`cutting_job_id`),
  ADD KEY `fk_qc_etching` (`etching_job_id`);

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
-- Indexes for table `lamination_materials`
--
ALTER TABLE `lamination_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lamination_job_id` (`lamination_job_id`),
  ADD KEY `item_id` (`item_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stock_item` (`item_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutting_jobs`
--
ALTER TABLE `cutting_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `delivery_packing`
--
ALTER TABLE `delivery_packing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `etching_jobs`
--
ALTER TABLE `etching_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_qc`
--
ALTER TABLE `inspection_qc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lamination_jobs`
--
ALTER TABLE `lamination_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lamination_materials`
--
ALTER TABLE `lamination_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packing_jobs`
--
ALTER TABLE `packing_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `production_history`
--
ALTER TABLE `production_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `thirteenth_month`
--
ALTER TABLE `thirteenth_month`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_transactions`
--
ALTER TABLE `accounting_transactions`
  ADD CONSTRAINT `fk_accounting_delivery` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_accounting_workorder` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `cutting_jobs`
--
ALTER TABLE `cutting_jobs`
  ADD CONSTRAINT `fk_cutting_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`wo_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_deliveries_inventory` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`);

--
-- Constraints for table `delivery_packing`
--
ALTER TABLE `delivery_packing`
  ADD CONSTRAINT `delivery_packing_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`),
  ADD CONSTRAINT `delivery_packing_ibfk_2` FOREIGN KEY (`packing_job_id`) REFERENCES `packing_jobs` (`id`);

--
-- Constraints for table `inspection_qc`
--
ALTER TABLE `inspection_qc`
  ADD CONSTRAINT `fk_qc_cutting` FOREIGN KEY (`cutting_job_id`) REFERENCES `cutting_jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qc_etching` FOREIGN KEY (`etching_job_id`) REFERENCES `etching_jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qc_lamination` FOREIGN KEY (`lamination_job_id`) REFERENCES `lamination_jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lamination_materials`
--
ALTER TABLE `lamination_materials`
  ADD CONSTRAINT `lamination_materials_ibfk_1` FOREIGN KEY (`lamination_job_id`) REFERENCES `lamination_jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lamination_materials_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stock_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thirteenth_month`
--
ALTER TABLE `thirteenth_month`
  ADD CONSTRAINT `fk_13th_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
