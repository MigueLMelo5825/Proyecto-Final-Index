-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-01-2023 a las 16:43:11
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `index`
--
CREATE DATABASE IF NOT EXISTS `index` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `index`;

-- --------------------------------------------------------

--
-- Estructura de la tabla `libros`
--

CREATE TABLE `libros` (
    `id` VARCHAR(20) PRIMARY KEY,        -- ID único de Google (ej: "zyTCAlS7fS8C")
    `titulo` VARCHAR(255) NOT NULL,
    `subtitulo` VARCHAR(255),
    `autores` TEXT,                      -- Guardaremos un string separado por comas o JSON
    `editorial` VARCHAR(100),
    `fecha_publicacion` VARCHAR(20),     -- Google a veces devuelve solo el año o fecha completa
    `descripcion` TEXT,
    `isbn_10` VARCHAR(10),
    `isbn_13` VARCHAR(13),
    `paginas` INT,
    `categoria` VARCHAR(100),            -- Género principal
    `imagen_url` VARCHAR(255),           -- URL de la portada (thumbnail)
    `idioma` VARCHAR(10),
    `preview_link` VARCHAR(255),
    `fecha_importacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
