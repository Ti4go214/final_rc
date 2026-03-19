-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 19, 2026 at 09:09 AM
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
-- Table structure for table `avaliacoes`
--

DROP TABLE IF EXISTS `avaliacoes`;
CREATE TABLE IF NOT EXISTS `avaliacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `local_id` int NOT NULL,
  `utilizador_id` int NOT NULL,
  `classificacao` int NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_avaliacao` (`local_id`,`utilizador_id`)
) ;

--
-- Dumping data for table `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `local_id`, `utilizador_id`, `classificacao`, `criado_em`) VALUES
(1, 1, 3, 4, '2026-03-10 12:21:49'),
(2, 3, 3, 1, '2026-03-10 12:21:55'),
(3, 1, 4, 2, '2026-03-10 12:22:09'),
(4, 3, 4, 1, '2026-03-10 12:22:16');

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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(22, 'Outros', '#7f7f7f', '?'),
(23, 'Shopping', '#abf264', 'SH');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fotos`
--

INSERT INTO `fotos` (`id`, `local_id`, `ficheiro`, `legenda`) VALUES
(1, 3, 'img_3_1772987121.png', NULL),
(2, 5, 'img_5_1773143074.png', NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locais`
--

INSERT INTO `locais` (`id`, `nome`, `categoria_id`, `criado_por`, `pais`, `cidade`, `morada`, `telefone`, `email`, `website`, `descricao`, `latitude`, `longitude`, `criado_em`) VALUES
(1, 'Câmara Municipal de Sesimbra', 13, 1, 'Portugal', 'Sesimbra', 'Rua Amélia Frade, 2970-635 Sesimbra', '212288500', NULL, NULL, 'Edifício da Câmara Municipal de Sesimbra.', 38.44404000, -9.10143000, '2026-02-23 14:28:55'),
(2, 'Bombeiros Voluntários de Sesimbra', 5, 1, 'Portugal', 'Sesimbra', 'Rua 4 de Maio, 2970-657 Sesimbra', '212289130', NULL, NULL, 'Corpo de bombeiros voluntários do concelho de Sesimbra.', 38.44489000, -9.10391000, '2026-02-23 14:28:55'),
(3, 'Escola secundária de sampaio', 18, 3, 'Portugal', 'Sesimbra', 'Escola Secundária de Sampaio, Estrada da Faúlha, Faúlha, Castelo, Sesimbra, Setúbal, 2970-577, Portugal', NULL, NULL, NULL, NULL, 38.47267553, -9.09408328, '2026-03-08 16:25:21'),
(5, 'Casa do TIago', 22, 4, 'Portugal', 'Setúbal', 'Rua Gonçalo Velho Cabral, Choilo, Azeitão (São Lourenço e São Simão), Setúbal, 2925-148, Portugal', NULL, NULL, NULL, NULL, 38.53407999, -9.01675522, '2026-03-10 11:44:34');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`) VALUES
(1, 'Eduardo Pinto', 'eduardo@exemplo.pt', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S', 'admin', '2026-02-23 14:28:55'),
(2, 'Ana Silva', 'ana@exemplo.pt', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S', 'normal', '2026-02-23 14:28:55'),
(3, 'tiago', 'ti.guerra9@gmail.com', '123', 'admin', '2026-02-23 15:48:01'),
(4, 'manel', 'manuelpinheiro@gmail.com', '1234', 'normal', '2026-03-10 11:42:44');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
