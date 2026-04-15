-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 15 Apr 2026 pada 11.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meditrack_pharmacy`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `drugs`
--

CREATE TABLE `drugs` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `name` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `license_number` varchar(100) NOT NULL,
  `manufacturer` longtext DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `storage_condition` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `dosage` longtext DEFAULT NULL,
  `category` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `drug_inventory_log`
--

CREATE TABLE `drug_inventory_log` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `drug_id` char(36) NOT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `quantity_change` int(11) DEFAULT NULL,
  `previous_quantity` int(11) DEFAULT NULL,
  `new_quantity` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `drug_stocks`
--

CREATE TABLE `drug_stocks` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `drug_id` char(36) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 50,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pharmacy_id` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `pharmacy_order_id` char(36) NOT NULL,
  `drug_id` char(36) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pharmacies`
--

CREATE TABLE `pharmacies` (
  `id` varchar(191) NOT NULL,
  `name` longtext DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `phone` longtext DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `license_number` longtext DEFAULT NULL,
  `manager_id` longtext DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pharmacy_orders`
--

CREATE TABLE `pharmacy_orders` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `patient_id` char(36) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'pending',
  `ready_date` date DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `drugs`
--
ALTER TABLE `drugs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_license_number` (`license_number`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

--
-- Indeks untuk tabel `drug_inventory_log`
--
ALTER TABLE `drug_inventory_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drug_id` (`drug_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `drug_stocks`
--
ALTER TABLE `drug_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock` (`drug_id`,`location`),
  ADD KEY `idx_drug_id` (`drug_id`),
  ADD KEY `idx_quantity` (`quantity`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drug_id` (`drug_id`),
  ADD KEY `idx_pharmacy_order_id` (`pharmacy_order_id`);

--
-- Indeks untuk tabel `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_pharmacies_license_number` (`license_number`) USING HASH;

--
-- Indeks untuk tabel `pharmacy_orders`
--
ALTER TABLE `pharmacy_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_status` (`status`);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `drug_inventory_log`
--
ALTER TABLE `drug_inventory_log`
  ADD CONSTRAINT `drug_inventory_log_ibfk_1` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `drug_stocks`
--
ALTER TABLE `drug_stocks`
  ADD CONSTRAINT `drug_stocks_ibfk_1` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`pharmacy_order_id`) REFERENCES `pharmacy_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
