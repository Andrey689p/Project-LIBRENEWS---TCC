-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 04:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `librenews`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrador`
--

CREATE TABLE `administrador` (
  `Idadministrador` int(11) NOT NULL,
  `Idconta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administrador`
--

INSERT INTO `administrador` (`Idadministrador`, `Idconta`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `candidato`
--

CREATE TABLE `candidato` (
  `Idcandidato` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `experiencia` text NOT NULL,
  `portfolio` varchar(300) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente',
  `datacandidatura` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `Idcategoria` int(11) NOT NULL,
  `nomecategoria` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categoria`
--

INSERT INTO `categoria` (`Idcategoria`, `nomecategoria`, `descricao`) VALUES
(1, 'Desenvolvimento', 'Notícias sobre desenvolvimento web, mobile e software'),
(2, 'Programação', 'Linguagens de programação, frameworks e boas práticas'),
(3, 'Tecnologia', 'Hardware, gadgets e inovações tecnológicas'),
(4, 'Sistemas', 'DevOps, infraestrutura e administração de sistemas'),
(5, 'Mercado', 'Tendências de mercado, carreiras e oportunidades em TI');

-- --------------------------------------------------------

--
-- Table structure for table `conta`
--

CREATE TABLE `conta` (
  `Idconta` int(11) NOT NULL,
  `nomeusuario` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `datacriacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conta`
--

INSERT INTO `conta` (`Idconta`, `nomeusuario`, `email`, `senha`, `datacriacao`) VALUES
(1, 'Administrador', 'admin@librenews.com.br', 'admin123', '2025-11-26 00:28:52'),
(2, 'João Silva', 'escritor@librenews.com.br', 'escritor123', '2025-11-26 00:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `escritor`
--

CREATE TABLE `escritor` (
  `Idescritor` int(11) NOT NULL,
  `Idconta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `escritor`
--

INSERT INTO `escritor` (`Idescritor`, `Idconta`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `noticia`
--

CREATE TABLE `noticia` (
  `Idnoticia` int(11) NOT NULL,
  `Idcategoria` int(11) NOT NULL,
  `Idescritor` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `conteudo` text NOT NULL,
  `imagem` varchar(300) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente',
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `motivorejeicao` text DEFAULT NULL,
  `Idadministrador` int(11) DEFAULT NULL,
  `datapublicacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`Idadministrador`),
  ADD UNIQUE KEY `Idconta` (`Idconta`);

--
-- Indexes for table `candidato`
--
ALTER TABLE `candidato`
  ADD PRIMARY KEY (`Idcandidato`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`Idcategoria`);

--
-- Indexes for table `conta`
--
ALTER TABLE `conta`
  ADD PRIMARY KEY (`Idconta`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `escritor`
--
ALTER TABLE `escritor`
  ADD PRIMARY KEY (`Idescritor`),
  ADD UNIQUE KEY `Idconta` (`Idconta`);

--
-- Indexes for table `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`Idnoticia`),
  ADD KEY `Idcategoria` (`Idcategoria`),
  ADD KEY `Idescritor` (`Idescritor`),
  ADD KEY `Idadministrador` (`Idadministrador`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrador`
--
ALTER TABLE `administrador`
  MODIFY `Idadministrador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidato`
--
ALTER TABLE `candidato`
  MODIFY `Idcandidato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `Idcategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `conta`
--
ALTER TABLE `conta`
  MODIFY `Idconta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `escritor`
--
ALTER TABLE `escritor`
  MODIFY `Idescritor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `noticia`
--
ALTER TABLE `noticia`
  MODIFY `Idnoticia` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `administrador_ibfk_1` FOREIGN KEY (`Idconta`) REFERENCES `conta` (`Idconta`) ON DELETE CASCADE;

--
-- Constraints for table `escritor`
--
ALTER TABLE `escritor`
  ADD CONSTRAINT `escritor_ibfk_1` FOREIGN KEY (`Idconta`) REFERENCES `conta` (`Idconta`) ON DELETE CASCADE;

--
-- Constraints for table `noticia`
--
ALTER TABLE `noticia`
  ADD CONSTRAINT `noticia_ibfk_1` FOREIGN KEY (`Idcategoria`) REFERENCES `categoria` (`Idcategoria`),
  ADD CONSTRAINT `noticia_ibfk_2` FOREIGN KEY (`Idescritor`) REFERENCES `escritor` (`Idescritor`) ON DELETE CASCADE,
  ADD CONSTRAINT `noticia_ibfk_3` FOREIGN KEY (`Idadministrador`) REFERENCES `administrador` (`Idadministrador`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
