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
-- Database: `meditrack_users`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` varchar(191) NOT NULL,
  `user_id` longtext DEFAULT NULL,
  `role` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `role`, `status`, `created_at`, `updated_at`) VALUES
('0372440f-722f-4e3f-8064-163eddde967a', 'b8741a41-86b3-4bca-a1f6-894ef681b44f', 'super_admin', 'active', 1000000000, 1000000000),
('1a6de0a9-5e99-48ed-930a-fa38428b83f2', '124cba95-ad36-4593-aed5-759c5e3e08bf', 'super_admin', 'active', 1000000000, 1000000000),
('85ef533b-6eee-4e4b-ad95-de64c2302fad', '026e679d-fb79-4407-bbb9-fecf2014d882', 'super_admin', 'active', 1000000000, 1000000000),
('962db1c1-0879-45a3-9425-e50ad81d8f47', '12deaaa4-5ad1-4dcb-83ee-fb2565291097', 'super_admin', 'active', 1000000000, 1000000000),
('b3b1f8bf-947b-4483-aecc-6dd32bac1fce', 'f67ca058-9078-4df2-92dd-308b7f63baf5', 'super_admin', 'active', 1000000000, 1000000000),
('cf249895-38fc-4138-892f-1ba36735404a', '4ef88e6a-6047-494f-b667-0db0bc4c3148', 'super_admin', 'active', 1000000000, 1000000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `resource` varchar(100) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` varchar(191) NOT NULL,
  `name` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `head_doctor_id` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `head_doctor_id`, `status`, `created_at`, `updated_at`) VALUES
('2dc83a24-f0cc-4e8a-a78a-0cc673c1fdaf', 'Umum', 'Praktik Umum', '', 'active', 1000000000, 1000000000),
('953f578a-f82b-48ae-ad37-d1a1d497f57f', 'Neurologi', 'Departemen Saraf', '', 'active', 1000000000, 1000000000),
('a0195821-c124-4970-85d1-e111796073ee', 'Umum', 'Praktik Umum', '', 'active', 1000000000, 1000000000),
('afa45665-a5f4-470c-b37d-77261601123d', 'Neurologi', 'Departemen Saraf', '', 'active', 1000000000, 1000000000),
('cb64a1a5-2da0-43cd-a0ff-9a63e5b8171e', 'Kardiologi', 'Departemen Jantung dan Pembuluh Darah', '', 'active', 1000000000, 1000000000),
('d628bcea-b4ae-4378-bd69-ae5303bff86a', 'Kardiologi', 'Departemen Jantung dan Pembuluh Darah', '', 'active', 1000000000, 1000000000),
('ebc5abbf-75a3-4c1d-a6ae-45b009a9c49a', 'Umum', 'Praktik Umum', '', 'active', 1000000000, 1000000000),
('f34d7aea-ceea-4a7a-a741-fca004dcaa25', 'Neurologi', 'Departemen Saraf', '', 'active', 1000000000, 1000000000),
('fbbf0778-9005-4f88-9956-85fd68fb00a4', 'Kardiologi', 'Departemen Jantung dan Pembuluh Darah', '', 'active', 1000000000, 1000000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `id` varchar(191) NOT NULL,
  `user_id` longtext DEFAULT NULL,
  `license_number` longtext DEFAULT NULL,
  `specialization` longtext DEFAULT NULL,
  `department_id` longtext DEFAULT NULL,
  `experience_years` bigint(20) DEFAULT NULL,
  `consultation_fee` bigint(20) DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `license_number`, `specialization`, `department_id`, `experience_years`, `consultation_fee`, `status`, `created_at`, `updated_at`) VALUES
