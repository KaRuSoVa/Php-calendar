-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 07 Haz 2023, 14:21:36
-- Sunucu sürümü: 10.4.27-MariaDB
-- PHP Sürümü: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `offer`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(7) DEFAULT NULL,
  `start` datetime NOT NULL,
  `starttime` time NOT NULL,
  `end` datetime DEFAULT NULL,
  `endtime` time NOT NULL,
  `writtenby` varchar(255) NOT NULL,
  `responsible_persons` varchar(255) NOT NULL,
  `email_addresses` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `color`, `start`, `starttime`, `end`, `endtime`, `writtenby`, `responsible_persons`, `email_addresses`, `updated_by`, `file_path`) VALUES
(129, 'de', 'de', '#0071c5', '2023-04-04 00:00:00', '05:04:00', '2023-04-05 00:00:00', '00:00:00', 'Gokhan Basturk', '', '', 'Gokhan Basturk', ''),
(130, 'dede', 'dede', '#0071c5', '2023-04-28 12:00:00', '02:02:00', '2023-04-28 14:00:00', '05:05:00', 'Gokhan Basturk', '', '', 'Gokhan Basturk', ''),
(131, 'DE', 'DE', '#0071c5', '2023-04-28 17:00:00', '00:00:00', '2023-04-29 00:00:00', '00:00:00', 'Gokhan Basturk', '', '', 'Gokhan Basturk', ''),
(132, 'de', 'de', '#ff00dd', '2023-05-02 15:00:00', '00:00:00', '2023-05-02 16:00:00', '00:00:00', 'Gokhan Basturk', '', '', 'Gokhan Basturk', ''),
(134, 'de2', 'de', '#cd8929', '2023-05-31 00:00:00', '00:00:00', '2023-06-01 00:00:00', '00:00:00', 'Gokhan Basturk', 'Patricia Raimondi', 'gokhan.basturk@safe-feurst.fr', 'Gokhan Basturk', 'uploads/logo_1683028499.png'),
(139, 'zszszs', 'szszszsz', '#ff0033', '2023-05-10 01:00:00', '00:00:00', '2023-05-11 00:00:00', '00:00:00', 'Gokhan Basturk', '', '', 'Gokhan Basturk', '');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('normal','admin') NOT NULL DEFAULT 'normal'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'Gokhan Basturk', 'gokhanbasturk12@gmail.com', '22300e980333caebc2688338b8c47b7e', 'admin'),
(3, ' Lena Sonnaly', 'lena.sonnaly@safe-feurst.fr', '$2y$10$KzQslhRN3e/10O6TH/pKSOGSiYoin00vNTVuefknQAGE.TPyZlQ86', 'normal'),
(4, 'Patricia Raimondi', 'gokhan.basturk@safe-feurst.fr', '22300e980333caebc2688338b8c47b7e', 'admin');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
