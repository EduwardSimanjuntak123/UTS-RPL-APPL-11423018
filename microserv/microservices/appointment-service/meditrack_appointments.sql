-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 15 Apr 2026 pada 11.12
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
-- Database: `meditrack_appointments`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `id` varchar(191) NOT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `appointment_date` bigint(20) DEFAULT NULL,
  `type` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `duration` bigint(20) DEFAULT NULL,
  `location` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `type`, `description`, `status`, `duration`, `location`, `notes`, `created_at`, `updated_at`) VALUES
('01fcd681-49af-4c20-9767-588cf03d2c21', 'pat-005', 'doc-005', 1776291487, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('020b27fd-b403-4a70-b107-855b9894c3a5', 'pat-001', 'doc-001', 1775069719, 'consultation', 'Pemeriksaan rutin', 'scheduled', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('0b9223ff-88d4-4b66-a861-827a34cfaa73', 'pat-003', 'doc-003', 1775242519, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('0ffd08c9-adbb-4079-8848-69ce792fd428', 'pat-005', 'doc-005', 1775415319, 'consultation', 'Pemeriksaan rutin', 'scheduled', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('128e4690-38c9-4cd5-947d-1e7903774d29', 'pat-002', 'doc-002', 1775168287, 'follow-up', 'Pemeriksaan rutin', 'completed', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('2cb5095f-45d2-4721-84c0-afbe26ec40f1', 'pat-004', 'doc-004', 1775341087, 'emergency', 'Pemeriksaan rutin', 'no-show', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('2d34b814-90d9-402a-83c4-4f330b8e892d', 'pat-002', 'doc-002', 1775156119, 'follow-up', 'Pemeriksaan rutin', 'completed', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('2f1df40d-7f28-41e6-8b58-c873700de77d', 'pat-008', 'doc-003', 1775674519, 'emergency', 'Pemeriksaan rutin', 'no-show', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('36315d2a-6bde-4533-a04d-d4d48773f541', 'pat-009', 'doc-004', 1776624919, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('3e74395b-c112-414d-b09d-31f26f5938b6', 'pat-007', 'doc-002', 1776452119, 'consultation', 'Pemeriksaan rutin', 'scheduled', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('476ba4b5-be1b-4b3d-a13a-e4bb2f11cd10', 'pat-001', 'doc-001', 1775081887, 'consultation', 'Pemeriksaan rutin', 'scheduled', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('52adab18-c9e7-4b5e-ac6a-65640dce6899', 'pat-008', 'doc-003', 1776538519, 'follow-up', 'Pemeriksaan rutin', 'completed', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('721c72c3-744c-47f8-a3e2-cea73d8b2837', 'pat-004', 'doc-004', 1776192919, 'follow-up', 'Pemeriksaan rutin', 'completed', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('76196166-9e5c-41be-a95c-b2dea1ce1475', 'pat-005', 'doc-005', 1775427487, 'consultation', 'Pemeriksaan rutin', 'scheduled', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('79283fae-633c-4e29-a00f-1b157579e1bd', 'pat-009', 'doc-004', 1775773087, 'consultation', 'Pemeriksaan rutin', 'scheduled', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('83b911af-c9e7-43c7-9c48-ca59fff7daeb', 'pat-005', 'doc-005', 1776279319, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('87703539-0bb5-4104-92e9-f7bef024bd53', 'pat-007', 'doc-002', 1775588119, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('8ac2e3f9-8815-487d-904f-8d88d80ac785', 'pat-010', 'doc-005', 1775847319, 'follow-up', 'Pemeriksaan rutin', 'completed', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('8d1cf065-613e-43c9-890c-884ad5dc79f2', 'pat-003', 'doc-003', 1776106519, 'consultation', 'Pemeriksaan rutin', 'scheduled', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('91d2e209-22f3-4061-a001-1bcf38bcec13', 'pat-006', 'doc-001', 1776365719, 'emergency', 'Pemeriksaan rutin', 'no-show', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('9857341d-929f-43af-bfb5-2d553ef8fc58', 'pat-009', 'doc-004', 1775760919, 'consultation', 'Pemeriksaan rutin', 'scheduled', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('98f55858-9f4e-4489-8d7a-4ea9a268a4f2', 'pat-010', 'doc-005', 1776723487, 'emergency', 'Pemeriksaan rutin', 'no-show', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('9a46138f-db78-4dfd-8b0e-fe8b2b111035', 'pat-004', 'doc-004', 1775328919, 'emergency', 'Pemeriksaan rutin', 'no-show', 30, 'Ruang Pemeriksaan A', '', 1774464919, 1774464919),
('9c427039-d28a-4497-bc76-60c4aa406401', 'pat-006', 'doc-001', 1775501719, 'follow-up', 'Pemeriksaan rutin', 'completed', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('9ca8fcb8-3324-4d1e-bc38-8b7dae83fb02', 'pat-007', 'doc-002', 1776464287, 'consultation', 'Pemeriksaan rutin', 'scheduled', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('a7e247ea-cf41-4b69-a56e-c21ace7458b1', 'pat-009', 'doc-004', 1776637087, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('ad5d85e2-a7a9-41a8-afe0-140c6d2148a1', 'pat-004', 'doc-004', 1776205087, 'follow-up', 'Pemeriksaan rutin', 'completed', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('b790e455-8954-4841-8fec-d374878fbde1', 'pat-010', 'doc-005', 1776711319, 'emergency', 'Pemeriksaan rutin', 'no-show', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('beb3ef5a-b15c-48c4-b0c4-c094a778a1ec', 'pat-006', 'doc-001', 1775513887, 'follow-up', 'Pemeriksaan rutin', 'completed', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('c5b3be53-689c-4714-97bb-19180e7db3d2', 'pat-008', 'doc-003', 1776550687, 'follow-up', 'Pemeriksaan rutin', 'completed', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('c907d7c9-f52f-42dc-9117-8187cd7ae89e', 'pat-003', 'doc-003', 1776118687, 'consultation', 'Pemeriksaan rutin', 'scheduled', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('c9ff3ab2-764c-48d3-bfe7-17ae403e37b9', 'pat-003', 'doc-003', 1775254687, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('cc7fd3de-8f72-4c66-a60e-c574c294da87', 'pat-007', 'doc-002', 1775600287, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('d2f808f6-177c-430a-8d88-1a3205e60c75', 'pat-001', 'doc-001', 1775945887, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('da5259d4-c221-4d93-b592-7916f4068791', 'pat-002', 'doc-002', 1776020119, 'emergency', 'Pemeriksaan rutin', 'no-show', 60, 'Ruang Pemeriksaan C', '', 1774464919, 1774464919),
('dc6a70a8-5237-4f69-9b3f-a5e669f5e24f', 'pat-002', 'doc-002', 1776032287, 'emergency', 'Pemeriksaan rutin', 'no-show', 60, 'Ruang Pemeriksaan C', '', 1774477087, 1774477087),
('dd2d010c-7a51-447b-a645-d84b54cfd40b', 'pat-008', 'doc-003', 1775686687, 'emergency', 'Pemeriksaan rutin', 'no-show', 45, 'Ruang Pemeriksaan B', '', 1774477087, 1774477087),
('de33d83b-04fc-4aaa-b314-59e7ad01f64a', 'pat-001', 'doc-001', 1775933719, 'general-checkup', 'Pemeriksaan rutin', 'cancelled', 45, 'Ruang Pemeriksaan B', '', 1774464919, 1774464919),
('f8ec6f57-c7e8-4219-811d-65d01f1cccf3', 'pat-006', 'doc-001', 1776377887, 'emergency', 'Pemeriksaan rutin', 'no-show', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087),
('f9b3cf5a-a0a1-4c73-8c14-b131239e0b5b', 'pat-010', 'doc-005', 1775859487, 'follow-up', 'Pemeriksaan rutin', 'completed', 30, 'Ruang Pemeriksaan A', '', 1774477087, 1774477087);

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_records`
--

CREATE TABLE `medical_records` (
  `id` varchar(191) NOT NULL,
  `patient_id` longtext DEFAULT NULL,
  `doctor_id` longtext DEFAULT NULL,
  `appointment_id` longtext DEFAULT NULL,
  `diagnosis` longtext DEFAULT NULL,
  `treatment` longtext DEFAULT NULL,
  `lab_results` longtext DEFAULT NULL,
  `medications` longtext DEFAULT NULL,
  `follow_up_date` bigint(20) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `treatment`, `lab_results`, `medications`, `follow_up_date`, `notes`, `created_at`, `updated_at`) VALUES
('04a98795-ff1f-4105-a03b-bd8f95c36b3a', 'pat-006', 'doc-001', '9c427039-d28a-4497-bc76-60c4aa406401', 'Asma', 'Resep obat dan istirahat', 'Normal', 'Omeprazol 20mg', 1775069719, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774464919, 1774464919),
('1672cc92-855c-4607-bbca-ccca84667721', 'pat-004', 'doc-004', 'ad5d85e2-a7a9-41a8-afe0-140c6d2148a1', 'Asma', 'Resep obat dan istirahat', 'Normal', 'Omeprazol 20mg', 1775081887, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774477087, 1774477087),
('2c9c2a5a-a18f-43d0-bb01-22cda97b1122', 'pat-010', 'doc-005', 'f9b3cf5a-a0a1-4c73-8c14-b131239e0b5b', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775081887, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774477087, 1774477087),
('36dd3793-ef4e-4e83-9cb8-0456c9dcbea4', 'pat-002', 'doc-002', '2d34b814-90d9-402a-83c4-4f330b8e892d', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775069719, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774464919, 1774464919),
('624b1f7e-ddf3-4a15-a39e-1a0498848e91', 'pat-008', 'doc-003', 'c5b3be53-689c-4714-97bb-19180e7db3d2', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775081887, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774477087, 1774477087),
('626b2da2-bcfe-4029-ae6e-f16607c3098c', 'pat-004', 'doc-004', '721c72c3-744c-47f8-a3e2-cea73d8b2837', 'Asma', 'Resep obat dan istirahat', 'Normal', 'Omeprazol 20mg', 1775069719, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774464919, 1774464919),
('8c0ac765-b703-464f-9e6b-ab5d7c402d86', 'pat-010', 'doc-005', '8ac2e3f9-8815-487d-904f-8d88d80ac785', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775069719, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774464919, 1774464919),
('90c6f013-3987-4c8e-83d4-f63c1cddb782', 'pat-008', 'doc-003', '52adab18-c9e7-4b5e-ac6a-65640dce6899', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775069719, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774464919, 1774464919),
('9abd2ef4-8742-4423-80ec-a6eea614b8ed', 'pat-006', 'doc-001', 'beb3ef5a-b15c-48c4-b0c4-c094a778a1ec', 'Asma', 'Resep obat dan istirahat', 'Normal', 'Omeprazol 20mg', 1775081887, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774477087, 1774477087),
('ddaa498b-846f-47a3-8153-a94471dbdfc6', 'pat-002', 'doc-002', '128e4690-38c9-4cd5-947d-1e7903774d29', 'Sakit Gigi', 'Resep obat dan istirahat', 'Normal', 'Ibuprofen 400mg', 1775081887, 'Pasien diminta kontrol ulang setelah 1 minggu', 1774477087, 1774477087);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