('581fd060-5a67-422a-9f30-b181dfb6ac95', '8b811880-5435-4268-bd59-e9ad52a9e345', 'DR01001', 'Kardiologi', 'd628bcea-b4ae-4378-bd69-ae5303bff86a', 5, 100000, 'active', 1000000000, 1000000000),
('5c62e9ad-b75b-4654-a65e-a3b7b16496bf', '3b21d3e8-8a23-4c0e-8bcd-3a55bc68c0fb', 'DR01005', 'Bedah', 'afa45665-a5f4-470c-b37d-77261601123d', 9, 300000, 'active', 1000000000, 1000000000),
('8b26c50e-62a3-4327-b890-b64a7481d612', '9917e09f-e483-47f9-9be6-3189c96eccf4', 'DR01004', 'Penyakit Dalam', 'd628bcea-b4ae-4378-bd69-ae5303bff86a', 8, 250000, 'active', 1000000000, 1000000000),
('92733d84-ab9a-4fad-8d7c-9f07ad2ea896', 'f12972db-e8a7-459f-82a7-fe4b7d2dc624', 'DR01002', 'Neurologi', 'afa45665-a5f4-470c-b37d-77261601123d', 6, 150000, 'active', 1000000000, 1000000000),
('b2616b66-77b1-430a-97df-ed33e32c05c6', '4d4556fd-a55b-437d-b41c-300779ffdfdf', 'DR01003', 'Umum', 'a0195821-c124-4970-85d1-e111796073ee', 7, 200000, 'active', 1000000000, 1000000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `patients`
--

CREATE TABLE `patients` (
  `id` varchar(191) NOT NULL,
  `user_id` longtext DEFAULT NULL,
  `medical_number` longtext DEFAULT NULL,
  `date_of_birth` bigint(20) DEFAULT NULL,
  `gender` longtext DEFAULT NULL,
  `blood_type` longtext DEFAULT NULL,
  `emergency_contact` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `medical_number`, `date_of_birth`, `gender`, `blood_type`, `emergency_contact`, `status`, `created_at`, `updated_at`) VALUES
('12ba315a-a469-49b9-9de3-57133fdd66d3', '6ed5dc4f-70d5-426e-9509-7bcdef4d751a', 'MED05002', 935635200, 'F', 'B', '082110000001', 'active', 1000000000, 1000000000),
('28cd9614-e544-4e0a-bbcf-2c1203514743', '54df9f4f-9bad-4988-b238-0d9d3ecc7c64', 'MED05010', 935635200, 'F', 'B', '082110000009', 'active', 1000000000, 1000000000),
('5713fb5c-6d35-4a36-89d9-440aad03228f', '159b9aa3-28e4-4653-9ac1-ebf0551a51d7', 'MED05009', 935635200, 'M', 'A', '082110000008', 'active', 1000000000, 1000000000),
('674b7cf3-f19a-46d9-82d3-6ef94d7a9d2c', '60498999-77de-4110-9c16-872de50a1252', 'MED05003', 935635200, 'M', 'AB', '082110000002', 'active', 1000000000, 1000000000),
('939c84dc-f877-4662-af7e-57dee275ee1c', '1a6c9e90-a0aa-4ff0-bbe2-579dc3244a6a', 'MED05006', 935635200, 'F', 'B', '082110000005', 'active', 1000000000, 1000000000),
('a663e51d-7fe5-458b-8b10-98120bbbc27c', '9ee10af5-6e2a-477e-8999-14e2c23df5da', 'MED05008', 935635200, 'F', 'O', '082110000007', 'active', 1000000000, 1000000000),
('bc0e6d8b-5982-4756-82b9-ea17e012ceb0', 'dff3e15a-7805-4dcc-8872-00da9fe64276', 'MED05001', 935635200, 'M', 'A', '082110000000', 'active', 1000000000, 1000000000),
('db473b51-4c7f-4e1c-a571-a000143bf6ff', 'bf1fa80f-13b7-48cb-9a9a-7810d75d2f5d', 'MED05007', 935635200, 'M', 'AB', '082110000006', 'active', 1000000000, 1000000000),
('ec840d3f-ffbb-4d0e-ab7b-08abd5a54d4f', '011a9640-1a0c-46da-912b-1c19ea2ca713', 'MED05005', 935635200, 'M', 'A', '082110000004', 'active', 1000000000, 1000000000),
('f159d5cd-d6f3-4199-ba9e-27cd3b10390f', 'fc4a3ec0-3b6b-442c-8b26-deb6a21fbf70', 'MED05004', 935635200, 'F', 'O', '082110000003', 'active', 1000000000, 1000000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `resource` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pharmacists`
--

CREATE TABLE `pharmacists` (
  `id` varchar(191) NOT NULL,
  `user_id` longtext DEFAULT NULL,
  `license_number` longtext DEFAULT NULL,
  `pharmacy_id` longtext DEFAULT NULL,
  `experience_years` bigint(20) DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pharmacists`
--

INSERT INTO `pharmacists` (`id`, `user_id`, `license_number`, `pharmacy_id`, `experience_years`, `status`, `created_at`, `updated_at`) VALUES
('3f8d0dfd-9201-46b2-8936-7e754283dc1a', 'a174f2be-e016-43ed-8aae-8ba2c2382249', 'APT08001', '838f2a2f-d927-4dbb-9be2-bc980552a547', 3, 'active', 1000000000, 1000000000),
('739dc730-ec13-400f-940f-f0eb191c8617', '2e0bb0fd-fca6-4a85-a511-3dcdb2c2609b', 'APT08002', '95bd0101-a635-4876-b265-762cd7bec66e', 4, 'active', 1000000000, 1000000000),
('f92f95bf-4675-44cb-9a88-fe636239c2ee', '28948482-e22d-43d9-abce-de565222e93c', 'APT08003', 'ae39d1e4-b803-439d-b30a-d53fd5055f94', 5, 'active', 1000000000, 1000000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` varchar(191) NOT NULL,
  `name` longtext DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `password` longtext DEFAULT NULL,
  `phone` longtext DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `role` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
('00f2c9ff-e6af-4813-979f-d7ec808fffe8', 'Pasien 3', 'pasien3@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567802', 'Jl. Pasien No. 3', 'patient', 'active', 1000000000, 1000000000),
('02bb6363-e661-44f8-add0-f7e07d21159a', 'Pasien 9', 'pasien9@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567808', 'Jl. Pasien No. 9', 'patient', 'active', 1000000000, 1000000000),
('0cd3121c-435d-4cab-abf4-6a323cde12a4', 'Apt. Rudi Gunawan', 'apt.rudi@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567902', 'Jl. Apotek No. 3', 'pharmacist', 'active', 1000000000, 1000000000),
('12deaaa4-5ad1-4dcb-83ee-fb2565291097', 'Admin Utama', 'admin@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567890', 'Jl. Admin No. 1', 'admin', 'active', 1000000000, 1000000000),
('2cc49c11-f0ec-49c1-b52a-873df6f30db1', 'Pasien 10', 'pasien10@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567809', 'Jl. Pasien No. 10', 'patient', 'active', 1000000000, 1000000000),
('397bcb5b-affc-49b9-acd4-9c9d603be652', 'Dr. Siti Nurhaliza', 'dr.siti@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567801', 'Jl. Dokter No. 2', 'doctor', 'active', 1000000000, 1000000000),
('4f1dad35-dc20-4a07-9023-c96a3c59ad41', 'Pasien 6', 'pasien6@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567805', 'Jl. Pasien No. 6', 'patient', 'active', 1000000000, 1000000000),
('5040abd3-9a0c-4a66-8e70-03d7fd95b841', 'Apt. Budi Setiawan', 'apt.budi@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567900', 'Jl. Apotek No. 1', 'pharmacist', 'active', 1000000000, 1000000000),
('52445691-b968-4402-be32-ee2021f0a1ed', 'Pasien 7', 'pasien7@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567806', 'Jl. Pasien No. 7', 'patient', 'active', 1000000000, 1000000000),
('558d5935-0c17-4584-8dd8-1b059de4ee98', 'Pasien 5', 'pasien5@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567804', 'Jl. Pasien No. 5', 'patient', 'active', 1000000000, 1000000000),
('58f36c95-8c0e-4db2-ac5a-f77d02f5f71e', 'Pasien 4', 'pasien4@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567803', 'Jl. Pasien No. 4', 'patient', 'active', 1000000000, 1000000000),
('69a3407e-8a3e-4b3b-b433-e0dd978d4b3e', 'Dr. Budi Hartono', 'dr.budi@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567800', 'Jl. Dokter No. 1', 'doctor', 'active', 1000000000, 1000000000),
('6acd8e9b-07a1-406f-92a3-09a4c081da0d', 'Pasien 1', 'pasien1@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567800', 'Jl. Pasien No. 1', 'patient', 'active', 1000000000, 1000000000),
('6c4be91c-38b2-4166-98ea-f398955f153a', 'Pasien 2', 'pasien2@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567801', 'Jl. Pasien No. 2', 'patient', 'active', 1000000000, 1000000000),
('b55770d4-b9a7-445c-b0cc-220fdef4b8d4', 'Dr. Ahmad Rizki', 'dr.ahmad@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567802', 'Jl. Dokter No. 3', 'doctor', 'active', 1000000000, 1000000000),
('b56c61ce-1677-49ba-9c64-f6bcd0578b9c', 'Dr. Rinto Harahap', 'dr.rinto@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567804', 'Jl. Dokter No. 5', 'doctor', 'active', 1000000000, 1000000000),
('b8741a41-86b3-4bca-a1f6-894ef681b44f', 'Admin Sekunder', 'admin2@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567891', 'Jl. Admin No. 2', 'admin', 'active', 1000000000, 1000000000),
('cef02906-04ab-487c-8a57-b494eb881f6a', 'Pasien 8', 'pasien8@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567807', 'Jl. Pasien No. 8', 'patient', 'active', 1000000000, 1000000000),
('ef5e9546-755f-4aad-a925-5865f7fa560b', 'Dr. Eka Putri', 'dr.eka@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567803', 'Jl. Dokter No. 4', 'doctor', 'active', 1000000000, 1000000000),
('f2cb1fd1-d646-4bd6-a85b-4002ed38baf2', 'Apt. Siti Handayani', 'apt.siti@meditrack.com', '$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO', '081234567901', 'Jl. Apotek No. 2', 'pharmacist', 'active', 1000000000, 1000000000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_doctors_license_number` (`license_number`) USING HASH;

--
-- Indeks untuk tabel `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_patients_medical_number` (`medical_number`) USING HASH;

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `pharmacists`
--
ALTER TABLE `pharmacists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_pharmacists_license_number` (`license_number`) USING HASH;

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_users_email` (`email`) USING HASH;

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
