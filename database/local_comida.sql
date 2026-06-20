-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-06-2026 a las 23:39:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `local_comida`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clocal`
--

CREATE TABLE `clocal` (
  `id` int(11) NOT NULL,
  `nombre` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clocal`
--

INSERT INTO `clocal` (`id`, `nombre`) VALUES
(12, 'Alfredo'),
(32, 'Ombu'),
(34, 'Ricardo'),
(36, 'Ricardito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comida`
--

CREATE TABLE `comida` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `img` varchar(256) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `clocal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comida`
--

INSERT INTO `comida` (`id`, `nombre`, `img`, `descripcion`, `tipo`, `precio`, `clocal`) VALUES
(1, 'pizza cuatro quesos', 'https://static.bainet.es/clip/cec8a68d-ba08-42ae-a6d8-bc32101352fa_source-aspect-ratio_1600w_0.jpg', '', 2, 13000, 12),
(2, 'choripan', '', '', 23, 9000, 32),
(3, 'Fugazzeta', '', '', 2, 12300, 34),
(6, 'Papas', '', '', 25, 8000, 32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`id`, `nombre`) VALUES
(1, 'Hamburgesa'),
(2, 'Pizza'),
(4, 'Milanesa'),
(21, 'Lomo'),
(23, 'Choripan'),
(25, 'Papas'),
(27, 'Empanada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `admin` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasena`, `admin`) VALUES
(3, 'webadmin', '$2y$10$TPWQmaOTYmdEB8wFd1ZpzukjOIY11Z1U5AhSnLCHgYuVPODjNQwCS', 1),
(4, 'Usuario', '$2y$10$AMRzNx4OzgWM5kl9eE9pfOcWzCjyBAq2adE5W5ALXVTDIoNsIqaha', 0),
(5, 'Usuario1', '$2y$10$OJW4fQae1GKhcQMpTHBStuu70Q6EvHbFOf1UEB1Foyhao/iottOHi', 0),
(6, 'user2', '$2y$10$2PRjK0C9yHISP7MCD68maerucD93iTL9HeVJf/qPFSUERVVDDoDB.', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clocal`
--
ALTER TABLE `clocal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comida`
--
ALTER TABLE `comida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipos` (`tipo`),
  ADD KEY `local` (`clocal`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clocal`
--
ALTER TABLE `clocal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `comida`
--
ALTER TABLE `comida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comida`
--
ALTER TABLE `comida`
  ADD CONSTRAINT `comida_ibfk_1` FOREIGN KEY (`tipo`) REFERENCES `tipos` (`id`),
  ADD CONSTRAINT `comida_ibfk_2` FOREIGN KEY (`clocal`) REFERENCES `clocal` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
