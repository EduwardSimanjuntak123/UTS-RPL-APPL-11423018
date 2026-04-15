-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 15 Apr 2026 pada 11.08
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
-- Database: `meditrack_payment`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `insurance_claims`
--

CREATE TABLE `insurance_claims` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `insurance_id` char(36) DEFAULT NULL,
  `invoice_id` char(36) NOT NULL,
  `claim_amount` decimal(10,2) DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `insurance_code` longtext DEFAULT NULL,
  `claim_type` longtext DEFAULT NULL,
  `amount` bigint(20) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `submitted_date` bigint(20) DEFAULT NULL,
  `approved_date` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `invoice_number` longtext DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `total_amount` bigint(20) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `due_date` bigint(20) DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `payment_id` longtext DEFAULT NULL,
  `amount` bigint(20) DEFAULT NULL,
  `tax_amount` bigint(20) DEFAULT NULL,
  `issued_date` bigint(20) DEFAULT NULL,
  `paid_date` bigint(20) DEFAULT NULL,
  `notes` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `patient_id`, `service_type`, `total_amount`, `paid_amount`, `due_date`, `status`, `created_at`, `updated_at`, `payment_id`, `amount`, `tax_amount`, `issued_date`, `paid_date`, `notes`) VALUES
('21109c9b-6f70-45d1-bbce-78e7e05c8601', 'INV-b1571cc2-a23', 'pat-010', NULL, 660000, 0.00, 1776279334, 'paid', 1773687334, 1773687334, 'e1f09e32-f2ac-41b1-b446-dc1d45439564', 600000, 60000, 1773687334, 1773687334, 'Invoice untuk pembayaran konsultasi'),
('2dc56763-5d46-4cc0-accf-c143b741e470', 'INV-dc9e4e01-6cf', 'pat-004', NULL, 880000, 0.00, 1775945894, 'paid', 1773353894, 1773353894, '3a786571-e7e1-4b26-a045-880da19c7e5f', 800000, 80000, 1773353894, 1773353894, 'Invoice untuk pembayaran konsultasi'),
('34e1c7fd-f4e7-47d4-802c-df0ec841c264', 'INV-f41e3fbc-807', 'pat-006', NULL, 440000, 0.00, 1776637094, 'paid', 1774045094, 1774045094, '0d7ba0f8-154c-4994-a13b-c93e63297f95', 400000, 40000, 1774045094, 1774045094, 'Invoice untuk pembayaran konsultasi'),
('6a019858-5186-47d1-8648-d909825af19f', 'INV-493290e9-f66', 'pat-002', NULL, 220000, 0.00, 1776982694, 'paid', 1774390694, 1774390694, 'e4f3fbf6-4410-48e5-ac0c-a80c1f618467', 200000, 20000, 1774390694, 1774390694, 'Invoice untuk pembayaran konsultasi'),
('7b4d5749-215a-4210-b3d6-79e372285042', 'INV-ab789969-be7', 'pat-010', NULL, 660000, 0.00, 1776291494, 'paid', 1773699494, 1773699494, '6fa6b94e-77b3-4d20-9d3f-1d6f8967fc9c', 600000, 60000, 1773699494, 1773699494, 'Invoice untuk pembayaran konsultasi'),
('b7802430-d77a-493d-a266-104824199d36', 'INV-26e5b6e4-ac4', 'pat-002', NULL, 220000, 0.00, 1776970534, 'paid', 1774378534, 1774378534, '34f6edc6-8093-474c-b2ee-74cea38c8fd2', 200000, 20000, 1774378534, 1774378534, 'Invoice untuk pembayaran konsultasi'),
('de2867d7-d26c-4743-824b-e250fcd39e93', 'INV-5775952a-5c3', 'pat-004', NULL, 880000, 0.00, 1775933734, 'paid', 1773341734, 1773341734, 'c8a6866b-343f-415f-b1a8-e6abcf00a92b', 800000, 80000, 1773341734, 1773341734, 'Invoice untuk pembayaran konsultasi'),
('f3a6f896-9a4f-4cf7-a7b7-688a73741c6c', 'INV-1bc88fbb-2aa', 'pat-006', NULL, 440000, 0.00, 1776624934, 'paid', 1774032934, 1774032934, 'e51720c8-43f6-4d01-8c36-d0bb0186d0a7', 400000, 40000, 1774032934, 1774032934, 'Invoice untuk pembayaran konsultasi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `invoice_id` char(36) NOT NULL,
  `amount` bigint(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `method` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `reference_number` longtext DEFAULT NULL,
  `transaction_date` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `payment_id` char(36) NOT NULL,
  `proof_url` text DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `refunds`
--

CREATE TABLE `refunds` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `payment_id` char(36) NOT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_id` (`invoice_id`),
  ADD KEY `idx_status` (`status`(768));

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_invoices_invoice_number` (`invoice_number`) USING HASH,
  ADD KEY `idx_invoice_number` (`invoice_number`(768)),
  ADD KEY `idx_patient_id` (`patient_id`(768)),
  ADD KEY `idx_status` (`status`(768));

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_invoice_id` (`invoice_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_status` (`status`(768));

--
-- Indeks untuk tabel `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`);

--
-- Indeks untuk tabel `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_status` (`status`);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD CONSTRAINT `insurance_claims_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD CONSTRAINT `payment_proofs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
