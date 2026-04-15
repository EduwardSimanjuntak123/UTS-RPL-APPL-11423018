-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 15 Apr 2026 pada 11.01
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
-- Database: `meditrack`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `analytics_logs`
--

CREATE TABLE `analytics_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_type` varchar(255) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` varchar(255) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'consultation',
  `location` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `status`, `description`, `notes`, `type`, `location`, `duration`, `created_at`, `updated_at`) VALUES
(1, 5, 3, '2026-04-06 10:22:44', 'scheduled', 'Pemeriksaan rutin', NULL, 'consultation', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(2, 5, 4, '2026-03-31 10:22:44', 'completed', 'Pemeriksaan rutin', NULL, 'general-checkup', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(3, 6, 2, '2026-04-16 10:22:44', 'completed', 'Pemeriksaan rutin', NULL, 'general-checkup', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(4, 6, 4, '2026-04-04 10:22:44', 'completed', 'Pemeriksaan rutin', NULL, 'consultation', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(5, 7, 4, '2026-04-21 10:22:44', 'completed', 'Pemeriksaan rutin', NULL, 'follow-up', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(6, 7, 3, '2026-04-22 10:22:44', 'scheduled', 'Pemeriksaan rutin', NULL, 'general-checkup', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(7, 8, 4, '2026-04-01 10:22:44', 'completed', 'Pemeriksaan rutin', NULL, 'consultation', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(8, 8, 2, '2026-04-14 10:22:45', 'completed', 'Pemeriksaan rutin', NULL, 'general-checkup', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(9, 9, 3, '2026-04-16 10:22:45', 'completed', 'Pemeriksaan rutin', NULL, 'consultation', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(10, 9, 4, '2026-04-13 10:22:45', 'completed', 'Pemeriksaan rutin', NULL, 'consultation', 'Ruang Pemeriksaan A', 30, '2026-03-25 03:22:45', '2026-03-25 03:22:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `drug_stocks`
--

CREATE TABLE `drug_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
  `drug_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `manufacturer` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `drug_stocks`
--

INSERT INTO `drug_stocks` (`id`, `pharmacy_id`, `drug_name`, `quantity`, `unit_price`, `expiry_date`, `manufacturer`, `batch_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'Paracetamol', 415, 15811.00, '2027-03-25 10:22:44', 'PT. Farmasi Indonesia', 'BATCH-69c3b774eb5df', '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(2, 1, 'Ibuprofen', 282, 41557.00, '2027-03-25 10:22:44', 'PT. Farmasi Indonesia', 'BATCH-69c3b774ec292', '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(3, 1, 'Amoxicillin', 283, 6780.00, '2027-03-25 10:22:44', 'PT. Farmasi Indonesia', 'BATCH-69c3b774ecdaa', '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(4, 1, 'Metformin', 283, 22417.00, '2027-03-25 10:22:44', 'PT. Farmasi Indonesia', 'BATCH-69c3b774edc5f', '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(5, 1, 'Lisinopril', 301, 23549.00, '2027-03-25 10:22:44', 'PT. Farmasi Indonesia', 'BATCH-69c3b774ee4c4', '2026-03-25 03:22:44', '2026-03-25 03:22:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `insurance_claims`
--

CREATE TABLE `insurance_claims` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `insurance_provider` varchar(255) NOT NULL,
  `policy_number` varchar(255) NOT NULL,
  `claim_amount` decimal(10,2) NOT NULL,
  `approved_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `submission_date` datetime NOT NULL,
  `approval_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_records`
--

CREATE TABLE `medical_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `diagnosis` text NOT NULL,
  `treatment` text DEFAULT NULL,
  `lab_results` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `follow_up_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `diagnosis`, `treatment`, `lab_results`, `medications`, `follow_up_date`, `notes`, `created_at`, `updated_at`, `doctor_id`, `appointment_id`) VALUES
(1, 5, 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 4, 2),
(2, 6, 'Demam Biasa', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 2, 3),
(3, 6, 'Kolesterol Tinggi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 4, 4),
(4, 7, 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 4, 5),
(5, 8, 'Kolesterol Tinggi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 4, 7),
(6, 8, 'Kolesterol Tinggi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 2, 8),
(7, 9, 'Kolesterol Tinggi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 3, 9),
(8, 9, 'Kolesterol Tinggi', 'Resep obat dan istirahat', 'Normal', 'Paracetamol 500mg', '2026-04-01 10:22:45', 'Pasien diminta kontrol ulang setelah 1 minggu', '2026-03-25 03:22:45', '2026-03-25 03:22:45', 4, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_25_091935_create_appointments_table', 1),
(5, '2026_03_25_092057_create_medical_records_table', 1),
(6, '2026_03_25_092130_create_payments_table', 1),
(7, '2026_03_25_093000_add_columns_to_users_table', 1),
(8, '2026_03_25_093100_add_columns_to_appointments_table', 1),
(9, '2026_03_25_093200_add_columns_to_medical_records_table', 1),
(10, '2026_03_25_093300_add_columns_to_payments_table', 1),
(11, '2026_03_25_093400_create_prescriptions_table', 1),
(12, '2026_03_25_093500_create_pharmacies_table', 1),
(13, '2026_03_25_093600_create_drug_stock_table', 1),
(14, '2026_03_25_093700_create_prescription_orders_table', 1),
(15, '2026_03_25_093800_create_insurance_claims_table', 1),
(16, '2026_03_25_093900_create_analytics_logs_table', 1),
(17, '2026_03_25_093910_add_insurance_claim_foreign_key_to_payments_table', 1),
(18, '2026_03_25_093920_add_pharmacy_foreign_key_to_prescriptions_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `status` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `insurance_claim_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pharmacies`
--

CREATE TABLE `pharmacies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pharmacies`
--

INSERT INTO `pharmacies` (`id`, `name`, `address`, `phone`, `email`, `license_number`, `manager_id`, `status`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'Apotek Sehat Sejahtera', 'Jl. Merdeka No. 123, Jakarta', '02142134213', 'apotek@meditrack.com', 'APT-001-2024', 10, 'active', -6.20880000, 106.84560000, '2026-03-25 03:22:44', '2026-03-25 03:22:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medication` varchar(255) NOT NULL,
  `dosage` varchar(255) NOT NULL,
  `frequency` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `instructions` text DEFAULT NULL,
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `issue_date` datetime NOT NULL,
  `expiry_date` datetime NOT NULL,
  `pharmacy_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `medication`, `dosage`, `frequency`, `duration`, `instructions`, `status`, `issue_date`, `expiry_date`, `pharmacy_id`, `created_at`, `updated_at`) VALUES
(1, 5, 4, 2, 'Paracetamol', '1 tablet', '2x sehari', 12, 'Diminum setelah makan', 'active', '2026-03-25 10:22:45', '2026-06-25 10:22:45', 1, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(2, 6, 2, 3, 'Lisinopril', '1 tablet', '2x sehari', 5, 'Diminum setelah makan', 'active', '2026-03-25 10:22:45', '2026-06-25 10:22:45', 1, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(3, 6, 4, 4, 'Ibuprofen', '1 tablet', '2x sehari', 14, 'Diminum setelah makan', 'active', '2026-03-25 10:22:45', '2026-06-25 10:22:45', 1, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(4, 7, 4, 5, 'Amoxicillin', '1 tablet', '2x sehari', 13, 'Diminum setelah makan', 'active', '2026-03-25 10:22:45', '2026-06-25 10:22:45', 1, '2026-03-25 03:22:45', '2026-03-25 03:22:45'),
(5, 8, 4, 7, 'Metformin', '1 tablet', '2x sehari', 13, 'Diminum setelah makan', 'active', '2026-03-25 10:22:45', '2026-06-25 10:22:45', 1, '2026-03-25 03:22:45', '2026-03-25 03:22:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prescription_orders`
--

CREATE TABLE `prescription_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prescription_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `pickup_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('6WExlrQwVY6qjTcVzzqpaeeU2TtEftpsasuqNBI9', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMlMxbmluTjVPQjNzSUV3Ujlad2laSEVDaUFQQWpieEVEcGk3dVRrcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zZXR0aW5ncyI7czo1OiJyb3V0ZSI7czoxNDoiYWRtaW4uc2V0dGluZ3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1776242165),
('nbtswnEPyEaeMi6p88afxXpIU5dMtJ7xIMgJ7cEE', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMDFGUXgzeE1PdWdkNDVkVXo1c3ZzU0wxbDM3bzVZNW9qOURiWmNiUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb2N0b3IvcHJlc2NyaXB0aW9ucyI7czo1OiJyb3V0ZSI7czoyMDoiZG9jdG9yLnByZXNjcmlwdGlvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1774456746),
('zDrUlR8GXS7PEPnm3Y2SP7AVhArtpRJ0Rgff00Fu', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVnJPa0MwT1BZUHBITnRNaEcwSjZGU3lHa3M0dFRlTWRZVGgzWEVheSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXRpZW50L21lZGljYWwtcmVjb3JkcyI7czo1OiJyb3V0ZSI7czoyMzoicGF0aWVudC5tZWRpY2FsLXJlY29yZHMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1O30=', 1774457692);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `insurance_provider` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'patient',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `specialty`, `license_number`, `status`, `insurance_provider`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin MediTrack', 'admin@meditrack.com', '08123456789', NULL, NULL, NULL, 'active', NULL, NULL, '$2y$12$Jptif7DqACNWfO5yCzsuUecjV5M2VgYlDDZxoaFRIcE/k0RPSRPry', 'admin', NULL, '2026-03-25 03:22:42', '2026-03-25 03:22:42'),
(2, 'Dr. Ahmad edo', 'doctor1@meditrack.com', '08123456781', NULL, 'Umum', 'LIC-DOC-0001', 'active', NULL, NULL, '$2y$12$lKZ6rFnKUGt0QhkWWXIOw.qgAhk9kb8a07t2jH/8CxW.wukqQHwgK', 'doctor', NULL, '2026-03-25 03:22:42', '2026-03-25 09:54:17'),
(3, 'Dr. Budi', 'doctor2@meditrack.com', '08123456782', NULL, 'Gigi', 'LIC-DOC-0002', 'active', NULL, NULL, '$2y$12$8/L3CiK1L8NXMuulNpm6M.FKhevpoadR0tmkxmazjBMPmV.9/dMDm', 'doctor', NULL, '2026-03-25 03:22:42', '2026-03-25 03:22:42'),
(4, 'Dr. Citra', 'doctor3@meditrack.com', '08123456783', NULL, 'Jantung', 'LIC-DOC-0003', 'active', NULL, NULL, '$2y$12$VaQR1qQRaRvgvbGcEVhoP.pCRthFnO1qy2nO7MzAQMXS/IpCmd3CW', 'doctor', NULL, '2026-03-25 03:22:43', '2026-03-25 03:22:43'),
(5, 'Patient 1', 'patient1@meditrack.com', '08987654321', 'Jl. Kesehatan No. 1, Jakarta', NULL, NULL, 'active', 'BPJS', NULL, '$2y$12$cCYSFYTdpHF139RQ4oXlu.9jQYCLSjtvRlQtTODRB65WAGHikywxO', 'patient', NULL, '2026-03-25 03:22:43', '2026-03-25 03:22:43'),
(6, 'Patient 2', 'patient2@meditrack.com', '08987654322', 'Jl. Kesehatan No. 2, Jakarta', NULL, NULL, 'active', 'BPJS', NULL, '$2y$12$mEoLZ4upjWUrQlaKszwubOSoel7qHGWdbuDMKmpNP.pGHQiPT3DBG', 'patient', NULL, '2026-03-25 03:22:43', '2026-03-25 03:22:43'),
(7, 'Patient 3', 'patient3@meditrack.com', '08987654323', 'Jl. Kesehatan No. 3, Jakarta', NULL, NULL, 'active', 'BPJS', NULL, '$2y$12$HrLB19MxaYndRYlzZ/T11eFsn5YGBd.149sizdKYlR.f4M4pU0GcK', 'patient', NULL, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(8, 'Patient 4', 'patient4@meditrack.com', '08987654324', 'Jl. Kesehatan No. 4, Jakarta', NULL, NULL, 'active', 'BPJS', NULL, '$2y$12$p.OCuqUKmPY24WitcOBm4OH7/Oo1Bzo6dKNNuzly2k8wdgEpDL27e', 'patient', NULL, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(9, 'Patient 5', 'patient5@meditrack.com', '08987654325', 'Jl. Kesehatan No. 5, Jakarta', NULL, NULL, 'active', 'BPJS', NULL, '$2y$12$wDfWm9KK4vcI84ybzEQ.nupzzqA7dg8GvJZiZB.VM0n5LEQPo4BXO', 'patient', NULL, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(10, 'Pharmacist Rina', 'pharmacist@meditrack.com', '08111111111', NULL, NULL, NULL, 'active', NULL, NULL, '$2y$12$euSJQn8UN0UH87i9OCSfje43dXgeLsR0SI5UYUfgrSQ9bxSx4939a', 'pharmacist', NULL, '2026-03-25 03:22:44', '2026-03-25 03:22:44'),
(11, 'Ninja 4', 'docter@meditrack.com', '081365438970', NULL, NULL, NULL, 'active', NULL, NULL, '$2y$12$9csXJLA3nsilEprOm53gYOF7hbDIidigFtIv6H8dum8k7gWZC5leq', 'doctor', NULL, '2026-03-25 03:34:12', '2026-03-25 03:34:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_logs_user_id_foreign` (`user_id`),
  ADD KEY `analytics_logs_event_type_index` (`event_type`),
  ADD KEY `analytics_logs_entity_type_index` (`entity_type`),
  ADD KEY `analytics_logs_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_patient_id_foreign` (`patient_id`),
  ADD KEY `appointments_doctor_id_foreign` (`doctor_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `drug_stocks`
--
ALTER TABLE `drug_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drug_stocks_pharmacy_id_foreign` (`pharmacy_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insurance_claims_patient_id_foreign` (`patient_id`),
  ADD KEY `insurance_claims_appointment_id_foreign` (`appointment_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medical_records_patient_id_foreign` (`patient_id`),
  ADD KEY `medical_records_doctor_id_foreign` (`doctor_id`),
  ADD KEY `medical_records_appointment_id_foreign` (`appointment_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_appointment_id_foreign` (`appointment_id`),
  ADD KEY `payments_patient_id_foreign` (`patient_id`),
  ADD KEY `payments_insurance_claim_id_foreign` (`insurance_claim_id`);

--
-- Indeks untuk tabel `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pharmacies_email_unique` (`email`),
  ADD UNIQUE KEY `pharmacies_license_number_unique` (`license_number`),
  ADD KEY `pharmacies_manager_id_foreign` (`manager_id`);

--
-- Indeks untuk tabel `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescriptions_patient_id_foreign` (`patient_id`),
  ADD KEY `prescriptions_doctor_id_foreign` (`doctor_id`),
  ADD KEY `prescriptions_appointment_id_foreign` (`appointment_id`),
  ADD KEY `prescriptions_pharmacy_id_foreign` (`pharmacy_id`);

--
-- Indeks untuk tabel `prescription_orders`
--
ALTER TABLE `prescription_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_orders_prescription_id_foreign` (`prescription_id`),
  ADD KEY `prescription_orders_pharmacy_id_foreign` (`pharmacy_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_license_number_unique` (`license_number`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `analytics_logs`
--
ALTER TABLE `analytics_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `drug_stocks`
--
ALTER TABLE `drug_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `insurance_claims`
--
ALTER TABLE `insurance_claims`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `prescription_orders`
--
ALTER TABLE `prescription_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD CONSTRAINT `analytics_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `drug_stocks`
--
ALTER TABLE `drug_stocks`
  ADD CONSTRAINT `drug_stocks_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD CONSTRAINT `insurance_claims_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `insurance_claims_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `medical_records_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `medical_records_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `payments_insurance_claim_id_foreign` FOREIGN KEY (`insurance_claim_id`) REFERENCES `insurance_claims` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD CONSTRAINT `pharmacies_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `prescriptions_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prescriptions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prescriptions_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prescription_orders`
--
ALTER TABLE `prescription_orders`
  ADD CONSTRAINT `prescription_orders_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`id`),
  ADD CONSTRAINT `prescription_orders_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
