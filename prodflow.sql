-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               11.7.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for prodflow
CREATE DATABASE IF NOT EXISTS `prodflow` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `prodflow`;

-- Dumping structure for table prodflow.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `sector` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.contracts
CREATE TABLE IF NOT EXISTS `contracts` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `eid` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.machines
CREATE TABLE IF NOT EXISTS `machines` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `machine` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.maintenances
CREATE TABLE IF NOT EXISTS `maintenances` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `machine_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.materials
CREATE TABLE IF NOT EXISTS `materials` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `material` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.materials_stock
CREATE TABLE IF NOT EXISTS `materials_stock` (
  `msid` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT current_timestamp(),
  `warehouse_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`msid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.planification
CREATE TABLE IF NOT EXISTS `planification` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `planned_qty` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Unrealized',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.production
CREATE TABLE IF NOT EXISTS `production` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `machine_id` int(11) DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.products
CREATE TABLE IF NOT EXISTS `products` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.salaries
CREATE TABLE IF NOT EXISTS `salaries` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.sales
CREATE TABLE IF NOT EXISTS `sales` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.staff
CREATE TABLE IF NOT EXISTS `staff` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `supplier` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.users
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'staff',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.vocations
CREATE TABLE IF NOT EXISTS `vocations` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table prodflow.warehouses
CREATE TABLE IF NOT EXISTS `warehouses` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
