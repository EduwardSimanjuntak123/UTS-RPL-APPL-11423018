-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 15 Apr 2026 pada 11.07
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
-- Database: `meditrack_medical`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `clinical_notes`
--

CREATE TABLE `clinical_notes` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `medical_record_id` char(36) NOT NULL,
  `note` text NOT NULL,
  `vitals` varchar(255) DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `appointment_id` longtext DEFAULT NULL,
  `note_content` longtext DEFAULT NULL,
  `findings` longtext DEFAULT NULL,
  `assessments` longtext DEFAULT NULL,
  `plans` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lab_results`
--

CREATE TABLE `lab_results` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `medical_record_id` char(36) NOT NULL,
  `test_name` longtext DEFAULT NULL,
  `result` longtext DEFAULT NULL,
  `unit` longtext DEFAULT NULL,
  `reference_range` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `test_type` longtext DEFAULT NULL,
  `test_date` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_records`
--

CREATE TABLE `medical_records` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `patient_id` char(36) NOT NULL,
  `doctor_id` char(36) NOT NULL,
  `diagnosis` text NOT NULL,
  `treatment` text DEFAULT NULL,
  `confidential` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` char(36) NOT NULL COMMENT 'UUID',
  `medical_record_id` char(36) NOT NULL,
  `drug_name` varchar(255) NOT NULL,
  `dosage` longtext DEFAULT NULL,
  `frequency` longtext DEFAULT NULL,
  `duration` bigint(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `instructions` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `appointment_id` longtext DEFAULT NULL,
  `medication` longtext DEFAULT NULL,
  `issue_date` bigint(20) DEFAULT NULL,
  `expiry_date` bigint(20) DEFAULT NULL,
  `pharmacy_id` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `clinical_notes`
--
ALTER TABLE `clinical_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_medical_record_id` (`medical_record_id`);

--
-- Indeks untuk tabel `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_medical_record_id` (`medical_record_id`),
  ADD KEY `idx_status` (`status`(768));

--
-- Indeks untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_medical_record_id` (`medical_record_id`),
  ADD KEY `idx_status` (`status`(768));

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `clinical_notes`
--
ALTER TABLE `clinical_notes`
  ADD CONSTRAINT `clinical_notes_ibfk_1` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `lab_results_ibfk_1` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
