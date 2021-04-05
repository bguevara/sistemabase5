-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 04-04-2021 a las 22:45:41
-- Versión del servidor: 10.5.8-MariaDB-1:10.5.8+maria~stretch
-- Versión de PHP: 7.3.26-1+0~20210112.74+debian9~1.gbpd78724

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdtest5`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `sector_id`, `name`, `phone`, `email`) VALUES
(3, 1, 'venetis', '04245438882', 'boris.guevara@gmail.com'),
(4, 1, 'Gvsoluciones', '04245438882', 'gvsolucionesinf@gmail.com'),
(5, 1, 'test', '04245438882', 'test@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectors`
--

CREATE TABLE `sectors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sectors`
--

INSERT INTO `sectors` (`id`, `name`) VALUES
(1, 'Sector 1'),
(4, 'Sector 2'),
(5, 'Sector 3'),
(6, 'Sector 5'),
(7, 'Sector 4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postalcode` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_conexion` datetime NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `name`, `lastname`, `business_name`, `address`, `postalcode`, `phone`, `last_conexion`, `active`) VALUES
(1, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$WwvQD0Es7qSF9hAv6iUpgw$uFSbMYFM2Prl+K4fKBEUXLHiKL+OUldOe8t4p4X2gis', 'Boris', 'Guevara', 'VENETIS', 'Caracas', '3000', '283923123', '0000-00-00 00:00:00', 1),
(2, 'client@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$WwvQD0Es7qSF9hAv6iUpgw$uFSbMYFM2Prl+K4fKBEUXLHiKL+OUldOe8t4p4X2gis', 'cliente1', '.', 'cliente 1', 'Caracas', '3000', '4342343243', '0000-00-00 00:00:00', 1),
(3, 'client2@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$WwvQD0Es7qSF9hAv6iUpgw$uFSbMYFM2Prl+K4fKBEUXLHiKL+OUldOe8t4p4X2gis', 'Cliente 2', '.', 'Cliente 2', 'Caracas', '3000', '32312312321', '0000-00-00 00:00:00', 1),
(5, 'client4@gmail.com', '[\"ROLE_CLIENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$1f/RhKAIyKrGVK2+/l3q6A$84+iiXCgK6ip+980qpm/q+S/+ioKwGGxdL3YjbzjcRM', 'cliente 4', 'fdd', 'Venetis', 'Caracas', '3000', '321312312321', '2021-04-04 20:52:33', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_sector`
--

CREATE TABLE `users_sector` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `sectors_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users_sector`
--

INSERT INTO `users_sector` (`id`, `users_id`, `sectors_id`) VALUES
(1, 2, 1),
(2, 2, 4),
(3, 3, 5),
(4, 3, 7);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8244AA3ADE95C867` (`sector_id`);

--
-- Indices de la tabla `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`);

--
-- Indices de la tabla `users_sector`
--
ALTER TABLE `users_sector`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4444577867B3B43D` (`users_id`),
  ADD KEY `IDX_444457783479DC16` (`sectors_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sectors`
--
ALTER TABLE `sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users_sector`
--
ALTER TABLE `users_sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `FK_8244AA3ADE95C867` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`);

--
-- Filtros para la tabla `users_sector`
--
ALTER TABLE `users_sector`
  ADD CONSTRAINT `FK_444457783479DC16` FOREIGN KEY (`sectors_id`) REFERENCES `sectors` (`id`),
  ADD CONSTRAINT `FK_4444577867B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
