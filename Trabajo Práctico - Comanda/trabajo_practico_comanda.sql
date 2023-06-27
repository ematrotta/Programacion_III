-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 27-06-2023 a las 02:38:57
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `trabajo_practico_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `ID_ENCUESTA` int(11) NOT NULL,
  `ID_MESA` varchar(5) NOT NULL,
  `PUNTUACION_MOZO` int(11) NOT NULL,
  `PUNTUACION_RESTO` int(11) NOT NULL,
  `PUNTUACION_MESA` int(11) NOT NULL,
  `PUNTUACION_COCINERO` int(11) NOT NULL,
  `COMENTARIO` varchar(66) NOT NULL,
  `FECHA_ENCUESTA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `ID` int(11) NOT NULL,
  `ID_MESA` varchar(5) NOT NULL,
  `ID_MOZO` int(11) DEFAULT NULL,
  `TOTAL` decimal(10,2) NOT NULL,
  `NOMBRE_CLIENTE` varchar(30) NOT NULL,
  `ESTADO` varchar(30) NOT NULL,
  `FECHA_APERTURA` datetime NOT NULL,
  `FECHA_CIERRE` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`ID`, `ID_MESA`, `ID_MOZO`, `TOTAL`, `NOMBRE_CLIENTE`, `ESTADO`, `FECHA_APERTURA`, `FECHA_CIERRE`) VALUES
