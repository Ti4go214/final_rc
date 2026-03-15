-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 05, 2026 at 11:26 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geo_dados`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cor` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `letras` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `cor`, `letras`) VALUES
(1, 'Aeroporto', '#3498db', 'A'),
(2, 'Autoridade Policial', '#2c3e50', 'AP'),
(3, 'Hospital', '#e74c3c', 'H'),
(4, 'Centro de Saúde', '#e67e22', 'CS'),
(5, 'Bombeiros', '#c0392b', 'B'),
(6, 'Proteção Civil', '#8e44ad', 'PC'),
(7, 'Hotel', '#2ecc71', 'H'),
(8, 'Alojamento Local', '#1abc9c', 'AL'),
(9, 'Restaurante', '#d35400', 'R'),
(10, 'Café / Bar', '#6f4e37', 'CB'),
(11, 'Museu', '#9b59b6', 'M'),
(12, 'Sala de Espetáculos', '#34495e', 'SE'),
(13, 'Câmara Municipal', '#2980b9', 'CM'),
(14, 'Junta de Freguesia', '#16a085', 'JF'),
(15, 'Base Militar', '#7f8c8d', 'BM'),
(16, 'Porto / Marina', '#1f618d', 'PM'),
(17, 'Órgãos de Comunicação Social', '#566573', 'OCS'),
(18, 'Escola', '#f1c40f', 'E'),
(19, 'Universidade', '#273746', 'U'),
(20, 'Farmácia', '#27ae60', 'F'),
(21, 'Supermercado', '#f39c12', 'S'),
(22, 'Outros', '#7f7f7f', '?');

-- --------------------------------------------------------

--
-- Table structure for table `fotos`
--

DROP TABLE IF EXISTS `fotos`;
CREATE TABLE IF NOT EXISTS `fotos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `local_id` int NOT NULL,
  `ficheiro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legenda` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `local_id` (`local_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locais`
--

DROP TABLE IF EXISTS `locais`;
CREATE TABLE IF NOT EXISTS `locais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_id` int NOT NULL,
  `criado_por` int DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `morada` text COLLATE utf8mb4_unicode_ci,
  `telefone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `criado_por` (`criado_por`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locais`
--

INSERT INTO `locais` (`id`, `nome`, `categoria_id`, `criado_por`, `pais`, `cidade`, `morada`, `telefone`, `email`, `website`, `descricao`, `latitude`, `longitude`, `criado_em`) VALUES
(1, 'Câmara Municipal de Sesimbra', 13, 1, 'Portugal', 'Sesimbra', 'Rua Amélia Frade, 2970-635 Sesimbra', '212288500', NULL, NULL, 'Edifício da Câmara Municipal de Sesimbra.', 38.44404000, -9.10143000, '2026-02-23 14:28:55'),
(2, 'Bombeiros Voluntários de Sesimbra', 5, 1, 'Portugal', 'Sesimbra', 'Rua 4 de Maio, 2970-657 Sesimbra', '212289130', NULL, NULL, 'Corpo de bombeiros voluntários do concelho de Sesimbra.', 38.44489000, -9.10391000, '2026-02-23 14:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('admin','normal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`) VALUES
(1, 'Eduardo Pinto', 'eduardo@exemplo.pt', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S', 'admin', '2026-02-23 14:28:55'),
(2, 'Ana Silva', 'ana@exemplo.pt', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S', 'normal', '2026-02-23 14:28:55'),
(3, 'tiago', 'ti.guerra9@gmail.com', '123', 'admin', '2026-02-23 15:48:01');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
