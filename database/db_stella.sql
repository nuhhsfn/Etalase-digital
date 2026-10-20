-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 08:46 AM
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
-- Database: `db_stella`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_project` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `bahasa` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link_project` varchar(255) NOT NULL,
  `link_demo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `nama_project`, `deskripsi`, `bahasa`, `gambar`, `link_project`, `link_demo`, `created_at`) VALUES
(3, 3, 'web add', 'dsdaa\r\nUntuk memudahkan Anda, berikut adalah full code lengkap untuk navbar yang menggunakan foto logo (lingkaran) serta tombol aksi di sebelah kanan yang diberi warna merah tanpa mengubah bentuk aslinya (btn-pill-accent btn-sm).', 'Website - HTML, CSS, JavaScript', '1792333581_6ad4d70d5429f.png', 'https://github.com/nuhhsfn/tugas_portofolio.pak-ikhwan', 'https://hasifdevv.netlify.app/', '2026-10-18 14:26:21'),
(4, 4, 'website', 'Untuk membuat garis mengkilapnya terlihat lebih tebal dan mencolok, Anda bisa menaikkan ukuran piksel pada properti border dan memperbesar radius pancaran pada box-shadow-nya.', NULL, '1792334732_6ad4db8cbf1d7.png', 'https://github.com/', 'https://hasifdevv.netlify.app/', '2026-10-18 14:45:32'),
(5, 3, 'AI - python', 'dfghgjsdfghjkcyguvhvih', 'Game 2D - JavaScript, HTML5 Canvas', '1792465255_6ad6d967cea6d.png', 'https://github.com/', 'https://hasifdevv.netlify.app/', '2026-10-20 03:00:55'),
(6, 3, 'web add', 'halooo dekkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk', 'website - html -css dan javascript', '1792478637_6ad70dadb8458.png', 'https://github.com/nuhhsfn/tugas_portofolio.pak-ikhwan', '', '2026-10-20 06:43:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','siswa') DEFAULT 'siswa',
  `foto` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `foto`) VALUES
(1, 'Super Admin Stella', 'superadmin@stella.sch.id', '$2y$10$k.jvkFXDD5SuvuuUivkGQeA0kX0pjDZtaOQYoDqVMN0F4rKC0g4dS', 'super_admin', 'default.png'),
(2, 'Admin Stella', 'admin@stella.sch.id', '$2y$10$y//Ho/gveHvJVBwOnSi6MeecPegd8YvQhvxNJOyvHFB5o.UkYW5y.', 'admin', 'default.png'),
(3, 'hasif', 'siswa@stella.sch.id', '$2y$10$K82BOnzYq0/cP4yQ.N792uTjmxn3P/E2.p3Zrn3JA/WXADzo.Oo.q', 'siswa', 'profile_1792377853_6ad583fd932c5.png'),
(4, 'najwa', 'najwa@stella.sch.id', '$2y$10$F0s3G7WmKfwoHzb.CJf03uf.nbWb4PXcNGOdDKC/hMS36g6yn2qdq', 'siswa', 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