(1, '8b108', 3, '0.00', 'Rogelio', 'cancelado', '2023-06-23 13:54:32', '2023-06-23 13:57:30'),
(2, '6812d', 3, '0.00', 'Ramon', 'cerrada', '2023-06-23 13:58:31', '2023-06-23 14:02:18'),
(3, '747c4', 4, '0.00', 'Fransico', 'cerrada', '2023-06-23 14:22:05', '2023-06-23 16:11:37'),
(4, '94ce3', 8, '9000.00', 'Emanuel', 'cerrada', '2023-06-24 02:06:22', '2023-06-24 02:35:36'),
(5, '259a3', 4, '0.00', 'Elias', 'cerrada', '2023-06-24 03:49:55', '2023-06-24 03:52:30'),
(6, 'ea51e', 4, '0.00', 'Elias', 'cliente esperando pedido', '2023-06-26 20:59:37', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `ID_PEDIDO` int(11) NOT NULL,
  `ID_MESA` varchar(5) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD` int(11) NOT NULL,
  `FECHA_ESTIMADA_FINALIZACION` datetime NOT NULL,
  `FECHA_FINALIZACION` datetime DEFAULT NULL,
  `SECTOR` varchar(30) NOT NULL,
  `ESTADO` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`ID_PEDIDO`, `ID_MESA`, `ID_USUARIO`, `ID_PRODUCTO`, `CANTIDAD`, `FECHA_ESTIMADA_FINALIZACION`, `FECHA_FINALIZACION`, `SECTOR`, `ESTADO`) VALUES
(1, '747c4', 5, 1, 1, '2023-06-23 15:02:05', '2023-06-23 15:40:10', 'cocina', 'listo para servir'),
(2, '747c4', 11, 2, 2, '2023-06-23 15:42:05', '2023-06-24 02:24:19', 'cocina', 'listo para servir'),
(3, '747c4', NULL, 3, 1, '2023-06-23 14:37:05', NULL, 'cerveza', 'pendiente'),
(4, '747c4', 6, 4, 1, '2023-06-23 14:37:05', NULL, 'bar', 'en preparacion'),
(5, '94ce3', 5, 1, 1, '2023-06-24 02:46:22', '2023-06-24 03:47:49', 'cocina', 'listo para servir'),
(6, '94ce3', NULL, 2, 2, '2023-06-24 03:26:22', NULL, 'cocina', 'pendiente'),
(7, '94ce3', NULL, 3, 1, '2023-06-24 02:21:22', NULL, 'cerveza', 'pendiente'),
(8, '94ce3', NULL, 4, 1, '2023-06-24 02:21:22', NULL, 'bar', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_PRODUCTO` int(11) NOT NULL,
  `TITULO` varchar(50) NOT NULL,
  `TIEMPO_PREPARACION` int(11) NOT NULL,
  `PRECIO` decimal(10,2) NOT NULL,
  `ESTADO` varchar(20) NOT NULL,
  `SECTOR` varchar(30) NOT NULL,
  `FECHA_CREACION` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ID_PRODUCTO`, `TITULO`, `TIEMPO_PREPARACION`, `PRECIO`, `ESTADO`, `SECTOR`, `FECHA_CREACION`) VALUES
(1, 'Milanesa a caballo', 40, '3000.00', 'activo', 'cocina', '2023-06-23 14:24:51'),
(2, 'Hamburguesa de Garbanzo', 40, '2000.00', 'activo', 'cocina', '2023-06-23 14:25:09'),
(3, 'Corona', 15, '800.00', 'activo', 'cerveza', '2023-06-23 14:25:28'),
(4, 'Daikiri', 15, '1200.00', 'activo', 'bar', '2023-06-23 14:25:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_ingresos`
--

CREATE TABLE `registro_ingresos` (
  `NRO_REGISTRO` int(11) NOT NULL,
  `FECHA_INGRESO` datetime NOT NULL,
  `ID_USUARIO_INGRESADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_ingresos`
--

INSERT INTO `registro_ingresos` (`NRO_REGISTRO`, `FECHA_INGRESO`, `ID_USUARIO_INGRESADO`) VALUES
(1, '2023-06-21 22:30:02', 1),
(2, '2023-06-21 22:46:14', 1),
(3, '2023-06-21 23:28:59', 7),
(4, '2023-06-21 23:30:05', 4),
(5, '2023-06-23 10:35:38', 4),
(6, '2023-06-23 10:37:30', 3),
(7, '2023-06-23 13:30:06', 7),
(8, '2023-06-23 13:31:46', 3),
(9, '2023-06-23 14:02:01', 4),
(10, '2023-06-23 14:23:36', 3),
(11, '2023-06-23 14:33:07', 6),
(12, '2023-06-23 14:39:02', 5),
(13, '2023-06-23 14:52:27', 5),
(14, '2023-06-23 15:20:01', 5),
(15, '2023-06-23 15:38:25', 4),
(16, '2023-06-23 15:39:54', 5),
(17, '2023-06-23 16:05:35', 3),
(18, '2023-06-23 16:10:12', 4),
(19, '2023-06-23 16:11:19', 3),
(20, '2023-06-23 21:02:29', 3),
(21, '2023-06-23 22:46:25', 3),
(22, '2023-06-24 00:31:07', 3),
(23, '2023-06-24 02:05:03', 8),
(24, '2023-06-24 02:13:11', 6),
(25, '2023-06-24 02:17:21', 3),
(26, '2023-06-24 02:21:40', 11),
(27, '2023-06-24 02:23:24', 5),
(28, '2023-06-24 02:24:02', 11),
(29, '2023-06-24 02:25:14', 4),
(30, '2023-06-24 02:28:30', 3),
(31, '2023-06-24 02:37:25', 4),
(32, '2023-06-24 03:46:34', 5),
(33, '2023-06-24 03:49:22', 4),
(34, '2023-06-24 03:51:09', 3),
(35, '2023-06-26 20:57:59', 3),
(36, '2023-06-26 20:58:35', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_USUARIO` int(11) NOT NULL,
  `NOMBRE` varchar(30) NOT NULL,
  `USER` varchar(30) NOT NULL,
  `PASSWORD` varchar(100) NOT NULL,
  `TIPO` varchar(30) NOT NULL,
  `FECHA_CREACION` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_USUARIO`, `NOMBRE`, `USER`, `PASSWORD`, `TIPO`, `FECHA_CREACION`, `FECHA_BAJA`) VALUES
(1, 'Emanuel', 'etrotta', '$2y$10$ciABC60zHy9J348ek6ZgmO8QNp2idnWInMkUgPv4M24HSeuUmJEhK', 'socio', '2023-06-21 22:25:39', NULL),
(2, 'Tatiana', 'tlagorio', '$2y$10$YB1AAImYa1Y/toHi6cTyD.HijOmo5qrJki5GIZ3bHq0ueQV/OFAj.', 'socio', '2023-06-21 23:09:19', NULL),
(3, 'Luciano', 'ltrotta', '$2y$10$jWLKWQ7hTsztN1Zpi6DuW.LcjWSQMObYeAA8gBN3Fd1a0UZfsmHmS', 'socio', '2023-06-21 23:21:26', NULL),
(4, 'Pablo', 'pmuzella', '$2y$10$1EXsL0Q/LCgjWd.1i5XuGul1M7we2SK74uopXP21G/HQqC76FiH3C', 'mesas', '2023-06-21 23:26:25', NULL),
(5, 'Franco', 'flippi', '$2y$10$CLkeGArzZnKF0./xl2nwE.M6CjiYBV2lLox6jLQaXwuEAZzWVJWgW', 'cocina', '2023-06-21 23:26:55', NULL),
(6, 'Jeremias', 'jparziale', '$2y$10$gvIZTiq2NA9w3qyNc85irurepxKU8AZk5952lbMaY.K7pvCRjZw26', 'bar', '2023-06-21 23:27:27', NULL),
(7, 'Matias', 'mrodriguez', '$2y$10$QhQZEOGOaDfbwlZ3kR4nte8gR5EcV1uPwU64cV5TAqUld.eM2Nrsu', 'cerveza', '2023-06-21 23:28:05', NULL),
(8, 'Pepe', 'pfulano', '$2y$10$DkD/M0iiKQzXziO/1k1DIe2Xh8cYJ4oxHmi4g/NjPEiwUbH35sw9O', 'mesas', '2023-06-23 22:12:23', NULL),
(9, 'Jeremias', 'jparziale', '$2y$10$c7lY.XkuUacyGQ/L4ytpQuYVKvFvw4ibcqPZdHuhCtrqVlsSk689i', 'mesas', '2023-06-23 22:39:29', '2023-06-23 22:49:50'),
(10, 'Elias', 'eltrotta', '$2y$10$v807CqUJlR5Do7vqv.YNuuBYB4hw7tZO7a23CJuvuFiURPRFNQqUu', 'socio', '2023-06-23 22:53:00', NULL),
(11, 'Leo', 'letrotta', '$2y$10$pALrlpFuUy3zwzL7aUfk9O71HzI987edGco9tbSiRr14DN7LILCu2', 'cocina', '2023-06-24 02:20:38', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`ID_ENCUESTA`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`ID_PEDIDO`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_PRODUCTO`);

--
-- Indices de la tabla `registro_ingresos`
--
ALTER TABLE `registro_ingresos`
  ADD PRIMARY KEY (`NRO_REGISTRO`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_USUARIO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `ID_ENCUESTA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `ID_PEDIDO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ID_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `registro_ingresos`
--
ALTER TABLE `registro_ingresos`
  MODIFY `NRO_REGISTRO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
